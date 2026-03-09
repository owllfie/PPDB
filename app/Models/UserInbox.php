<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInbox extends Model
{
    protected $table = 'user_inboxes';
    protected $primaryKey = 'id_inbox';

    protected $fillable = [
        'id_user',
        'subject',
        'message',
        'status',
        'action_label',
        'action_url',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
