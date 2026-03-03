<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'setting';
    protected $primaryKey = 'id_setting';
    public $timestamps = false;

    protected $fillable = [
        'nama_sekolah',
        'logo_sekolah',
        'alamat',
        'kepala_sekolah',
        'nomor_kontak',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
