<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class UserTopic extends Model
{
    protected $table = 'user_topics';

    protected $fillable = [
        'lesson_id',
        'topic_id',
        'mark',
    ];

    protected $hidden = [
        'lesson_id',
        'topic_id',
        'mark',
        'created_at',
        'updated_at',
    ];
}
