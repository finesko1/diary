<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class AssignmentType extends Model
{
    protected $table = 'assignment_types';

    protected $fillable = ['name'];
}
