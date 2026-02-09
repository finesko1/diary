<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class PersonalData extends Model
{
    protected $table = 'personal_data';

    protected $fillable = [
        'last_name', 'first_name', 'middle_name',
        'date_of_birth', 'user_id', 'username',
    ];

    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullName()
    {
        return [
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
        ];
    }
}
