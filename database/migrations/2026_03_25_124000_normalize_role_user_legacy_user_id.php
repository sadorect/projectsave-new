<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('role_user')) {
            return;
        }

        if (! Schema::hasColumn('role_user', 'user_id')) {
            return;
        }

        if (Schema::hasColumn('role_user', 'model_id')) {
            DB::statement('UPDATE role_user SET model_id = COALESCE(model_id, user_id)');
        }

        $primaryColumns = collect(DB::select("SHOW INDEX FROM role_user WHERE Key_name = 'PRIMARY'"))
            ->pluck('Column_name')
            ->values()
            ->all();

        $desiredPrimary = ['role_id', 'model_id', 'model_type'];

        if ($primaryColumns !== [] && $primaryColumns !== $desiredPrimary) {
            DB::statement('ALTER TABLE role_user DROP PRIMARY KEY');
        }

        try {
            Schema::table('role_user', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        } catch (\Throwable $e) {
            DB::statement('ALTER TABLE role_user MODIFY user_id BIGINT UNSIGNED NULL');
        }

        if (Schema::hasColumn('role_user', 'model_id') && Schema::hasColumn('role_user', 'model_type')) {
            $primaryColumns = collect(DB::select("SHOW INDEX FROM role_user WHERE Key_name = 'PRIMARY'"))
                ->pluck('Column_name')
                ->values()
                ->all();

            if ($primaryColumns !== $desiredPrimary) {
                DB::statement('ALTER TABLE role_user ADD PRIMARY KEY (role_id, model_id, model_type)');
            }
        }
    }

    public function down(): void
    {
        // Legacy compatibility migration intentionally left without a destructive rollback.
    }
};