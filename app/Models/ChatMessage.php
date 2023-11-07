<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message'
    ];

    public function room(): HasOne
    {
        return $this->hasOne(Chatroom::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function helper(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'helper_id');
    }
}
