<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('registrasi')) {
            return;
        }

        DB::statement('ALTER TABLE registrasi MODIFY id_user BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('registrasi')) {
            return;
        }

        DB::statement('ALTER TABLE registrasi MODIFY id_user BIGINT UNSIGNED NOT NULL');
    }
};
