<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileMaterial extends Model
{
    protected $table = 'profile_materials';

    protected $fillable = [
        'user_id',
        'subject_id',
        'description',
    ];

    protected $hidden = [
        'user_id',
        'subject_id',
        'description',
    ];


    public function files()
    {
        return $this->morphMany(File::class, 'attachable');
    }
}
