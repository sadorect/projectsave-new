<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultGuard = config('auth.defaults.guard', 'web');

        DB::table('roles')
            ->whereNull('guard_name')
            ->orWhere('guard_name', '')
            ->update(['guard_name' => $defaultGuard]);

        DB::table('permissions')
            ->whereNull('guard_name')
            ->orWhere('guard_name', '')
            ->update(['guard_name' => $defaultGuard]);
    }

    public function down(): void
    {
        // This migration only normalizes invalid guard names and is intentionally irreversible.
    }
};