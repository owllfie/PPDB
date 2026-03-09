<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'nis',
        'id_registrasi',
        'id_user',
        'nama_lengkap',
        'email',
        'no_hp',
        'alamat',
        'asal_sekolah',
        'id_jurusan',
        'tanggal_daftar_ulang',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'tanggal_daftar_ulang' => 'datetime',
        ];
    }
}
