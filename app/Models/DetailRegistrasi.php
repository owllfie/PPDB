<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRegistrasi extends Model
{
    protected $table = 'detail_registrasi';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_registrasi',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'anak_ke-',
        'alamat_lengkap',
        'no_hp',
        'email',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'sekolah_asal',
        'id_jurusan',
        'kk',
        'ijazah',
        'akta_lahir',
        'rapor',
        'pas_foto',
    ];
}
