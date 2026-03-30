<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devotional_import_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_disk')->default('local');
            $table->string('source_path');
            $table->string('source_filename');
            $table->string('source_checksum', 64)->nullable()->index();
            $table->string('duplicate_strategy')->default('update');
            $table->string('status')->default('queued')->index();
            $table->unsignedInteger('total_entries')->nullable();
            $table->unsignedInteger('processed_entries')->default(0);
            $table->integer('last_processed_index')->default(-1);
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->json('created_categories')->nullable();
            $table->json('category_counts')->nullable();
            $table->json('failures')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('queued_at')->nullable()->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devotional_import_sessions');
    }
};
