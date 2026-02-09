<?php

namespace App\Services\User;


use App\Http\Requests\PersonalData\UpdateCityPostRequest;
use App\Http\Requests\PersonalData\UpdateDateOfBirthPostRequest;
use App\Http\Requests\PersonalData\UpdateEmailPostRequest;
use App\Http\Requests\PersonalData\UpdateFullNamePostRequest;
use App\Http\Requests\PersonalData\UpdateUsernamePostRequest;
use App\Models\User\PersonalData;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PersonalDataService
{
    public function getData($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $personalData = optional(User::find($userId))->personalData;
        $personalData = [
            'email' => Auth::user()->email,
            'firstName' => $personalData->first_name ?? null,
            'lastName' => $personalData->last_name ?? null,
            'middleName' => $personalData->middle_name ?? null,
            'dateOfBirth' => $personalData && $personalData->date_of_birth
                ? Carbon::parse($personalData->date_of_birth)->format('d-m-Y')
                : null,
            'username' => auth()->user()->username ?? null,
        ];

        if (!$personalData)
            throw new \InvalidArgumentException('Персональные данные не найдены');

        return $personalData;
    }

    public function updateDateOfBirth(UpdateDateOfBirthPostRequest $request)
    {
        try {
            $user = Auth::user();

            PersonalData::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => $request->dateOfBirth
                ]
            );
        }
        catch (\Exception $exception)
        {
            throw new \InvalidArgumentException($exception->getMessage());
        }
    }

    public function updateFullName(UpdateFullNamePostRequest $request)
    {
        try {
            PersonalData::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'last_name' => $request->lastName,
                    'first_name' => $request->firstName,
                    'middle_name' => $request->middleName,
                ]
            );
        }
        catch (\Exception $exception)
        {
            throw new \InvalidArgumentException($exception->getMessage());
        }
    }

    public function updateEmail(UpdateEmailPostRequest $request)
    {
        try {
            auth()->user()->updateOrCreate(['id' => Auth::id()],[
                'email' => $request->email,
            ]);
        }
        catch (\Exception $exception)
        {
            throw new \InvalidArgumentException($exception->getMessage());
        }
    }

    public function updateUsername(UpdateUsernamePostRequest $request)
    {
        try
        {
            auth()->user()->update([
                'username' => $request->username
            ]);
        }
        catch (\InvalidArgumentException $exception)
        {
            throw new \InvalidArgumentException($exception->getMessage());
        }
    }
}
