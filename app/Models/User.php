<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nisn',
        'nama_lengkap',
        'email',
        'no_hp',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role', 'id_role');
    }

    public function inboxMessages()
    {
        return $this->hasMany(UserInbox::class, 'id_user', 'id_user');
    }
}
