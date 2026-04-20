<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartConversationRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::where('participant_one_id', $request->user()->id)
            ->orWhere('participant_two_id', $request->user()->id)
            ->with(['participantOne', 'participantTwo', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->latest()
            ->paginate($request->per_page ?? 15);
        return ConversationResource::collection($conversations);
    }

    public function store(StartConversationRequest $request)
    {
        $conversation = Conversation::create([
            'participant_one_id' => $request->user()->id,
            'participant_two_id' => $request->participant_two_id,
        ]);
        
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'message' => $request->initial_message,
        ]);
        
        return new ConversationResource($conversation->load(['participantOne', 'participantTwo', 'messages']));
    }

    public function show(Conversation $conversation)
    {
        // تأكد أن المستخدم مشارك في المحادثة
        if (!in_array(auth()->id(), [$conversation->participant_one_id, $conversation->participant_two_id])) {
            abort(403);
        }
        return new ConversationResource($conversation->load(['participantOne', 'participantTwo', 'messages.sender']));
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation)
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
            'attachment_path' => $request->hasFile('attachment') ? $request->file('attachment')->store('messages', 'public') : null,
        ]);
        return new MessageResource($message);
    }
}