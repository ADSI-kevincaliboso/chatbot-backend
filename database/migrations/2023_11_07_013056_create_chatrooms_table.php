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
        Schema::create('chatrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('owner');
            $table->index('owner');
            $table->foreign('owner')->references('id')->on('users');

            $table->unsignedBigInteger('moderator')->nullable();
            $table->index('moderator');
            $table->foreign('moderator')->references('id')->on('users');

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatrooms');
    }
};
