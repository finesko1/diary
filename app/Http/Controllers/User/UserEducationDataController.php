<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationData\UpdateBeginningOfTeachingPostRequest;
use App\Http\Requests\EducationData\UpdateCoursePostRequest;
use App\Http\Requests\EducationData\UpdateLanguageLevelPostRequest;
use App\Services\User\EducationDataService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserEducationDataController extends Controller
{
    protected $educationDataService;

    public function __construct(EducationDataService $educationDataService)
    {
        $this->educationDataService = $educationDataService;
    }

    public function updateBeginningOfTeaching(UpdateBeginningOfTeachingPostRequest $request)
    {
        try
        {
            $this->educationDataService->updateBeginningOfTeaching($request);

        }
        catch (\InvalidArgumentException $exception)
        {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function updateCourse(UpdateCoursePostRequest $request)
    {
        try
        {
            $this->educationDataService->updateCourse($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $exception)
        {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function updateLanguageLevel(UpdateLanguageLevelPostRequest $request)
    {
        try
        {
            $this->educationDataService->updateLanguageLevel($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $exception)
        {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
