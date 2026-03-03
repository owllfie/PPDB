<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrasi', function (Blueprint $table) {
            $table->increments('id_registrasi');
            $table->string('nisn', 50);
            $table->string('nama_lengkap', 50);
            $table->string('nik', 50);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin', 50);
            $table->string('agama', 50);
            $table->integer('anak_ke-');
            $table->string('alamat_lengkap', 255);
            $table->string('nama_ayah', 50);
            $table->string('nama_ibu', 50);
            $table->string('pekerjaan_ayah', 50);
            $table->string('pekerjaan_ibu', 50);
            $table->string('kk', 255);
            $table->string('ijazah', 255);
            $table->string('akta_lahir', 255);
            $table->string('sekolah_asal', 50);
            $table->integer('nilai_rapor');
            $table->string('no_hp', 50);
            $table->string('email', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrasi');
    }
};
