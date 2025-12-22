<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManagerStatic as Image;
use App\Events\MessageSubmitted;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function nickname(): View|RedirectResponse
    {
        // Clear session if user explicitly returns to nickname page
        if (request()->has('logout')) {
            session()->forget('nickname');
        }
        
        // If already has nickname and not logging out, redirect to chat
        if (session('nickname') && !request()->has('logout')) {
            return redirect()->route('chat.index');
        }
        
        return view('nickname');
    }

    public function enter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nickname' => ['required', 'string', 'max:50'],
            'disclaimer' => ['accepted'],
        ]);

        // Store nickname in session
        session(['nickname' => $validated['nickname']]);

        return redirect()->route('chat.index');
    }

    public function index(): View|RedirectResponse
    {
        // Check if user has nickname in session
        if (!session('nickname')) {
            return redirect()->route('chat.nickname');
        }

        $messages = Message::approved()
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        // Count pending messages for this user
        $pendingCount = Message::where('username', session('nickname'))
            ->where('status', Message::STATUS_PENDING)
            ->count();

        session(['pending_messages_count' => $pendingCount]);

        return view('chat-new', compact('messages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:80'],
            'content' => ['required', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'], // 10MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = strtolower($image->getClientOriginalExtension());
            $filename = uniqid('chatimg_') . '.' . $ext;

            // Simpan file tanpa kompresi (karena GD belum tersedia di Apache)
            $savePath = 'chat-images/' . $filename;
            // Simpan di disk 'public' supaya muncul di public/storage
            $image->storeAs('chat-images', $filename, 'public');
            $imagePath = 'storage/' . $savePath;
        }

        $message = Message::create([
            'username' => $validated['username'],
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'status' => Message::STATUS_PENDING,
        ]);

        event(new MessageSubmitted($message));

        return back()->with('status', 'Pesan terkirim dan menunggu persetujuan admin.');
    }

    public function logout(): RedirectResponse
    {
        session()->forget('nickname');
        session()->forget('pending_messages_count');
        return redirect()->route('chat.nickname');
    }
}
