<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatbotMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $bot = User::find(2);

        return [
            "id" => $this->id,
            "message" => $this->message,
            "sender" => new UserResource($bot),
            "created_at" => $this->created_at
        ];
    }
}
