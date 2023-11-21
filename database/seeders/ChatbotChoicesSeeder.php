<?php

namespace Database\Seeders;

use App\Models\ChatbotChoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatbotChoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatChoices = [
            'Information',
            'I want to create an incident report'
        ];

        foreach ($chatChoices as $chatChoice) {
            ChatbotChoice::create([
                'choice' => $chatChoice
            ]);
        };
    }
}
