<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;

class MessageService
{
    public function sendMessage(int $conversationId, int $userId, string $messageText): Message
    {
        $message = Message::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'message' => $messageText,
        ]);

        $message->load('user');

        broadcast(new MessageSent($message));

        $this->cacheConversationMessages($conversationId);

        return $message;
    }


    public function getConversationMessages(int $conversationId)
    {
        return Cache::remember("conversation.{$conversationId}.messages", 300, function () use ($conversationId) {
            return Message::where('conversation_id', '=', $conversationId, 'and')
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();
        });
    }
    protected function cacheConversationMessages(int $conversationId): void
    {
        Cache::forget("conversation.{$conversationId}.messages");
    }
}
