<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    protected $table = 'friendships';

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
        'initiator_id',
        'block_type',
    ];

    protected $hidden = [
        'initiator_id',
        'created_at',
        'updated_at',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_DECLINED = 'declined';

    const BLOCK_FRIEND = 'user_blocked_friend';
    const BLOCK_BY_FRIEND = 'friend_blocked_user';
    const BLOCK_MUTUAL = 'mutual_block';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
