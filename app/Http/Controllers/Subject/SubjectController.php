<?php

namespace App\Http\Controllers\Subject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\AddAssignmentInTopicPostRequest;
use App\Http\Requests\Subject\AddAttachmentInAssignmentPostRequest;
use App\Http\Requests\Subject\AssignmentDeleteRequest;
use App\Http\Requests\Subject\AssignmentTypeDeleteRequest;
use App\Http\Requests\Subject\AssignmentTypesGetRequest;
use App\Http\Requests\Subject\CreateAssignmentTypePostRequest;
use App\Http\Requests\Subject\CreateSubjectPostRequest;
use App\Http\Requests\Subject\CreateAssignmentPostRequest;
use App\Http\Requests\Subject\CreateTopicPostRequest;
use App\Http\Requests\Subject\CreateUserTopicAssignmentPostRequest;
use App\Http\Requests\Subject\CreateUserTopicPostRequest;
use App\Http\Requests\Subject\RemoveAttachmentInAssignmentDeleteRequest;
use App\Http\Requests\Subject\TopicDeleteRequest;
use App\Http\Requests\Subject\TopicsGetRequest;
use App\Http\Requests\Subject\UpdateAssignmentMarkPatchRequest;
use App\Http\Requests\Subject\UpdateAssignmentPutRequest;
use App\Http\Requests\Subject\UpdateUserTopicPutRequest;
use App\Http\Requests\Subject\UserTopicDeleteRequest;
use App\Http\Requests\UserTopic\AddAttachmentInUserTopicPostRequest;
use App\Services\Subject\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function getTopics(TopicsGetRequest $request)
    {
        try
        {
            $topics = $this->subjectService->getTopics($request);

            return response()->json([
                'topics' => $topics
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteTopic(TopicDeleteRequest $request)
    {
        try
        {
            $this->subjectService->deleteTopic($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAssignmentTypes(AssignmentTypesGetRequest $request)
    {
        try
        {
            $assignmentTypes = $this->subjectService->getAssignmentTypes($request);

            return response()->json(['assignmentTypes' => $assignmentTypes]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteAssignmentType(AssignmentTypeDeleteRequest $request)
    {
        try
        {
            $this->subjectService->deleteAssignmentType($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function create(CreateSubjectPostRequest $request)
    {
        try
        {
            $this->subjectService->create($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
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

    public function deleteUserTopic(UserTopicDeleteRequest $request)
    {
        try
        {
            $this->subjectService->deleteUserTopic($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateUserTopic(UpdateUserTopicPutRequest $request)
    {
        try
        {
            $this->subjectService->updateUserTopic($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
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
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createAssignmentType(CreateAssignmentTypePostRequest $request)
    {
        try
        {
            $assignmentType = $this->subjectService->createAssignmentType($request);

            return response()->json(['success' => true, 'assignmentType' => $assignmentType]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateAssignment(UpdateAssignmentPutRequest $request)
    {
        try
        {
            $this->subjectService->updateAssignment($request);

            return response()->json(['success' => true]);
        }
        catch (\InvalidArgumentException $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteAssignment(AssignmentDeleteRequest $request)
    {
        try
        {
            $this->subjectService->deleteAssignment($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateAssignmentMark(UpdateAssignmentMarkPatchRequest $request)
    {
        try
        {
            $this->subjectService->updateAssignmentMark($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
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
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function addAssignmentInTopic(AddAssignmentInTopicPostRequest $request)
    {
        $this->subjectService->addAssignmentInTopic($request);

        return response()->json(['success' => true]);
    }

    public function addAttachmentInAssignment(AddAttachmentInAssignmentPostRequest $request)
    {
        try
        {
            $this->subjectService->addAttachmentInAssignment($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function addAttachmentInUserTopic(AddAttachmentInUserTopicPostRequest $request)
    {
        $response = $this->subjectService->addAttachmentInUserTopic($request);

        return response()->json([
            'success' => true,
            ...$response
        ]);
    }

    public function deleteAttachmentInAssignment(RemoveAttachmentInAssignmentDeleteRequest $request)
    {
        try
        {
            $this->subjectService->deleteAttachmentInAssignment($request);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


}
