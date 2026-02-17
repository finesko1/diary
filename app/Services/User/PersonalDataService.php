<?php

namespace App\Services\User;


use App\Exceptions\ApiException;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PersonalDataService
{
    public function getData($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $user = User::find($userId);
        $personalData = optional(User::find($userId))->personalData;
        $personalData = [
            'email' => $user->email,
            'firstName' => $personalData->first_name ?? null,
            'lastName' => $personalData->last_name ?? null,
            'middleName' => $personalData->middle_name ?? null,
            'dateOfBirth' => $personalData && $personalData->date_of_birth
                ? Carbon::parse($personalData->date_of_birth)->format('d-m-Y')
                : null,
            'username' => auth()->user()->username ?? null,
            'img' => $user->img ? Storage::url($user->img) : null,
            'role' => $user->role
        ];

        throw_if(!$personalData,
            new ApiException('Персональные данные не найдены', 404)
        );

        return $personalData;
    }

    public function updateDateOfBirth(UpdateDateOfBirthPostRequest $request): String
    {
        $user = Auth::user();

        $dateOfBirth = PersonalData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'date_of_birth' => $request->dateOfBirth
            ])
            ->refresh()
            ->date_of_birth;

        return Carbon::parse($dateOfBirth)->format('d-m-Y');
    }

    public function updateFullName(UpdateFullNamePostRequest $request): array
    {
        $user = Auth::user();

        $personalData = PersonalData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'last_name' => $request->lastName,
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
            ]
        )->refresh();

        return [
            'firstName' => $personalData->first_name ?? null,
            'lastName' => $personalData->last_name ?? null,
            'middleName' => $personalData->middle_name ?? null,
        ];
    }

    public function updateEmail(UpdateEmailPostRequest $request): String
    {
        $user = Auth::user();

        return $user->update([
                'id' => $user->id
            ],
            [
                'email' => $request->email,
            ])
            ->refresh()
            ->email;
    }

    public function updateUsername(UpdateUsernamePostRequest $request): String
    {
        $user = Auth::user();

        return $user->update([
                'username' => $request->username
            ])
            ->refresh()
            ->username;
    }
}
