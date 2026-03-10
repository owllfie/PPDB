<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE users DROP FOREIGN KEY users_role_foreign');
        } catch (\Throwable $e) {
        }

        DB::statement('ALTER TABLE users MODIFY role BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_role_foreign FOREIGN KEY (role) REFERENCES roles(id_role) ON DELETE CASCADE');
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE users DROP FOREIGN KEY users_role_foreign');
        } catch (\Throwable $e) {
        }

        DB::statement('ALTER TABLE users MODIFY role BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_role_foreign FOREIGN KEY (role) REFERENCES roles(id_role) ON DELETE CASCADE');
    }
};
