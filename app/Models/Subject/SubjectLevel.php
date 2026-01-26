<?php

namespace App\Models\Subject;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class SubjectLevel extends Model
{
    protected $table = 'subject_levels';

    protected $fillable = [
        'user_id',
        'subject_id',
        'level',
        'evaluated_by',
        'certificate_info',
        'certificate_date',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
