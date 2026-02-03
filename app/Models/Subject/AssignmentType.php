<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class AssignmentType extends Model
{
    protected $table = 'assignment_types';

    protected $fillable = [
        'name',
        'user_id',
    ];

    protected $hidden = [
        'name',
        'created_at',
        'updated_at',
        'user_id',
    ];
}
