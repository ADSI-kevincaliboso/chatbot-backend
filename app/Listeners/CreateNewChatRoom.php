<?php

namespace App\Listeners;

use App\Events\NewChatRoom;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNewChatRoom
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewChatRoom $event): void
    {
        //
    }
}
