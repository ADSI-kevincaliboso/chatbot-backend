<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResourceCollection;
use App\Models\ChatMessage;
use App\Models\Chatroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new UserResourceCollection(User::users()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();

        if ($request->has('user_type')) {
            $data['user_type'] = $request->user_type;
        }
        try {
            DB::beginTransaction();
            $user = User::create($data);
            DB::commit();

            return response()->json([
                'message' => 'Registered successfully',
                'data' => $user
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
    public function destroy(User $user)
    {
        try {
            if ($user->user_type == 'user' && $user->chatroom) {
                DB::beginTransaction();
                ChatMessage::where('chatroom_id', $user->chatroom->id)->delete();
                DB::commit();
    
                DB::beginTransaction();
                Chatroom::where('id', $user->chatroom->id)->delete();
                DB::commit();
            }

            DB::beginTransaction();
            $user->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be deleted',
                'details' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getModerators(Request $request)
    {
        return User::moderators()->get();
    }
}
