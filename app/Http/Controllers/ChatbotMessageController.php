<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatbotMessageResource;
use App\Http\Resources\ChatbotMessageResourceCollection;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatbotChoice;
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

    public function botInit()
    {
        $message = ChatbotMessage::where('id', 1)->with('choices')->get();

        return response()->json([
            'message' => 'Bot Initialized',
            'data' => $message
        ], Response::HTTP_OK);
    }

    public function getResponse(Request $request)
    {
        $selectedId = $request->selectedId;

        $chatbotMessage = ChatbotMessage::where('id', $selectedId)->with('choices')->get();
        $chat = $chatbotMessage[0];

        return response()->json([
            'message' => 'Bot Response',
            'data' => $chat
        ], Response::HTTP_OK);
    }

    public function getMessages()
    {
        return ChatbotMessage::with('choices')->get();
    }

    public function createIncidentReport(Request $request)
    {
        $messageId = $request->messageId;
        $message = $request->message;

        $user = Auth::user();
        $bot = User::find(2);

        $chatbotMessage = ChatbotMessage::find($messageId);

        try {
            DB::beginTransaction();
            $chat = ChatMessage::create([
                'user_id' => $user->id,
                'chatroom_id' => $user->chatroom->id,
                'message' => $message
            ]);
            DB::commit();

            DB::beginTransaction();
            $chatbot = ChatMessage::create([
                'user_id' => $bot->id,
                'chatroom_id' => $user->chatroom->id,
                'message' => $chatbotMessage->nextPrompt->message
            ]);
            DB::commit();

            $data = [
                'chatMessage' => new ChatMessageResource($chat),
                'chatbotMessage' => new ChatMessageResource($chatbot)
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
        return $chatbotMessage->nextPrompt;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $message = $request->message;
        $user = Auth::user();
        $bot = User::find(2);

        $verify = ChatbotChoice::where('choice', 'LIKE', '%' . $message . '%')->get();

        if ($verify->isEmpty()) {
            return response()->json([
                'message' => 'Please try again'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $chat = ChatMessage::create([
                'user_id' => $user->id,
                'chatroom_id' => $user->chatroom->id,
                'message' => $request->message
            ]);
            DB::commit();

            $botMessage = ChatbotMessage::find($verify[0]->reply_id);

            DB::beginTransaction();
            $chatbot = ChatMessage::create([
                'user_id' => $bot->id,
                'chatroom_id' => $user->chatroom->id,
                'message' => $botMessage->message
            ]);
            DB::commit();

            $chatbot->nextId = $botMessage->nextId;
            $chatbot->chatbotId = $botMessage->id;
            $chatbot->choices = $botMessage->choices;

            $data = [
                'chatMessage' => new ChatMessageResource($chat),
                'chatbotMessage' => new ChatbotMessageResource($chatbot)
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
