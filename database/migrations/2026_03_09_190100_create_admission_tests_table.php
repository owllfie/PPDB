<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_tests', function (Blueprint $table) {
            $table->increments('id_admission_test');
            $table->unsignedInteger('id_registrasi');
            $table->string('token', 120)->unique();
            $table->string('test_type', 50)->default('primary');
            $table->string('status', 50)->default('assigned');
            $table->json('answers')->nullable();
            $table->unsignedTinyInteger('basic_score')->default(0);
            $table->unsignedTinyInteger('interest_score')->default(0);
            $table->unsignedTinyInteger('total_score')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('id_registrasi')->references('id_registrasi')->on('registrasi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_tests');
    }
};
