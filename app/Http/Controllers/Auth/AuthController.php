<?php

namespace App\Http\Controllers\Auth;

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
                'name' => $user->name
            ]);

            DB::commit();

            Auth::attempt(['email' => $request->email, 'password' => $request->password]);

            $authUser = auth()->user();

            $token = $user->createToken(config("app.key"))->plainTextToken;

            return response()->json([
                'message' => 'Registered successfully',
                'token' => $token,
                'chatroomId' => $chatRoom->id,
                'data' => $authUser
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request){
        Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        $user = auth()->user();

        if ($user) {
            $token = $user->createToken(config("app.key"))->plainTextToken;
            $chatroom = 0;

            if ($user->chatroom){
                $chatroom = $user->chatroom->id;
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
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Goodbye'], Response::HTTP_OK);
    }
}
