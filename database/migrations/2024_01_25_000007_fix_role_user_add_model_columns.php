<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Production may have a legacy role_user table with only role_id + user_id.
        // Spatie needs model_type and model_id. This migration fixes that without
        // touching the table when the columns already exist.

        if (! Schema::hasColumn('role_user', 'model_type')) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->string('model_type')->default('App\\Models\\User')->after('role_id');
            });
            // Remove the default now that backfill is done
            DB::statement("ALTER TABLE role_user ALTER COLUMN model_type DROP DEFAULT");
        }

        if (! Schema::hasColumn('role_user', 'model_id')) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            });

            // If the old user_id column exists, copy its values into model_id
            if (Schema::hasColumn('role_user', 'user_id')) {
                DB::statement("UPDATE role_user SET model_id = user_id");
            }

            // Make model_id NOT NULL now that it's populated
            Schema::table('role_user', function (Blueprint $table) {
                $table->unsignedBigInteger('model_id')->nullable(false)->change();
            });
        }

        // Add the index if not already present
        $indexes = collect(DB::select("SHOW INDEX FROM role_user"))->pluck('Key_name');
        if (! $indexes->contains('role_user_model_id_model_type_index')) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->index(['model_id', 'model_type'], 'role_user_model_id_model_type_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropIndex('role_user_model_id_model_type_index');
            $table->dropColumn(['model_type', 'model_id']);
        });
    }
};
