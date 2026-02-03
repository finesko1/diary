<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'name',
    ];

    public function subjectLevel()
    {
        return $this->hasOne(SubjectLevel::class);
    }
}
