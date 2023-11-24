<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatMessageResource;
use App\Models\ChatbotChoice;
use App\Models\ChatMessage;
use App\Services\ChatMessageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatbotChoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ChatbotChoice::with('reply')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatbotChoice $chatbot_choice)
    {
        // create new chatmessage here
        $user = Auth::user();
        $chatService = new ChatMessageService();

        try {
            DB::beginTransaction();
            $chat = $chatService->createRecord($user->id, $user->chatroom->id, $chatbot_choice->choice);
            DB::commit();

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
