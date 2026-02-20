<?php

namespace App\Models\Subject;

use App\Models\File;
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

    protected static function booted()
    {
        static::deleting(function ($userTopic) {
            $assignmentIds = $userTopic->assignments()->pluck('assignments.id');

            Assignment::whereIn('id', $assignmentIds)->delete();
        });
    }

    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'user_topic_assignments')
            ->withTimestamps();
    }

    public function files()
    {
        return $this->morphMany(File::class, 'attachable');
    }
}
