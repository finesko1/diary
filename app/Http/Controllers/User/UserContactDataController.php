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
        $city = $this->contactDataService->updateCity($request);

        return response()->json([
            'success' => true,
            'city' => $city
        ]);
    }

    public function updateTelephone(UpdateTelephonePostRequest $request)
    {
        $telephone = $this->contactDataService->updateTelephone($request);

        return response()->json([
            'success' => true,
            'telephone' => $telephone
        ]);
    }

    public function updateWhatsApp(UpdateWhatsAppPostRequest $request)
    {
       $whatsApp = $this->contactDataService->updateWhatsApp($request);

       return response()->json([
           'success' => true,
           'whatsApp' => $whatsApp
       ]);
    }

    public function updateTelegram(UpdateTelegramPostRequest $request)
    {
        $telegram = $this->contactDataService->updateTelegram($request);

        return response()->json([
            'success' => true,
            'telegram' => $telegram
        ]);
    }

    public function updateVk(UpdateVkPostRequest $request)
    {
        $vk = $this->contactDataService->updateVk($request);

        return response()->json([
            'success' => true,
            'vk' => $vk
        ]);
    }

    public function updateCallsPlatform(UpdateCallsPlatformPostRequest $request)
    {
        $callsPlatform = $this->contactDataService->updateCallsPlatform($request);

        return response()->json([
            'success' => true,
            'callsPlatform' => $callsPlatform
        ]);
    }
}
