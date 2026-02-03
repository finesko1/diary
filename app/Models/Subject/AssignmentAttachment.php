<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Model;

class AssignmentAttachment extends Model
{
    protected $table = 'assignment_attachments';

    protected $fillable = [
        'assignment_id',
        'user_id',
        'type',
        'description',
        'path',
        'original_name',
        'mime_type',
        'size',
        'metadata',
    ];
    protected $hidden = [
        'assignment_id',
        'user_id',
        'type',
        'description',
        'path',
        'original_name',
        'mime_type',
        'size',
        'metadata',
    ];
}
