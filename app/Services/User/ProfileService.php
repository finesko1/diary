<?php

namespace App\Services\User;

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
        try
        {
            $userId = $userId ?? auth()->id();

            return [
                'personalData' => $this->personalDataService->getData($userId),
                'educationData' => $this->educationDataService->getData($userId),
                'contactData' => $this->contactDataService->getData($userId),
            ];
        }
        catch (\Exception $exception)
        {
            throw new \InvalidArgumentException($exception->getMessage());
        }
    }

}
