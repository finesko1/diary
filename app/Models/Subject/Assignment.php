<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $table = 'assignments';

    protected $fillable = [
        'assignment_type_id',
        'description',
        'status',
        'mark',
    ];
}
