<?php

namespace App\Services\User;

use App\Http\Requests\Friendship\FriendRequest;
use App\Http\Requests\Friendship\ValidateRequestsGetRequest;
use App\Models\User\Friendship;
use App\Models\User\User;

class FriendshipService
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->getFriends();
    }

    public function show(FriendRequest $request)
    {
        return $this->userService->getUsersDataForProfileById($request->friendId);
    }

    public function block(FriendRequest $request)
    {
        $user = auth()->user();
        $friendship = $user->friendship($request->friendId);

        if (User::find($request->friendId)->role === User::ROLE_ADMIN)
            throw new \InvalidArgumentException('Невозможно заблокировать пользователя');

        if (!$friendship)
        {
            Friendship::create([
                'user_id' => $user->id,
                'friend_id' => $request->friendId,
                'status' => Friendship::STATUS_BLOCKED,
                'block_type' => Friendship::BLOCK_FRIEND
            ]);
        }
        else
        {
            if ($friendship->status === Friendship::STATUS_BLOCKED)
            {
                $friendship->update([
                    'block_type' => Friendship::BLOCK_MUTUAL
                ]);
            }
            else
            {
                $friendship->update([
                    'status' => Friendship::STATUS_BLOCKED,
                    'block_type' => Friendship::BLOCK_FRIEND
                ]);
            }
        }
    }

    public function unblock(FriendRequest $request)
    {
        $user = auth()->user();
        $friendship = $user->friendship($request->friendId);

        if (!$friendship || $friendship->status !== Friendship::STATUS_BLOCKED )
            throw new \InvalidArgumentException('Пользователь не заблокирован');

        if ($user->isLearner())
            throw new \InvalidArgumentException('Недоступно');

        $friendship->update([
            'status' => Friendship::STATUS_ACCEPTED,
            'initiator_id' => $user->id,
            'block_type' => null
        ]);

//        if ($friendship === Friendship::BLOCK_MUTUAL)
//        {
//            $friendship->update([
//                'block_type' => $friendship->user_id === $user->id
//                    ? Friendship::BLOCK_BY_FRIEND
//                    : Friendship::BLOCK_FRIEND
//            ]);
//        }
//        else
//        {
//            $friendship->update([
//                'status' => Friendship::STATUS_DECLINED,
//                'initiator_id' => $friendship->user_id === $user->id
//                    ? $request->friendId
//                    : $user->id,
//                'block_type' => null
//            ]);
//        }
    }

    public function delete(FriendRequest $request)
    {
        $user = auth()->user();
        $friendId = $request->friendId;

        $friendship = $user->friendship($friendId);

        if (!$friendship || $friendship->status !== Friendship::STATUS_ACCEPTED)
            throw new \InvalidArgumentException('Пользователь не в друзьях');

        $friendship->update([
            'status' => 'declined',
            'initiator_id' => $friendship->user_id === $user->id
                ? $friendId
                : $user->id,
        ]);
    }

    public function getRequests(ValidateRequestsGetRequest $request)
    {
        $user = auth()->user();
        $requestType = $request->type ?? 'pending';

        $friendships = match ($requestType)
        {
            'pending' => $user->friendships()
                ->where('status', $requestType)
                ->where('initiator_id', '!=', $user->id)
                ->get()
                ->map(function ($friendship) use ($user) {
                    return $this->getFriendId($friendship);
                })->toArray(),
            'pending_me' => $user->friendships()
                ->where('status', $requestType)
                ->where('initiator_id', $user->id)
                ->get()
                ->map(function ($friendship) use ($user) {
                    return $this->getFriendId($friendship, false);
                })->toArray(),
            'blocked' => $user->friendships()
                ->where('status', $requestType)
                ->where(function ($query) use ($requestType) {
                    $query->where('status', $requestType)
                        ->where(function ($q) {
                            $q->where('block_type', Friendship::BLOCK_FRIEND)
                                ->orWhere('block_type', Friendship::BLOCK_MUTUAL);
                        });
                })
                ->get()
                ->map(function ($friendship) use ($user) {
                    return $this->getFriendId($friendship);
                })->toArray(),
            'blocked_me' => $user->friendships()
                ->where('status', 'blocked')
                ->where(function ($q) {
                    $q->where('block_type', Friendship::BLOCK_BY_FRIEND)
                        ->orWhere('block_type', Friendship::BLOCK_MUTUAL);
                })
                ->get()
                ->map(function ($friendship) use ($user) {
                    return $this->getFriendId($friendship, false);
                })->toArray(),
            'declined' => $user->friendships()
                ->where('status', $requestType)
                ->get()
                ->map(function ($friendship) use ($user) {
                    return $friendship->initiator_id !== $user->id
                        ? $this->getFriendId($friendship)
                        : null;
                })
                ->filter()
                ->toArray(),
            default => collect([])
        };

        return $this->userService->getUsersDataForListById($friendships);
    }

    /**
     * Получить id друга в отношении friendships
     *
     * @param \App\Models\Friendship $friendship Объект отношения
     * @param bool $getFriendId При false возвращает id пользователя, при true - id друга
     * @return string|null
     */
    protected function getFriendId(Friendship $friendshipRow, bool $getFriendId = true): string
    {
        return $friendshipRow->user_id === auth()->user()->id
            ? ($getFriendId ? $friendshipRow->friend_id : $friendshipRow->user_id)
            : ($getFriendId ? $friendshipRow->user_id : $friendshipRow->friend_id);
    }
}
