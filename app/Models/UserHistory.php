<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserHistory extends Model
{
    use HasFactory;

    protected $table = 'user_histories';
    protected $primaryKey = 'id_history';

    protected $fillable = [
        'id_user',
        'id_admin',
        'field',
        'old_value',
        'new_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user')->withTrashed();
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'id_user')->withTrashed();
    }
}
