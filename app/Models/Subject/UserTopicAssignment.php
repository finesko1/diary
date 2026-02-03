<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class UserTopicAssignment extends Model
{
    protected $table = 'user_topic_assignments';

    protected $fillable = [
        'user_topic_id',
        'assignment_id',
    ];
}
