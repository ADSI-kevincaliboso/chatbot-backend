<?php

namespace App\Services;

use App\Models\ChatMessage;

class ChatMessageService
{
  /**
   * Create chat message record.
   */
  public function createRecord($userId, $chatRoomId, $message)
  {
    return ChatMessage::create([
      'user_id' => $userId,
      'chatroom_id' => $chatRoomId,
      'message' => $message
    ]);
  }
}