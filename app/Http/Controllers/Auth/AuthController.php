<?php

namespace App\Http\Controllers\Auth;

use App\Events\DestroyChatroom;
use App\Events\NewChatRoom;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Chatroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => $request->password
            ]);

            $chatRoom = Chatroom::create([
                'name' => $user->name,
                'user_id' => $user->id
            ]);

            DB::commit();

            Auth::attempt(['email' => $request->email, 'password' => $request->password]);

            $authUser = auth()->user();

            $token = $user->createToken(config("app.key"))->plainTextToken;
            
            broadcast(new NewChatRoom($chatRoom))->toOthers();
            

            return response()->json([
                'message' => 'Registered successfully',
                'token' => $token,
                'chatroomId' => $chatRoom->id,
                'data' => $authUser
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created',
                'details' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request){
        Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        $user = auth()->user();

        if ($user) {
            $token = $user->createToken(config("app.key"))->plainTextToken;
            $chatroom = 0;

            if ($user->user_type == 'user') {
                if ($user->chatroom){
                    $chatroom = $user->chatroom->id;
                } else {
                    $createdChatroom = Chatroom::create([
                        'name' => $user->name,
                        'user_id' => $user->id
                    ]);

                    $chatroom = $createdChatroom->id;
                    broadcast(new NewChatRoom($createdChatroom))->toOthers();
                }
            }

            
            return response()->json([
                'message' => 'Login success',
                'token' => $token,
                'chatroomId' => $chatroom,
                'data' => $user
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Wrong Credentials'
        ], Response::HTTP_NOT_FOUND);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== "admin") {
            // broadcast the chatroom delete here
            broadcast(new DestroyChatroom($user->chatroom))->toOthers();

            $user->chatroom->delete();
        }

        $user->tokens()->delete();
        return response()->json(['message' => 'Goodbye'], Response::HTTP_OK);
    }
}
