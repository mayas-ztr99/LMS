<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class ConversationService
{
    public function createOrGetConversation(int $studentId, int $instructorId): Conversation
    {
        $conversation = Conversation::where('student_id', '=', $studentId)
            ->where('instructor_id', '=', $instructorId)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'student_id' => $studentId,
                'instructor_id' => $instructorId,
            ]);
        }

        Cache::put("conversation.{$conversation->id}", $conversation, 300);

        return $conversation;
    }

    public function getConversationForUser(int $conversationId, int $userId): Conversation
    {
        $conversation = Conversation::with('messages.user')->find($conversationId);

        if (!$conversation) {
            throw new ModelNotFoundException('Conversation not found.');
        }

        // التحقق من أن المستخدم طرف في المحادثة
        if ($conversation->student_id !== $userId && $conversation->instructor_id !== $userId) {
            abort(403, 'Unauthorized action.');
        }

        return $conversation;
    }

    public function getUserConversations(int $userId)
    {
        return Cache::remember("user.{$userId}.conversations", 300, function () use ($userId) {
            return Conversation::where('student_id', '=', $userId)
                ->orWhere('instructor_id', '=', $userId)
                ->with(['student', 'instructor'])
                ->get();
        });
    }
}
