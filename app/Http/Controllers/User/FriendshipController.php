<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Friendship\FriendRequest;
use App\Http\Requests\Friendship\ValidateRequestsGetRequest;
use App\Services\User\FriendshipService;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    protected $friendshipService;

    public function __construct(FriendshipService $friendshipService)
    {
        $this->friendshipService = $friendshipService;
    }

    public function index()
    {
        try
        {
            $friends = $this->friendshipService->index();

            return response()->json(['users' => $friends]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(FriendRequest $request)
    {
        try
        {
            $friend = $this->friendshipService->show($request);

            return response()->json($friend);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function block(FriendRequest $request)
    {
        try
        {
            $this->friendshipService->block($request);
            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function unblock(FriendRequest $request)
    {
        try
        {
            $this->friendshipService->unblock($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete(FriendRequest $request)
    {
        try
        {
            $this->friendshipService->delete($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getRequests(ValidateRequestsGetRequest $request)
    {
        try
        {
            // подгрузить и личные данные
            $users = $this->friendshipService->getRequests($request);

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sendRequest(FriendRequest $request)
    {
        try
        {

        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function acceptRequest(FriendRequest $request)
    {
        try
        {

        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function declineRequest(FriendRequest $request)
    {
        try
        {

        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
