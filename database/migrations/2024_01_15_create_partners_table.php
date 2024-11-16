<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('dob');
            $table->string('profession');
            $table->string('phone');
            $table->string('email')->unique();
            $table->enum('born_again', ['yes', 'no']);
            $table->date('salvation_date')->nullable();
            $table->string('salvation_place')->nullable();
            $table->enum('water_baptized', ['yes', 'no']);
            $table->enum('baptism_type', ['immersion', 'sprinkling'])->nullable();
            $table->enum('holy_ghost_baptism', ['yes', 'no']);
            $table->text('holy_ghost_baptism_reason')->nullable();
            $table->enum('leadership_experience', ['yes', 'no']);
            $table->json('leadership_details')->nullable();
            $table->string('calling');
            $table->string('partner_type');
            $table->string('commitment_question');
            $table->string('commitment_answer');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partners');
    }
};
