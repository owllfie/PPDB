<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('registrasi')) {
            return;
        }

        Schema::table('registrasi', function (Blueprint $table) {
            if (!Schema::hasColumn('registrasi', 'id_jurusan')) {
                $table->unsignedInteger('id_jurusan')->nullable();
            }
            if (!Schema::hasColumn('registrasi', 'recommended_jurusan_id')) {
                $table->unsignedInteger('recommended_jurusan_id')->nullable();
                $table->foreign('recommended_jurusan_id')->references('id_jurusan')->on('jurusan')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('registrasi')) {
            return;
        }

        Schema::table('registrasi', function (Blueprint $table) {
            if (Schema::hasColumn('registrasi', 'recommended_jurusan_id')) {
                $table->dropForeign(['recommended_jurusan_id']);
                $table->dropColumn('recommended_jurusan_id');
            }
        });
    }
};
