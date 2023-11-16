<?php

namespace Database\Seeders;

use App\Models\ChatbotMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatbotMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatMessages = [
            'Welcome to this Bullying Report Chatbot. What can I help you with today?',
            'Sorry to hear this. Let\'s make an official report for this in our system. Can you tell us what happened?',
            'This is received. Rest assured that this will be dealt with immediately. Let\'s wait for an admin or moderator to connect to your chatroom for better report creation. Thank you for your patience.'
        ];

        foreach ($chatMessages as $chatMessage) {
            ChatbotMessage::create([
                'message' => $chatMessage
            ]);
        };
    }
}
