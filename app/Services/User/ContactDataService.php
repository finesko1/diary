<?php

namespace App\Services\User;

use App\Http\Requests\ContactData\UpdateCallsPlatformPostRequest;
use App\Http\Requests\ContactData\UpdateTelegramPostRequest;
use App\Http\Requests\ContactData\UpdateTelephonePostRequest;
use App\Http\Requests\ContactData\UpdateVkPostRequest;
use App\Http\Requests\ContactData\UpdateWhatsAppPostRequest;
use App\Http\Requests\PersonalData\UpdateCityPostRequest;
use App\Models\User\User;
use App\Models\User\UserContactData;
use App\Services\Phone\RussianPhoneService;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class ContactDataService
{
    protected $phoneService;

    public function __construct(RussianPhoneService $phoneService)
    {
        $this->phoneService = $phoneService;
    }

    public function getData($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $contactData = optional(User::find($userId))->contactData;

        if (!$contactData)
        {
            return ['error' => 'Контактные данные не найдены'];
        }

        return [
            'city' => $contactData->city,
            'telephone' => $contactData->telephone,
            'whatsapp' => $contactData->whatsapp,
            'vk' => $contactData->vk,
            'telegram' => $contactData->telegram,
            'callsPlatform' => $contactData->calls_platform,
        ];
    }

    public function updateCity(UpdateCityPostRequest $request): String
    {
        $user = Auth::user();

        return UserContactData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'city' => $request->city,
            ])
            ->refresh()
            ->city ?? '';
    }

    public function updateTelephone(UpdateTelephonePostRequest $request): String
    {
        $user = Auth::user();

        $telephone = '';

        if(!empty($request->telephone))
            $telephone = $this->phoneService->phoneNumberToE164($request->telephone);

        return UserContactData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'telephone' => $telephone
            ])
            ->refresh()
            ->telephone;
    }

    public function updateTelegram(UpdateTelegramPostRequest $request): String
    {
        $user = Auth::user();

        return UserContactData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'telegram' => $request->telegram
            ])
            ->refresh()
            ->telegram ?? '';
    }

    public function updateWhatsApp(UpdateWhatsAppPostRequest $request): String
    {
        $user = Auth::user();

        $telephone = '';

        if(!empty($request->telephone))
            $telephone = $this->phoneService->phoneNumberToE164($request->telephone);

        return UserContactData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'whatsapp' => $telephone
            ])
            ->refresh()
            ->whatsapp;
    }

    public function updateVk(UpdateVkPostRequest $request): String
    {
        $user = Auth::user();

        return UserContactData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'vk' => $request->vk
            ])
            ->refresh()
            ->vk ?? '';
    }

    public function updateCallsPlatform(UpdateCallsPlatformPostRequest $request): String
    {
        $user = Auth::user();

        return UserContactData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'calls_platform' => $request->callsPlatform
            ])
            ->refresh()
            ->calls_platform ?? '';
    }

}
