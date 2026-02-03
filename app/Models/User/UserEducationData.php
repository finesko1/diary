<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserEducationData extends Model
{
    protected $table = 'user_education_data';

    protected $fillable = [
        'beginning_of_teaching',
        'course',
    ];

    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
