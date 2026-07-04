<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ConversationService;
use App\Services\MessageService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    use ApiResponseTrait;

    protected $conversationService;
    protected $messageService;

    public function __construct(
        ConversationService $conversationService,
        MessageService $messageService
    ) {
        $this->conversationService = $conversationService;
        $this->messageService = $messageService;
    }

    public function store(Request $request)
    {
        $user = $request->user();


        // // منع المحادثة مع النفس
        // if ((int) $instructorId === (int) $user->id) {
        //     return $this->errorResponse('لا يمكنك بدء محادثة مع نفسك.', 422);
        // }
        $instructorId = $request->input('instructor_id');
        $conversation = $this->conversationService->createOrGetConversation(
            $user->id,
            $instructorId
        );

        $messages = $this->messageService->getConversationMessages($conversation->id);

        return $this->successResponse(
            [
                'conversation' => $conversation,
                'messages' => $messages,
            ],
            'Conversation created successfully.',
            201
        );
    }

    public function show($id, Request $request)
    {
        $user = $request->user();
        try {
            $conversation = $this->conversationService->getConversationForUser($id, $user->id);
        } catch (\Exception $e) {
            return $this->errorResponse('Conversation not found ', 403);
        }

        $messages = $this->messageService->getConversationMessages($conversation->id);

        return $this->successResponse(
            [
                'conversation' => $conversation,
                'messages' => $messages,
            ],
            'Conversation retrieved successfully.'
        );
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = $this->conversationService->getUserConversations($user->id);

        return $this->successResponse(
            $conversations,
            'Conversations retrieved succesfully. '
        );
    }
}
