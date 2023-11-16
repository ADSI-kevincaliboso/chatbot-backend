<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'ChatBot ADMIN',
            'email' => 'admin@chatbot.com',
            'password' => bcrypt('chatbot123'),
            'user_type' => 'admin'
        ]);

        User::create([
            'name' => 'Chatbot',
            'email' => 'bot@chatbot.com',
            'password' => bcrypt('123456'),
            'user_type' => 'admin'
        ]);
    }
}
