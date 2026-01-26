<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserContactData extends Model
{
    protected $table = 'user_contact_data';

    protected $fillable = [
        'user_id',
        'city',
        'telephone',
        'whatsapp',
        'telegram',
        'vk',
        'calls_platform',
    ];

    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
