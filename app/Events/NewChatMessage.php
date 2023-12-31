<?php

namespace App\Events;

use App\Http\Resources\ChatMessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatMessage;
    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessageResource $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.'.$this->chatMessage->room->id),
        ];
    }

    public function broadcastAs()
    {
        return 'message.new';
    }
}
