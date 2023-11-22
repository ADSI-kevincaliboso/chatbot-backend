<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chatbot_choices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chatbot_messages_id')->nullable();
            $table->index('chatbot_messages_id');
            $table->foreign('chatbot_messages_id')->references('id')->on('chatbot_messages');
            $table->string('choice');
            $table->integer('reply_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_choices');
    }
};
