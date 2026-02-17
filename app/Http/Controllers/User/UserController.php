<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateLearnerPostRequest;
use App\Http\Requests\User\UpdatePasswordPostRequest;
use App\Http\Requests\User\UploadPhotoPostRequest;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show()
    {
        $user = auth()->user();

        return response()->json([
            'username' => $user->username,
            'lastName' => $user->personalData->last_name ?? null,
            'firstName' => $user->personalData->first_name ?? null,
            'middleName' => $user->personalData->middle_name ?? null,
            'role' => $user->role,
            'img' => $user->img ? Storage::path($user->img) : null,
        ]);
    }

    public function uploadPhoto(UploadPhotoPostRequest $request)
    {
        try
        {
            $user = Auth::user();

            if ($user->img)
            {
                $oldImagePath = str_replace('/storage', 'public', $user->img);
                Storage::delete($oldImagePath);
            }

            $file = $request->file('photo');

            $extension = $file->getClientOriginalExtension();
            $fileName = 'avatar.' . $extension;

            $path = $file->storeAs(
                "users/{$user->id}/photos",
                $fileName,
                'public'
            );

            $user->img = $path;
            $user->save();
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Фото успешно загружено',
                'image_url' => Storage::path($user->img),
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при загрузке фото',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function updatePassword(UpdatePasswordPostRequest $request)
    {
        try
        {
            $this->userService->updatePassword($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createLearner(CreateLearnerPostRequest $request)
    {
        try
        {
            $this->userService->createLearner($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
