<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chatroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'owner',
        'status',
        'moderator'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function scopeActiveRooms($query)
    {
        return $query->where('status', '=', 'active');
    }
}
