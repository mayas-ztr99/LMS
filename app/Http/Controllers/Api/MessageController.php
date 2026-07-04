<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Services\ConversationService;
use App\Services\MessageService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use ApiResponseTrait;

    protected $messageService;
    protected $conversationService;

    public function __construct(
        MessageService $messageService,
        ConversationService $conversationService
    ) {
        $this->messageService = $messageService;
        $this->conversationService = $conversationService;
    }

    public function store(SendMessageRequest $request)
    {
        $user = $request->user();
        $conversationId = $request->conversation_id;

        // التحقق من أن المستخدم طرف في المحادثة
        try {
            $conversation = $this->conversationService->getConversationForUser($conversationId, $user->id);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Unauthorized to send messages in this conversation.',
                403
            );
        }

        // إرسال الرسالة
        $message = $this->messageService->sendMessage(
            $conversationId,
            $user->id,
            $request->message
        );

        return $this->successResponse(
            $message->load('user'),
            'Message sent successfully.',
            201
        );
    }

    public function index($conversationId, Request $request)
    {
        $user = $request->user();

        // التحقق من الصلاحية
        try {
            $this->conversationService->getConversationForUser($conversationId, $user->id);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Unauthorized to view messages in this conversation.',
                403
            );
        }

        $messages = $this->messageService->getConversationMessages($conversationId);

        return $this->successResponse(
            $messages,
            'Messages retrieved successfully.'
        );
    }
}
