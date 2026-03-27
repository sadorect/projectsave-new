<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deletion_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('deletion_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('requester_name')->nullable()->after('user_id');
            $table->string('requester_email')->nullable()->after('requester_name');
            $table->foreignId('processed_by')->nullable()->after('status');
            $table->text('processed_notes')->nullable()->after('processed_by');
        });

        Schema::table('deletion_requests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();
        });

        DB::table('deletion_requests')
            ->join('users', 'users.id', '=', 'deletion_requests.user_id')
            ->update([
                'requester_name' => DB::raw('users.name'),
                'requester_email' => DB::raw('users.email'),
            ]);
    }

    public function down(): void
    {
        Schema::table('deletion_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['processed_by']);
            $table->dropColumn(['requester_name', 'requester_email', 'processed_notes']);
        });

        Schema::table('deletion_requests', function (Blueprint $table) {
            $table->dropColumn('processed_by');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
