<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactData\UpdateCallsPlatformPostRequest;
use App\Http\Requests\ContactData\UpdateTelegramPostRequest;
use App\Http\Requests\ContactData\UpdateTelephonePostRequest;
use App\Http\Requests\ContactData\UpdateVkPostRequest;
use App\Http\Requests\ContactData\UpdateWhatsAppPostRequest;
use App\Http\Requests\PersonalData\UpdateCityPostRequest;
use App\Services\User\ContactDataService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class UserContactDataController extends Controller
{
    protected $contactDataService;

    public function __construct(ContactDataService $contactDataService)
    {
        $this->contactDataService = $contactDataService;
    }

    public function updateCity(UpdateCityPostRequest $request)
    {
        $this->contactDataService->updateCity($request);

        return response()->json(['success' => true]);
    }

    public function updateTelephone(UpdateTelephonePostRequest $request)
    {
        try
        {
            $this->contactDataService->updateTelephone($request);
            return response()->json(['success' => true]);
        }
        catch (InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateWhatsApp(UpdateWhatsAppPostRequest $request)
    {
        try
        {
            $this->contactDataService->updateWhatsApp($request);
            return response()->json(['success' => true]);
        }
        catch (InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateTelegram(UpdateTelegramPostRequest $request)
    {
        try
        {
            $this->contactDataService->updateTelegram($request);
            return response()->json(['success' => true]);
        }
        catch (InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateVk(UpdateVkPostRequest $request)
    {
        try
        {
            $this->contactDataService->updateVk($request);
            return response()->json(['success' => true]);
        }
        catch (InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateCallsPlatform(UpdateCallsPlatformPostRequest $request)
    {
        try
        {
            $this->contactDataService->updateCallsPlatform($request);
            return response()->json(['success' => true]);
        }
        catch (InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
