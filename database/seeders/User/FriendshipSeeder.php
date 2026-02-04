<?php

namespace Database\Seeders\User;

use App\Models\User\User;
use App\Models\User\Friendship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FriendshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::where('role', User::ROLE_TEACHER)->first();
        $friends = User::where('role', '!=', User::ROLE_TEACHER)->get();

        foreach ($friends as $friend)
        {
            if ($friend->role === User::ROLE_CHILDREN)
            {
                Friendship::create([
                    'user_id' => $teacher->id,
                    'friend_id' => $friend->id,
                    'status' => 'accepted',
                    'initiator_id' => $teacher->id,
                ]);
            }
            if ($friend->role === User::ROLE_STUDENT)
            {
                Friendship::create([
                    'user_id' => $teacher->id,
                    'friend_id' => $friend->id,
                    'status' => 'accepted',
                    'initiator_id' => $friend->id,
                ]);
            }
            if ($friend->role === User::ROLE_ADULT)
            {
                Friendship::create([
                    'user_id' => $teacher->id,
                    'friend_id' => $friend->id,
                    'status' => 'accepted',
                    'block_type' => 'friend_blocked_user',
                ]);
            }
        }

    }
}
