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
        'username',
        'email',
        'no_hp',
        'password',
        'role',
        'is_verified',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'otp_expires_at' => 'datetime',
            'is_verified' => 'boolean',
        ];
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
