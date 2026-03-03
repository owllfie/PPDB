<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registrasi extends Model
{
    protected $table = 'registrasi';
    protected $primaryKey = 'id_registrasi';

    protected $fillable = [
        'id_user',
        'id_jurusan',
        'nisn',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'anak_ke-',
        'alamat_lengkap',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'kk',
        'ijazah',
        'akta_lahir',
        'sekolah_asal',
        'nilai_rapor',
        'no_hp',
        'email',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }
}
