<?php

namespace App\Services\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                'lastName' => $user->personalData['last_name'],
                'firstName' => $user->personalData['first_name'],
                'middleName' => $user->personalData['middle_name'],
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
        return auth()->user()->friends()
            ->with(['personalData' => function ($query) {
                $query->select('user_id', 'first_name', 'last_name', 'middle_name');
            }])
            ->get()
            ->mapWithKeys(function ($friend) {
                return $this->getUsersDataForListById($friend->id);
            });
    }
}
