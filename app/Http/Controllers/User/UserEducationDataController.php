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
        $beginningOfTeaching = $this->educationDataService->updateBeginningOfTeaching($request);

        return response()->json([
            'success' => true,
            'beginningOfTeaching' => $beginningOfTeaching
        ]);
    }

    public function updateCourse(UpdateCoursePostRequest $request)
    {
        $course = $this->educationDataService->updateCourse($request);

        return response()->json([
            'success' => true,
            'course' => $course
        ]);
    }

    public function updateLanguageLevel(UpdateLanguageLevelPostRequest $request)
    {
        $response = $this->educationDataService->updateLanguageLevel($request);

        return response()->json([
            'success' => true,
            'subject_id' => $response['subject_id'],
            'level' => $response['level'],
            'evaluated_by' => $response['evaluated_by'],
        ]);
    }
}
