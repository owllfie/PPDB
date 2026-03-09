<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrasi', function (Blueprint $table) {
            $table->string('current_stage', 50)->default('registration_review')->after('status');
            $table->string('selection_status', 50)->nullable()->after('current_stage');
            $table->string('test_access_token', 120)->nullable()->after('selection_status');
            $table->string('re_registration_token', 120)->nullable()->after('test_access_token');
        });
    }

    public function down(): void
    {
        Schema::table('registrasi', function (Blueprint $table) {
            $table->dropColumn([
                'current_stage',
                'selection_status',
                'test_access_token',
                're_registration_token',
            ]);
        });
    }
};
