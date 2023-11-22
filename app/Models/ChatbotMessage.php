<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message'
    ];

    public function choices() : HasMany
    {
        return $this->hasMany(ChatbotChoice::class, 'chatbot_messages_id', 'id');
    }

    public function nextPrompt() : HasOne
    {
        return $this->hasOne(ChatbotMessage::class, 'id', 'nextId');
    }
}
