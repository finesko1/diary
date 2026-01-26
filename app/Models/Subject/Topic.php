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
    ];
}
