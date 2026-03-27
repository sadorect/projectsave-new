<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('newsletter_sent_at')->nullable()->after('view_count');
        });

        DB::table('posts')
            ->whereNotNull('published_at')
            ->update(['newsletter_sent_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('newsletter_sent_at');
        });
    }
};
