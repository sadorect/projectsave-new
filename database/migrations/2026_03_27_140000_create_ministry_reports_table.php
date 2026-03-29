<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministry_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('report_type')->index();
            $table->string('lead_team')->nullable();
            $table->string('location')->nullable()->index();
            $table->date('report_date')->index();
            $table->text('summary');
            $table->longText('details');
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedInteger('people_reached')->default(0);
            $table->unsignedInteger('souls_impacted')->default(0);
            $table->unsignedInteger('volunteers_count')->default(0);
            $table->string('testimony_title')->nullable();
            $table->text('testimony_quote')->nullable();
            $table->longText('prayer_points')->nullable();
            $table->longText('next_steps')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministry_reports');
    }
};
