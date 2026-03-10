<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admission_tests')) {
            return;
        }

        Schema::table('admission_tests', function (Blueprint $table) {
            if (!Schema::hasColumn('admission_tests', 'id_jurusan')) {
                $table->unsignedInteger('id_jurusan')->nullable()->after('id_registrasi');
                $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('admission_tests')) {
            return;
        }

        Schema::table('admission_tests', function (Blueprint $table) {
            if (Schema::hasColumn('admission_tests', 'id_jurusan')) {
                $table->dropForeign(['id_jurusan']);
                $table->dropColumn('id_jurusan');
            }
        });
    }
};
