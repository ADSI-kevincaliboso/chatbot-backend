<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatbotMessageResource;
use App\Http\Resources\ChatbotMessageResourceCollection;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatbotMessage;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // instead of returning an instance of ChatbotMessage, let's create an entry on chatMessage model and eturn it
        $chatbotMessageId = $request->chatbotMessageId;
        $message = $request->message;
        $user = Auth::user();
        $bot = User::find(2);

        $botMessage = ChatbotMessage::find($chatbotMessageId);

        try {
            DB::beginTransaction();
            $chat = ChatMessage::create([
                'user_id' => $user->id,
                'chatroom_id' => $user->chatroom->id,
                'message' => $request->message
            ]);
            
            DB::commit();

            DB::beginTransaction();
            $chatbotMessage = ChatMessage::create([
                'user_id' => $bot->id,
                'chatroom_id' => $user->chatroom->id,
                'message' => $botMessage->message
            ]);
            
            DB::commit();

            

            $data = [
                "chatbotMessage" => new ChatbotMessageResource(($chatbotMessage)),
                "chatMessage" => new ChatMessageResource($chat)
            ];

            return response()->json([
                'message' => 'Message sent',
                'data' => $data
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created',
                'details' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
