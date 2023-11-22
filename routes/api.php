<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatbotChoicesController;
use App\Http\Controllers\ChatbotMessageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatroomController;
use App\Http\Controllers\UserController;
use App\Models\ChatbotMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::middleware(['moderator'])->group(function () {
        Route::get('chat-rooms/moderator', [ChatroomController::class, 'getAssignedRooms']);
    });

    
    Route::middleware(['admin'])->group(function () {
        Route::get('users/moderators', [UserController::class, 'getModerators']);
        Route::post('chat-rooms/assign', [ChatroomController::class, 'assignModerator']);
        Route::apiResources([
            'users' => UserController::class
        ]);
    });
    Route::patch('users/{user}', [UserController::class, 'update']);
    Route::apiResource('chat-rooms', ChatroomController::class);

    Route::get('chat/room/{roomId}/messages', [ChatController::class, 'messages']);
    Route::post('chat/room/{roomId}/message', [ChatController::class, 'newMessage']);
    Route::post('chat/room/{roomId}/message/bot', [ChatController::class, 'newBotMessage']);
    Route::get('chatbot-messages/init', [ChatbotMessageController::class, 'botInit']);
    Route::post('chatbot-messages/get-response', [ChatbotMessageController::class, 'getResponse']);
    Route::post('chatbot-messages/create-incident-report', [ChatbotMessageController::class, 'createIncidentReport']);
    Route::apiResource('chatbot-messages', ChatbotMessageController::class);
    Route::apiResource('chatbot-choices', ChatbotChoicesController::class);
});


Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    if (Auth::check()) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
