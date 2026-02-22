<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Make user-referencing FK columns safe to cascade/nullify on user deletion.
 *
 * - courses.instructor_id          → nullable + SET NULL on delete
 * - anniversary_wish_logs.user_id  → CASCADE on delete
 * - anniversary_wish_logs.sent_by  → nullable + SET NULL on delete
 * - video_interactions.user_id     → CASCADE on delete
 * - partners.user_id               → SET NULL on delete (already nullable)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── courses ───────────────────────────────────────────────────────
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->unsignedBigInteger('instructor_id')->nullable()->change();
            $table->foreign('instructor_id')->references('id')->on('users')->nullOnDelete();
        });

        // ── anniversary_wish_logs ─────────────────────────────────────────
        Schema::table('anniversary_wish_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->dropForeign(['sent_by']);
            $table->unsignedBigInteger('sent_by')->nullable()->change();
            $table->foreign('sent_by')->references('id')->on('users')->nullOnDelete();
        });

        // ── video_interactions ────────────────────────────────────────────
        Schema::table('video_interactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // ── partners ──────────────────────────────────────────────────────
        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
        // ── form_submissions ──────────────────────────────────────────────────
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->unsignedBigInteger('instructor_id')->nullable(false)->change();
            $table->foreign('instructor_id')->references('id')->on('users');
        });

        Schema::table('anniversary_wish_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');

            $table->dropForeign(['sent_by']);
            $table->unsignedBigInteger('sent_by')->nullable(false)->change();
            $table->foreign('sent_by')->references('id')->on('users');
        });

        Schema::table('video_interactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
