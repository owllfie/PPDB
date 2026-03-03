<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jurusan')) {
            Schema::create('jurusan', function (Blueprint $table) {
                $table->increments('id_jurusan');
                $table->string('nama_jurusan', 50);
            });
        }

        if (!Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->increments('id_log');
                $table->unsignedBigInteger('id_user');
                $table->string('action', 255);
                $table->string('ip_address', 50)->nullable();
                $table->timestamp('created_at')->nullable();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('setting')) {
            Schema::create('setting', function (Blueprint $table) {
                $table->increments('id_setting');
                $table->string('nama_sekolah', 50)->nullable();
                $table->string('logo_sekolah', 255)->nullable();
                $table->string('alamat', 255)->nullable();
                $table->string('kepala_sekolah', 50)->nullable();
                $table->string('nomor_kontak', 50)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->unsignedInteger('created_by')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('setting');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('jurusan');
    }
};
