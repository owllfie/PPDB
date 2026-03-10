<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registrasi extends Model
{
    protected $table = 'registrasi';
    protected $primaryKey = 'id_registrasi';

    protected $fillable = [
        'id_user',
        'nisn',
        'id_jurusan',
        'nilai_rapor',
        'nilai_tes',
        'status',
        'current_stage',
        'selection_status',
        'test_access_token',
        're_registration_token',
        'rapor',
        'pas_foto',
        'recommended_jurusan_id',
    ];

    public function tests()
    {
        return $this->hasMany(AdmissionTest::class, 'id_registrasi', 'id_registrasi');
    }

}
