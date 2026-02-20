<?php

namespace App\Http\Controllers\Subject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\DayEventsGetRequest;
use App\Http\Requests\Subject\EventTopicsGetRequests;
use App\Http\Requests\Subject\MonthEventsGetRequest;
use App\Http\Requests\Subject\UserTopicAssignmentsGetRequets;
use App\Http\Requests\UserTopic\LessonDeleteRequest;
use App\Models\Subject\UserTopic;
use App\Services\Subject\UserTopicService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserTopicController extends Controller
{
    protected $userTopicService;

    public function __construct(UserTopicService $userTopicService)
    {
        $this->userTopicService = $userTopicService;
    }

    public function getMonthEvents(MonthEventsGetRequest $request)
    {
        try
        {
            $topicsOnMonth = $this->userTopicService->getMonthEvents($request);

            return response()->json(['monthEvents' => $topicsOnMonth]);
        }
        catch (\InvalidArgumentException $exception)
        {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function getDayEvents(DayEventsGetRequest $request)
    {
        try
        {
            $dayEvents = $this->userTopicService->getDayEvents($request);

            return response()->json(['dayEvents' => $dayEvents]);
        }
        catch (\InvalidArgumentException $exception)
        {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function getEventTopics(EventTopicsGetRequests $request)
    {
        try
        {
            $response = $this->userTopicService->getEventTopics($request);

            return response()->json($response);
        }
        catch (\InvalidArgumentException $exception)
        {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function getUserTopicAssignments(UserTopicAssignmentsGetRequets $request)
    {
        try
        {
            $response = $this->userTopicService->getUserTopicAssignments($request);

            return response()->json([
                'success' => true,
                'assignments' => $response['assignments'],
                'attachments' => $response['attachments'],
            ]);
        }
        catch (\InvalidArgumentException $exception)
        {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function deleteLesson(LessonDeleteRequest $request)
    {
        $lesson = $this->userTopicService->deleteLesson($request);

        return response()->json([
            'success' => true,
            'lesson' => $lesson
        ]);
    }
}
