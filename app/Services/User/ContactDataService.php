<?php

namespace App\Services\User;

use App\Http\Requests\ContactData\UpdateCallsPlatformPostRequest;
use App\Http\Requests\ContactData\UpdateTelegramPostRequest;
use App\Http\Requests\ContactData\UpdateTelephonePostRequest;
use App\Http\Requests\ContactData\UpdateVkPostRequest;
use App\Http\Requests\ContactData\UpdateWhatsAppPostRequest;
use App\Http\Requests\PersonalData\UpdateCityPostRequest;
use App\Models\User\User;
use App\Services\Phone\RussianPhoneService;
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

    public function updateCity(UpdateCityPostRequest $request)
    {
        auth()->user()->contactData->update([
            'city' => $request->city
        ]);
    }

    public function updateTelephone(UpdateTelephonePostRequest $request)
    {
        $telephone = $request->telephone;
        if(!empty($telephone))
            $telephone = $this->phoneService->phoneNumberToE164($telephone);


        auth()->user()->contactData->update([
            'telephone' => $telephone
        ]);
    }

    public function updateTelegram(UpdateTelegramPostRequest $request)
    {
        auth()->user()->contactData->update([
            'telegram' => $request->telegram
        ]);
    }

    public function updateWhatsApp(UpdateWhatsAppPostRequest $request)
    {
        $telephone = $this->phoneService->phoneNumberToE164($request->whatsapp);

        auth()->user()->contactData->update([
            'whatsapp' => $telephone
        ]);
    }

    public function updateVk(UpdateVkPostRequest $request)
    {
        auth()->user()->contactData->update([
            'vk' => $request->vk
        ]);
    }

    public function updateCallsPlatform(UpdateCallsPlatformPostRequest $request)
    {
        auth()->user()->contactData->update([
            'calls_platform' => $request->callsPlatform
        ]);
    }



}
