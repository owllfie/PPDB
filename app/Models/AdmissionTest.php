<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionTest extends Model
{
    protected $table = 'admission_tests';
    protected $primaryKey = 'id_admission_test';

    protected $fillable = [
        'id_registrasi',
        'id_jurusan',
        'token',
        'test_type',
        'status',
        'answers',
        'basic_score',
        'interest_score',
        'total_score',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'submitted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function registration()
    {
        return $this->belongsTo(Registrasi::class, 'id_registrasi', 'id_registrasi');
    }
}
