<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'student_id',
        'date',
    ];
    protected $hidden = [
        'subject_id',
        'teacher_id',
        'student_id',
        'date',
        'created_at',
        'updated_at',
    ];

    public function subjectLevel()
    {
        return $this->hasOne(SubjectLevel::class);
    }
}
