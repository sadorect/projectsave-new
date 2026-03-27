<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('featured_image_candidate_path')->nullable()->after('image');
            $table->string('featured_image_approval_status')->nullable()->after('featured_image_generation_status');
            $table->foreignId('featured_image_reviewed_by')->nullable()->after('featured_image_approval_status')->constrained('users')->nullOnDelete();
            $table->timestamp('featured_image_reviewed_at')->nullable()->after('featured_image_reviewed_by');
            $table->text('featured_image_review_notes')->nullable()->after('featured_image_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('featured_image_reviewed_by');
            $table->dropColumn([
                'featured_image_candidate_path',
                'featured_image_approval_status',
                'featured_image_reviewed_at',
                'featured_image_review_notes',
            ]);
        });
    }
};