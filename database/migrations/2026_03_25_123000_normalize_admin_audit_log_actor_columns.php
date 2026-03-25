<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_audit_logs')) {
            return;
        }

        Schema::table('admin_audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_audit_logs', 'admin_user_id')) {
                $table->unsignedBigInteger('admin_user_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('admin_audit_logs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('admin_user_id');
            }
        });

        if (Schema::hasColumn('admin_audit_logs', 'admin_user_id') && Schema::hasColumn('admin_audit_logs', 'user_id')) {
            DB::table('admin_audit_logs')
                ->whereNull('admin_user_id')
                ->whereNotNull('user_id')
                ->update(['admin_user_id' => DB::raw('user_id')]);

            DB::table('admin_audit_logs')
                ->whereNull('user_id')
                ->whereNotNull('admin_user_id')
                ->update(['user_id' => DB::raw('admin_user_id')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_audit_logs')) {
            return;
        }

        Schema::table('admin_audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('admin_audit_logs', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};