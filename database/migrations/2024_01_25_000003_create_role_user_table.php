<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type'], 'role_user_primary');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->index(['model_id', 'model_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_user');
    }
};
