<?php

namespace App\Http\Controllers;

use App\Events\AssignedChatroom;
use App\Events\NewChatRoom;
use App\Http\Requests\AssignChatroomRequest;
use App\Models\Chatroom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Chatroom::where('status', 'active')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->chatroom) {
            return response()->json(['message' => 'Chatroom Info', 'id' => $user->chatroom->id], Response::HTTP_OK);
        }

        try {
            DB::beginTransaction();

            $chatRoom = Chatroom::create([
                'name' => $user->name,
                'owner' => $user->id
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Chatroom created',
                'id' => $chatRoom->id
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
    public function show(Chatroom $chat_room)
    {
        return response()->json([
            'message' => 'Chatroom information',
            'data' => $chat_room
        ], Response::HTTP_OK);
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
    public function destroy(Chatroom $chat_room)
    {
        try {
            DB::beginTransaction();
            $chat_room->delete();
            DB::commit();

            return response()->json([
                'message' => 'Chatroom deleted'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created',
                'details' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAssignedRooms(Request $request) {
        $user = auth()->user();
        return Chatroom::where('moderator', $user->id)->activeRooms()->get();
    }

    public function assignModerator(AssignChatroomRequest $request) {
        $chatroom = Chatroom::find($request->chatroom_id);
        try {
            DB::beginTransaction();
            $chatroom->update([
                'moderator' => $request->moderator_id
            ]);
            DB::commit();

            broadcast(new AssignedChatroom($chatroom, $request->moderator_id));

            return response()->json([
                'message' => 'Chatroom Assigned'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created',
                'details' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
