<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\MessageApproved;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
}
