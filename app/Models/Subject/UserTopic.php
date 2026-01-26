<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class UserTopic extends Model
{
    protected $table = 'user_topics';

    protected $fillable = [
        'teacher_id',
        'student_id',
        'topic_id',
        'date',
    ];
}
