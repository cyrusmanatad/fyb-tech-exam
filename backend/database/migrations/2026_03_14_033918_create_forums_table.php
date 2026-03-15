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
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('content');
            $table->timestamps();
        });

        Schema::create('forums_comments', function (Blueprint $table) {
            $table->id();
            
            // user relationship
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // forum relationship
            $table->unsignedBigInteger('forum_id');
            $table->foreign('forum_id')->references('id')->on('forums')->cascadeOnDelete();

            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forums');
        Schema::dropIfExists('forums_comments');
    }
};
