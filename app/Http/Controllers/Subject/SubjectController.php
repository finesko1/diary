<?php

namespace App\Http\Controllers\Subject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\CreateSubjectPostRequest;
use App\Http\Requests\Subject\CreateAssignmentPostRequest;
use App\Http\Requests\Subject\CreateTopicPostRequest;
use App\Http\Requests\Subject\CreateUserTopicAssignmentPostRequest;
use App\Http\Requests\Subject\CreateUserTopicPostRequest;
use App\Services\Subject\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function create(CreateSubjectPostRequest $request)
    {
        try
        {
            $this->subjectService->create($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function createTopic(CreateTopicPostRequest $request)
    {
        try
        {
            $this->subjectService->createTopic($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function createUserTopic(CreateUserTopicPostRequest $request)
    {
        try
        {
            $this->subjectService->createUserTopic($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function createAssignment(CreateAssignmentPostRequest $request)
    {
        try
        {
            $this->subjectService->createAssignment($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function createUserTopicAssignment(CreateUserTopicAssignmentPostRequest $request)
    {
        try
        {
            $this->subjectService->createUserTopicAssignment($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


}
