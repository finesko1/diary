<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalData\UpdateCityPostRequest;
use App\Http\Requests\PersonalData\UpdateDateOfBirthPostRequest;
use App\Http\Requests\PersonalData\UpdateEmailPostRequest;
use App\Http\Requests\PersonalData\UpdateFullNamePostRequest;
use App\Http\Requests\PersonalData\UpdateUsernamePostRequest;
use App\Models\User\User;
use App\Services\User\PersonalDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PersonalDataController extends Controller
{
    protected $personalDataService;

    public function __construct(PersonalDataService $personalDataService)
    {
        $this->personalDataService = $personalDataService;
    }

    public function getFullName(Request $request)
    {
        try
        {
            $request->validate(['userId' => 'uuid|exists:users,id']);

            if (!$request->userId)
                return response()->json(optional(auth()->user()->personalData)->getFullName());
            else
                return response()->json(optional(User::find($request->userId)->personalData)->getFullName());
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }
    }

    public function updateDateOfBirth(UpdateDateOfBirthPostRequest $request)
    {
        $this->personalDataService->updateDateOfBirth($request);

        return response()->json(['success' => true]);
    }

    public function updateFullName(UpdateFullNamePostRequest $request)
    {
        $this->personalDataService->updateFullName($request);

        return response()->json(['success' => true]);
    }

    public function updateEmail(UpdateEmailPostRequest $request)
    {
        try {
            $this->personalDataService->updateEmail($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function updateUsername(UpdateUsernamePostRequest $request)
    {
        try
        {
            $this->personalDataService->updateUsername($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json($e->getMessage(), 400);
        }
    }
}
