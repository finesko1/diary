<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Subject\SubjectLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasUuids;

    // Константы ролей
    const ROLE_ADMIN = 0;
    const ROLE_TEACHER = 1;
    const ROLE_CHILDREN = 2;
    const ROLE_STUDENT = 3;
    const ROLE_ADULT = 4;

    // Массив
    const ROLES = [
        self::ROLE_ADMIN => 'admin',
        self::ROLE_TEACHER => 'teacher',
        self::ROLE_CHILDREN => 'children',
        self::ROLE_STUDENT => 'student',
        self::ROLE_ADULT => 'adult',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isLearner(): bool
    {
        return !in_array($this->role, [self::ROLE_TEACHER, self::ROLE_ADMIN]);
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isAdult(): bool
    {
        return $this->role === self::ROLE_ADULT;
    }

    public function personalData()
    {
        return $this->hasOne(PersonalData::class);
    }

    public function educationData()
    {
        return $this->hasOne(UserEducationData::class);
    }

    public function subjectLevels()
    {
        return $this->hasMany(SubjectLevel::class);
    }

    public function contactData()
    {
        return $this->hasOne(UserContactData::class);
    }

    public function friendships()
    {
        return Friendship::where(function($query) {
            $query->where('user_id', $this->id)
                ->orWhere('friend_id', $this->id);
        });
    }

    public function friendship($friendId)
    {
        return Friendship
            ::where([['user_id', $this->id], ['friend_id', $friendId]])
            ->orWhere([['user_id', $friendId], ['friend_id', $this->id]])
            ->first();
    }

    public function friends()
    {
        return $this->friendships()->where(function($query) {
            $query->where('status', 'accepted');
        });
    }

    public function getLearners()
    {
        if ($this->role !== self::ROLE_TEACHER) {
            return collect();
        }

        $friendIds = collect()
            ->merge(
                Friendship::where('user_id', $this->id)
                    ->where('status', 'accepted')
                    ->pluck('friend_id')
            )
            ->merge(
                Friendship::where('friend_id', $this->id)
                    ->where('status', 'accepted')
                    ->pluck('user_id')
            )
            ->unique();

        return User::whereIn('id', $friendIds)
            ->where('role', '!=', self::ROLE_TEACHER)
            ->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
