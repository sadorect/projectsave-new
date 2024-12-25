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
        Schema::table('courses', function (Blueprint $table) {
            $table->text('objectives')->nullable()->after('description');
            $table->text('outcomes')->nullable()->after('objectives');
            $table->text('evaluation')->nullable()->after('outcomes');
            $table->text('recommended_books')->nullable()->after('evaluation');
            $table->json('documents')->nullable()->after('recommended_books');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'objectives',
                'outcomes',
                'evaluation',
                'recommended_books',
                'documents'
            ]);
        });
    }
};
