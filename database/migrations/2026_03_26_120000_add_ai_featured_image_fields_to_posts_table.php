<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('featured_image_source')->nullable()->after('image');
            $table->boolean('featured_image_generation_enabled')->default(false)->after('featured_image_source');
            $table->string('featured_image_generation_status')->nullable()->after('featured_image_generation_enabled');
            $table->string('featured_image_provider')->nullable()->after('featured_image_generation_status');
            $table->string('featured_image_preset')->nullable()->after('featured_image_provider');
            $table->text('featured_image_prompt')->nullable()->after('featured_image_preset');
            $table->json('featured_image_options')->nullable()->after('featured_image_prompt');
            $table->timestamp('featured_image_generated_at')->nullable()->after('featured_image_options');
            $table->text('featured_image_generation_error')->nullable()->after('featured_image_generated_at');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'featured_image_source',
                'featured_image_generation_enabled',
                'featured_image_generation_status',
                'featured_image_provider',
                'featured_image_preset',
                'featured_image_prompt',
                'featured_image_options',
                'featured_image_generated_at',
                'featured_image_generation_error',
            ]);
        });
    }
};