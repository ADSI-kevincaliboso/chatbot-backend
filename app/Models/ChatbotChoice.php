<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatbotChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'choice',
        'reply_id'
    ];

    public function reply() : HasOne
    {
        return $this->hasOne(ChatbotMessage::class, 'id', 'reply_id');
    }
}
