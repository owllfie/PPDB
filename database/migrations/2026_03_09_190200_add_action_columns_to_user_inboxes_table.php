<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_inboxes', function (Blueprint $table) {
            $table->string('action_label', 100)->nullable()->after('status');
            $table->string('action_url', 255)->nullable()->after('action_label');
        });
    }

    public function down(): void
    {
        Schema::table('user_inboxes', function (Blueprint $table) {
            $table->dropColumn(['action_label', 'action_url']);
        });
    }
};
