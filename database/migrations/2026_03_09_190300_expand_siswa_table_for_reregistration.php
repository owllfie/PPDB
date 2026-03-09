<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->nullable()->after('id_registrasi');
            $table->string('nama_lengkap', 100)->nullable()->after('nis');
            $table->string('email', 255)->nullable()->after('nama_lengkap');
            $table->string('no_hp', 50)->nullable()->after('email');
            $table->text('alamat')->nullable()->after('no_hp');
            $table->string('asal_sekolah', 100)->nullable()->after('alamat');
            $table->unsignedInteger('id_jurusan')->nullable()->after('asal_sekolah');
            $table->timestamp('tanggal_daftar_ulang')->nullable()->after('id_jurusan');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn([
                'id_user',
                'nama_lengkap',
                'email',
                'no_hp',
                'alamat',
                'asal_sekolah',
                'id_jurusan',
                'tanggal_daftar_ulang',
                'updated_at',
            ]);
        });
    }
};
