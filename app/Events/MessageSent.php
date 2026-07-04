<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // قناة خاصة لكل محادثة
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    /**
     * اسم الحدث الذي سيستمع إليه العميل
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * البيانات المرسلة مع الحدث
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user->name,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }

    /**
     * تحديد أن هذا الحدث سيتم وضعه في الـ Queue
     */
    public function shouldBroadcastNow(): bool
    {
        return false; // سيدخل الـ Queue
    }
}
