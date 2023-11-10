<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatMessagesCollection;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Gets all the messages from the chatroom
     * 
     * @param $roomId
     * @return jsonResponse
     */
    public function messages($roomId)
    {
        $chatMessage = ChatMessage::where('chatroom_id', $roomId)
            ->with('user')
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json([
            'message' => "Chat Messages",
            'chatroom_id' => $roomId,
            'data' => new ChatMessagesCollection($chatMessage)
        ], Response::HTTP_OK);
    }

    /**
     * Create new message for current user logged in
     * 
     * @param {Request $request, $roomId}
     * @return jsonResponse
     */
    public function newMessage(Request $request, $roomId)
    {
        $request->validate([
            'message' => 'required'
        ]);

        $userId = Auth::user()->id;

        try {
            DB::beginTransaction();
            $chat = ChatMessage::create([
                'user_id' => $userId,
                'chatroom_id' => $roomId,
                'message' => $request->message
            ]);
            DB::commit();

            broadcast(new NewChatMessage(new ChatMessageResource($chat)))->toOthers();

            return response()->json([
                'message' => 'Message sent',
                'data' => new ChatMessageResource($chat)
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created',
                'details' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
