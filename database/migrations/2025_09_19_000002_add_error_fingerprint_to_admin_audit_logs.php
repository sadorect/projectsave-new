<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('admin_audit_logs', function (Blueprint $table) {
            $table->string('error_fingerprint')->nullable()->after('meta')->index();
        });
    }

    public function down()
    {
        Schema::table('admin_audit_logs', function (Blueprint $table) {
            $table->dropColumn('error_fingerprint');
        });
    }
};
