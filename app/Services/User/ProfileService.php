<?php

namespace App\Services\User;

use App\Http\Requests\Profile\ProfileByIdGetRequest;

class ProfileService
{
    protected $personalDataService;
    protected $educationDataService;
    protected $contactDataService;

    public function __construct(
        PersonalDataService $personalDataService,
        EducationDataService $educationDataService,
        ContactDataService $contactDataService
    ) {
        $this->personalDataService = $personalDataService;
        $this->educationDataService = $educationDataService;
        $this->contactDataService = $contactDataService;
    }

    public function getFullProfile($userId = null)
    {
        $userId = $userId ?? auth()->id();

        return [
            'personalData' => $this->personalDataService->getData($userId),
            'educationData' => $this->educationDataService->getData($userId),
            'contactData' => $this->contactDataService->getData($userId),
        ];
    }

    public function getProfileDataById(ProfileByIdGetRequest $request)
    {
        $userId = $request->user_id ? $request->user_id : auth()->id();

        return [
            'personalData' => $this->personalDataService->getData($userId),
            'educationData' => $this->educationDataService->getData($userId),
            'contactData' => $this->contactDataService->getData($userId),
        ];
    }

}
