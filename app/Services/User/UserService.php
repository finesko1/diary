<?php

namespace App\Services\User;

use App\Http\Requests\User\CreateLearnerPostRequest;
use App\Http\Requests\User\UpdatePasswordPostRequest;
use App\Models\User\Friendship;
use App\Models\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\password;

class UserService
{
    public function getUsersDataForListById(string|array $userIds)
    {
        $userIds = is_string($userIds) ? [$userIds] : $userIds;

        $users = User::whereIn('id', $userIds)->get();

        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'lastName' => $user->personalData['last_name'] ?? null,
                'firstName' => $user->personalData['first_name'] ?? null,
                'middleName' => $user->personalData['middle_name'] ?? null,
                'img' => $user->img ? Storage::path($user->img) : null,
            ];
        })->toArray();
    }

    public function getUsersDataForProfileById(string|array $userIds)
    {
        if (is_string($userIds))
            $userIds = [$userIds];

        $friends = User::find($userIds);

        return $friends->map(function ($friend) {
            return [
                'id' => $friend->id,
                'email' => $friend->email,
                'last_name' => $friend->personalData->last_name ?? null,
                'first_name' => $friend->personalData->first_name ?? null,
                'middle_name' => $friend->personalData->middle_name ?? null,
                'contactData' => $friend->contactData,
                'educationData' => [
                    $friend->role === User::ROLE_TEACHER ? 'beginning_of_teaching' : 'course'
                    => $friend->educationData?->beginning_of_teaching ?? $friend->educationData?->course,
                ],
            ];
        })->toArray();
    }

    public function getFriends()
    {
        return auth()->user()->friends()->get()->flatMap(function ($user) {
            return $this->getUsersDataForListById(
                $user->user_id === auth()->user()->id
                    ? $user->friend_id
                    : $user->user_id
            );
        });
    }

    public function createLearner(CreateLearnerPostRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $learner = User::create([
               'username' => $request->username,
               'password' => Hash::make($request->password),
            ]);

            Friendship::create([
                'user_id' => auth()->user()->id,
                'friend_id' => $learner->id,
                'status' => Friendship::STATUS_ACCEPTED,
            ]);

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();

            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    public function updatePassword(UpdatePasswordPostRequest $request)
    {
        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);
    }

    public function isFriend($firstUserId, $secondUserId): bool
    {
        $friendship = Friendship
            ::where([['user_id', $firstUserId], ['friend_id', $secondUserId]])
            ->orWhere([['user_id', $secondUserId], ['friend_id', $firstUserId]])
            ->first();

        if (!$friendship ||  $friendship->status !== Friendship::STATUS_ACCEPTED)
        {
            return false;
        }

        return true;
    }
}
