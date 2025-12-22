<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;

class MessageApproved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('public.chat'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'username' => $this->message->username,
            'content' => $this->message->content,
            'status' => $this->message->status,
            'approved_at' => $this->message->approved_at?->toIso8601String(),
            'created_at' => $this->message->created_at?->toIso8601String(),
            'image_path' => $this->message->image_path ? asset($this->message->image_path) : null,
            'display_time' => ($this->message->approved_at ?? $this->message->created_at)
                ->timezone(config('app.timezone'))
                ->format('H:i'),
        ];
    }
}
