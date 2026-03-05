<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('detail_registrasi')) {
            return;
        }

        DB::statement("ALTER TABLE detail_registrasi MODIFY `alamat_lengkap` VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        if (!Schema::hasTable('detail_registrasi')) {
            return;
        }

        DB::statement("ALTER TABLE detail_registrasi MODIFY `alamat_lengkap` VARCHAR(50) NOT NULL");
    }
};
