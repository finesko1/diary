<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileByIdGetRequest;
use App\Services\User\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function getProfileData()
    {
        return response()->json($this->profileService->getFullProfile());
    }

    public function getProfileDataById(ProfileByIdGetRequest $request)
    {
        return response()->json($this->profileService->getProfileDataById($request));
    }
}
