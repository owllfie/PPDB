<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 50)->default('pending')->after('email');
            }
            if (!Schema::hasColumn('users', 'nama_lengkap')) {
                $table->string('nama_lengkap', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->string('alamat', 255)->nullable()->after('nama_lengkap');
            }
            if (!Schema::hasColumn('users', 'login_token_hash')) {
                $table->string('login_token_hash', 255)->nullable()->after('otp_expires_at');
            }
            if (!Schema::hasColumn('users', 'login_token_expires_at')) {
                $table->timestamp('login_token_expires_at')->nullable()->after('login_token_hash');
            }
            if (!Schema::hasColumn('users', 'login_token_used_at')) {
                $table->timestamp('login_token_used_at')->nullable()->after('login_token_expires_at');
            }
        });

        DB::statement('ALTER TABLE users MODIFY role BIGINT UNSIGNED NULL');
        if (Schema::hasColumn('users', 'status')) {
            DB::table('users')
                ->whereNotNull('role')
                ->where('role', '!=', 1)
                ->update(['status' => 'approved', 'is_verified' => true]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'login_token_used_at')) {
                $table->dropColumn('login_token_used_at');
            }
            if (Schema::hasColumn('users', 'login_token_expires_at')) {
                $table->dropColumn('login_token_expires_at');
            }
            if (Schema::hasColumn('users', 'login_token_hash')) {
                $table->dropColumn('login_token_hash');
            }
            if (Schema::hasColumn('users', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('users', 'nama_lengkap')) {
                $table->dropColumn('nama_lengkap');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });

        DB::statement('ALTER TABLE users MODIFY role BIGINT UNSIGNED NOT NULL');
    }
};
