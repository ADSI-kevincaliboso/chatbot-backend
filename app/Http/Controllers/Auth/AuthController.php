<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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
            DB::commit();

            Auth::attempt(['email' => $user->email, 'password' => $user->password]);

            $token = $user->createToken(config("app.key"))->plainTextToken;

            return response()->json([
                'message' => 'Registered successfully',
                'token' => $token,
                'data' => $user
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Record cannot be created' . $th
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request){
        Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        $user = auth()->user();

        if ($user) {
            $token = $user->createToken(config("app.key"))->plainTextToken;
            
            return response()->json([
                'message' => 'Login success',
                'token' => $token,
                'data' => $user
            ], Response::HTTP_OK);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Goodbye'], Response::HTTP_OK);
    }
}
