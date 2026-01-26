<?php

namespace App\Services\User;


use App\Http\Requests\PersonalData\UpdateCityPostRequest;
use App\Http\Requests\PersonalData\UpdateDateOfBirthPostRequest;
use App\Http\Requests\PersonalData\UpdateEmailPostRequest;
use App\Http\Requests\PersonalData\UpdateFullNamePostRequest;
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
            'firstName' => $personalData->first_name,
            'lastName' => $personalData->last_name,
            'middleName' => $personalData->middle_name,
            'dateOfBirth' => $personalData->date_of_birth
                ? Carbon::parse($personalData->date_of_birth)->format('d-m-Y')
                : null,
        ];

        if (!$personalData)
            throw new \InvalidArgumentException('Персональные данные не найдены');

        return $personalData;
    }

    public function updateDateOfBirth(UpdateDateOfBirthPostRequest $request)
    {
        Auth::user()->personalData->update([
            'date_of_birth' => $request->dateOfBirth
        ]);
    }

    public function updateFullName(UpdateFullNamePostRequest $request)
    {
        Auth::user()->personalData->update([
            'user_id' => Auth::id(),
            'last_name' => $request->lastName,
            'first_name' => $request->firstName,
            'middle_name' => $request->middleName,
        ]);
    }

    public function updateEmail(UpdateEmailPostRequest $request)
    {
        auth()->user()->personalData->update([
            'user_id' => Auth::id(),
            'email' => $request->email,
        ]);
    }
}
