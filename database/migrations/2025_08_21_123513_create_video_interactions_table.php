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
        Schema::create('video_interactions', function (Blueprint $table) {
            $table->id();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('lesson_id')->constrained();
        $table->string('action'); // started, progress, completed
        $table->integer('position')->nullable(); // seconds into the video
        $table->timestamp('timestamp');
        $table->unique(['user_id', 'lesson_id', 'action'], 'user_lesson_action_unique');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_interactions');
    }
};
