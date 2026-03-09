<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrasi', function (Blueprint $table) {
            if (!Schema::hasColumn('registrasi', 'kk')) {
                $table->string('kk', 255)->nullable()->after('status');
            }

            if (!Schema::hasColumn('registrasi', 'ijazah')) {
                $table->string('ijazah', 255)->nullable()->after('kk');
            }

            if (!Schema::hasColumn('registrasi', 'akta_lahir')) {
                $table->string('akta_lahir', 255)->nullable()->after('ijazah');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrasi', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('registrasi', 'kk')) {
                $dropColumns[] = 'kk';
            }

            if (Schema::hasColumn('registrasi', 'ijazah')) {
                $dropColumns[] = 'ijazah';
            }

            if (Schema::hasColumn('registrasi', 'akta_lahir')) {
                $dropColumns[] = 'akta_lahir';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
