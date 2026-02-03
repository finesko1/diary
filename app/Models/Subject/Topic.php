<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $table = 'topics';

    protected $fillable = [
        'name',
        'description',
        'subject_id',
        'user_id',
    ];

    protected $hidden = [
        'user_id',
        'subject_id',
        'created_at',
        'updated_at',
    ];
}
