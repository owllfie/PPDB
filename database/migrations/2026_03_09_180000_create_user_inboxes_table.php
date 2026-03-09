<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_inboxes', function (Blueprint $table) {
            $table->increments('id_inbox');
            $table->unsignedBigInteger('id_user');
            $table->string('subject', 150);
            $table->text('message');
            $table->string('status', 50)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['id_user', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_inboxes');
    }
};
