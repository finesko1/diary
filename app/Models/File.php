<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'disk', 'path', 'filename', 'original_name', 'mime_type', 'size', 'extension', 'type',
        'metadata', 'width', 'height', 'user_id',
    ];

    /**
     * Пользователь, загрузивший файл
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Родительская модель (полиморфная связь)
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}
