<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('registrasi')) {
            return;
        }

        Schema::table('registrasi', function (Blueprint $table) {
            if (!Schema::hasColumn('registrasi', 'id_jurusan')) {
                $table->unsignedInteger('id_jurusan')->nullable()->after('id_user');
            }

            if (!Schema::hasColumn('registrasi', 'nik')) {
                $table->string('nik', 50)->nullable()->after('nama_lengkap');
            }

            if (!Schema::hasColumn('registrasi', 'tempat_lahir')) {
                $table->string('tempat_lahir', 50)->nullable()->after('nik');
            }

            if (!Schema::hasColumn('registrasi', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }

            if (!Schema::hasColumn('registrasi', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 50)->nullable()->after('tanggal_lahir');
            }

            if (!Schema::hasColumn('registrasi', 'agama')) {
                $table->string('agama', 50)->nullable()->after('jenis_kelamin');
            }

            if (!Schema::hasColumn('registrasi', 'anak_ke-')) {
                $table->integer('anak_ke-')->nullable()->after('agama');
            }

            if (!Schema::hasColumn('registrasi', 'alamat_lengkap')) {
                $table->string('alamat_lengkap', 255)->nullable()->after('anak_ke-');
            }

            if (!Schema::hasColumn('registrasi', 'nama_ayah')) {
                $table->string('nama_ayah', 50)->nullable()->after('alamat_lengkap');
            }

            if (!Schema::hasColumn('registrasi', 'nama_ibu')) {
                $table->string('nama_ibu', 50)->nullable()->after('nama_ayah');
            }

            if (!Schema::hasColumn('registrasi', 'pekerjaan_ayah')) {
                $table->string('pekerjaan_ayah', 50)->nullable()->after('nama_ibu');
            }

            if (!Schema::hasColumn('registrasi', 'pekerjaan_ibu')) {
                $table->string('pekerjaan_ibu', 50)->nullable()->after('pekerjaan_ayah');
            }

            if (!Schema::hasColumn('registrasi', 'kk')) {
                $table->string('kk', 255)->nullable()->after('pekerjaan_ibu');
            }

            if (!Schema::hasColumn('registrasi', 'ijazah')) {
                $table->string('ijazah', 255)->nullable()->after('kk');
            }

            if (!Schema::hasColumn('registrasi', 'akta_lahir')) {
                $table->string('akta_lahir', 255)->nullable()->after('ijazah');
            }

            if (!Schema::hasColumn('registrasi', 'sekolah_asal')) {
                $table->string('sekolah_asal', 50)->nullable()->after('akta_lahir');
            }

            if (!Schema::hasColumn('registrasi', 'no_hp')) {
                $table->string('no_hp', 50)->nullable()->after('nilai_rapor');
            }

            if (!Schema::hasColumn('registrasi', 'email')) {
                $table->string('email', 255)->nullable()->after('no_hp');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty to avoid destructive schema changes on rollback.
    }
};
