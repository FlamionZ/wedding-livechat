<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\MessageApproved;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class MessageModerationController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'pendingMessages' => Message::pending()->latest()->get(),
            'approvedMessages' => Message::approved()
                ->orderByDesc('created_at')
                ->take(50)
                ->get()
                ->sortBy('created_at')
                ->values(),
        ]);
    }

    public function approve(Message $message, Request $request): RedirectResponse|JsonResponse
    {
        if (! $message->isPending()) {
            return $this->respondAfterAction($request, $message, 'Pesan sudah diproses.');
        }

        $message->forceFill([
            'status' => Message::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ])->save();

        $message->refresh();

        event(new MessageApproved($message));

        return $this->respondAfterAction($request, $message, 'Pesan disetujui.');
    }

    public function reject(Message $message, Request $request): RedirectResponse|JsonResponse
    {
        if (! $message->isPending()) {
            return $this->respondAfterAction($request, $message, 'Pesan sudah diproses.');
        }

        $message->forceFill([
            'status' => Message::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ])->save();

        $message->refresh();

        return $this->respondAfterAction($request, $message, 'Pesan ditolak.');
    }

    private function respondAfterAction(Request $request, Message $message, string $status): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $status,
                'data' => [
                    'id' => $message->id,
                    'status' => $message->status,
                    'username' => $message->username,
                    'content' => $message->content,
                    'approved_at' => $message->approved_at?->toIso8601String(),
                    'created_at' => $message->created_at?->toIso8601String(),
                    'image_path' => $message->image_path ? asset($message->image_path) : null,
                    'display_time' => ($message->approved_at ?? $message->created_at)?->timezone(config('app.timezone'))?->format('H:i'),
                ],
            ]);
        }

        return redirect()->back()->with('status', $status);
    }

    public function downloadMessages(): StreamedResponse
    {
        $filename = 'chat-messages-' . now()->format('Ymd-His') . '.csv';

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'id',
                'username',
                'content',
                'image_path',
                'status',
                'approved_by',
                'approved_at',
                'created_at',
                'updated_at',
            ]);

            Message::orderBy('created_at')
                ->chunk(500, function ($messages) use ($handle) {
                    foreach ($messages as $message) {
                        fputcsv($handle, [
                            $message->id,
                            $message->username,
                            $message->content,
                            $message->image_path,
                            $message->status,
                            $message->approved_by,
                            $message->approved_at?->toIso8601String(),
                            $message->created_at?->toIso8601String(),
                            $message->updated_at?->toIso8601String(),
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function downloadPhotos(): RedirectResponse|BinaryFileResponse
    {
        $messagesWithImages = Message::whereNotNull('image_path')->get();

        if ($messagesWithImages->isEmpty()) {
            return redirect()->back()->with('status', 'Tidak ada foto untuk diunduh.');
        }

        $tempDir = storage_path('app/tmp');
        File::ensureDirectoryExists($tempDir);

        $zipPath = $tempDir . '/chat-photos-' . now()->format('Ymd-His') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('status', 'Gagal membuat arsip foto.');
        }

        foreach ($messagesWithImages as $message) {
            $relative = ltrim(str_replace('storage/', '', (string) $message->image_path), '/');
            $filePath = storage_path('app/public/' . $relative);

            if (! File::exists($filePath)) {
                continue;
            }

            $entryName = 'photos/' . $message->id . '-' . basename($relative);
            $zip->addFile($filePath, $entryName);
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
