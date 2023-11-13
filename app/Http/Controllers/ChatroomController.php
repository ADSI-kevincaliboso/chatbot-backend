<?php

namespace App\Http\Controllers;

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
        return Chatroom::all();
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
                'name' => $user->name
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
}
