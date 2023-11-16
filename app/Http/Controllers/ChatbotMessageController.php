<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatbotMessageResource;
use App\Http\Resources\ChatbotMessageResourceCollection;
use App\Models\ChatbotMessage;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatbotMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new ChatbotMessageResourceCollection(ChatbotMessage::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $chatbotMessageId = $request->chatbotMessageId;

        $chatbotMessage = ChatbotMessage::find($chatbotMessageId);

        return new ChatbotMessageResource(($chatbotMessage));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ChatbotMessage $chatbot_message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
