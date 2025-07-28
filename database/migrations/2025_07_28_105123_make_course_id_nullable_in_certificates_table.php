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
        Schema::table('certificates', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['course_id']);
            
            // Modify course_id to be nullable
            $table->foreignId('course_id')->nullable()->change();
            
            // Re-add the foreign key constraint but allow null values
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['course_id']);
            
            // Restore to non-nullable (this might fail if there are null values)
            $table->foreignId('course_id')->change();
            
            // Re-add the non-nullable foreign key constraint
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }
};
