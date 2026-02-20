<?php

namespace App\Models\Subject;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Assignment extends Model
{
    protected $table = 'assignments';

    protected $fillable = [
        'assignment_type_id',
        'description',
        'status',
        'mark',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($assignment) {
            $files = $assignment->files;

            foreach ($files as $file) {
                Storage::disk($file->disk)->delete($file->path);
                $file->delete();
            };
        });
    }

    public function files()
    {
        return $this->morphMany(File::class, 'attachable');
    }

    public function userTopics()
    {
        return $this->belongsToMany(UserTopic::class, 'user_topic_assignments')
            ->withTimestamps();
    }
}
