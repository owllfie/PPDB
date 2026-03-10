<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('registrasi')) {
            Schema::table('registrasi', function (Blueprint $table) {
                if (!Schema::hasColumn('registrasi', 'rapor')) {
                    $table->string('rapor', 255)->nullable();
                }
                if (!Schema::hasColumn('registrasi', 'pas_foto')) {
                    $table->string('pas_foto', 255)->nullable();
                }
            });
        }

        if (Schema::hasTable('detail_registrasi')) {
            Schema::table('detail_registrasi', function (Blueprint $table) {
                if (!Schema::hasColumn('detail_registrasi', 'rapor')) {
                    $table->string('rapor', 255)->nullable();
                }
                if (!Schema::hasColumn('detail_registrasi', 'pas_foto')) {
                    $table->string('pas_foto', 255)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('registrasi')) {
            Schema::table('registrasi', function (Blueprint $table) {
                $dropColumns = [];
                if (Schema::hasColumn('registrasi', 'rapor')) {
                    $dropColumns[] = 'rapor';
                }
                if (Schema::hasColumn('registrasi', 'pas_foto')) {
                    $dropColumns[] = 'pas_foto';
                }
                if (!empty($dropColumns)) {
                    $table->dropColumn($dropColumns);
                }
            });
        }

        if (Schema::hasTable('detail_registrasi')) {
            Schema::table('detail_registrasi', function (Blueprint $table) {
                $dropColumns = [];
                if (Schema::hasColumn('detail_registrasi', 'rapor')) {
                    $dropColumns[] = 'rapor';
                }
                if (Schema::hasColumn('detail_registrasi', 'pas_foto')) {
                    $dropColumns[] = 'pas_foto';
                }
                if (!empty($dropColumns)) {
                    $table->dropColumn($dropColumns);
                }
            });
        }
    }
};
