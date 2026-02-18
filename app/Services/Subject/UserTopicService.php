<?php

namespace App\Services\Subject;

use App\Http\Requests\Subject\DayEventsGetRequest;
use App\Http\Requests\Subject\EventTopicsGetRequests;
use App\Http\Requests\Subject\MonthEventsGetRequest;
use App\Http\Requests\Subject\UserTopicAssignmentsGetRequets;
use App\Models\Subject\Assignment;
use App\Models\Subject\AssignmentAttachment;
use App\Models\Subject\AssignmentType;
use App\Models\Subject\Lesson;
use App\Models\Subject\Topic;
use App\Models\Subject\UserTopic;
use App\Models\Subject\UserTopicAssignment;
use App\Models\User\User;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UserTopicService
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getMonthEvents(MonthEventsGetRequest $request)
    {
        if ($request->user_id && auth()->user()->isLearner())
            throw new \InvalidArgumentException('Недоступно');

        $dayOfMonth = $request->date
            ? Carbon::parse($request->date)
            : Carbon::now();

        $user = auth()->user();

        $startOfMonthDay = $dayOfMonth->copy()->startOfMonth();
        $endOfMonthDay = $dayOfMonth->copy()->endOfMonth();

        if (!$user->isLearner()) {
            $lessons = $request->user_id
                ? Lesson::where('student_id', $request->user_id)
                    ->whereBetween('date', [$startOfMonthDay, $endOfMonthDay])
                    ->orderBy('date')
                    ->get()
                : Lesson::where('teacher_id', $user->id)
                    ->whereBetween('date', [$startOfMonthDay, $endOfMonthDay])
                    ->orderBy('date')
                    ->get();
        } else {
            $lessons = Lesson::where('student_id', $user->id)
                ->whereBetween('date', [$startOfMonthDay, $endOfMonthDay])
                ->orderBy('date')
                ->get();
        }

        return $lessons->map(function ($lesson) {
            return Carbon::parse($lesson->date)->format('d-m-Y');
        })->unique()->values();
    }

    public function getDayEvents(DayEventsGetRequest $request)
    {
        if ($request->user_id && auth()->user()->isLearner())
            throw new \InvalidArgumentException('Недоступно');

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $user = $request->user_id
            ? User::find($request->user_id)
            : auth()->user();

        $lessons = Lesson::whereDate('date', $date)
            ->orderBy('date')
            ->where(function ($query) use ($user) {
                if ($user->isLearner())
                    $query->where('student_id', $user->id);
                else
                    $query->where('teacher_id', $user->id);
            })
            ->get();

        $response = collect();
        foreach ($lessons as $lesson)
        {
            $subjectId = $lesson->subject_id;
            $time = Carbon::parse($lesson->date)->format('H:i');

            $cUser = auth()->user()->isLearner()
                ? $this->userService->getUsersDataForListById($lesson->teacher_id)[0]
                : $this->userService->getUsersDataForListById($lesson->student_id)[0];

            $userTopics = UserTopic::where('lesson_id', $lesson->id)->get()
                ->map(function ($userTopic) {
                    $topic = Topic::find($userTopic->topic_id);
                    return [
                        'id' => $userTopic->id,
                        'topic_id' => $topic->id ?? null,
                        'name' => $topic->name ?? null,
                        'description' => $topic->description ?? null,
                        'mark' => $userTopic->mark,
                    ];
                });

            $userTopicsWithTasks = $userTopics->map(function ($topic) {
                $assignmentIds = UserTopicAssignment::where('user_topic_id', $topic['id'])->get()
                    ->pluck('assignment_id');

                $assignments = Assignment::whereIn('id', $assignmentIds)->get();

                $tasks = $assignments->map(function ($assignment) {
                    $assignmentType = AssignmentType::find($assignment->assignment_type_id);

                    return [
                        "name" => $assignmentType->name ?? null,
                        "description" => $assignment->description,
                        "status" => $assignment->status,
                        "mark" => $assignment->mark,
                    ];
                })->toArray();

                $topic['tasks'] = $tasks;
                return $topic;
            });

            $response->push([
                'id' => $lesson->id,
                'subject_id' => $subjectId,
                'time' => $time,
                'user' => $cUser,
                'topics' => $userTopicsWithTasks,
            ]);
        }

        return $response;
    }

    public function getEventTopics(EventTopicsGetRequests $request)
    {
        $lesson = Lesson::find($request->lesson_id);
        $userTopics = UserTopic::where('lesson_id', $lesson->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($userTopic) use ($request) {
                return [
                    'name' => Topic::find($userTopic->topic_id)->name ?? null,
                    'description' => Topic::find($userTopic->topic_id)->description ?? null,
                    'user_topic_id' => $userTopic->id,
                ];
            })->filter();

        return [
            'topics' => $userTopics,
            'subject_id' => $lesson->subject_id,
            'date' => Carbon::parse($lesson->date)->format('d-m-Y'),
            'time' => Carbon::parse($lesson->date)->format('H:i'),
        ];
    }

    public function getUserTopicAssignments(UserTopicAssignmentsGetRequets $request)
    {
        $lesson = Lesson::find($request->lesson_id);
        $userTopic = UserTopic::find($request->user_topic_id);

        $userTopicAssignments = UserTopicAssignment::where('user_topic_id', $userTopic->id)->get();

        $assignments = $userTopicAssignments->map(function ($userTopicAssignment) {
            $assignment = Assignment::find($userTopicAssignment->assignment_id);
            $assignmentAttachments = $assignment->files()->get();

            $teacherFiles = $assignmentAttachments->filter(function ($file) {
                return $file->user && $file->user->role === User::ROLE_TEACHER;
                })
                ->filter(function ($file) {
                    return $file->type === 'file';
                })
                ->map(function ($assignmentAttachment) {
                return [
                    'id' => $assignmentAttachment->id,
                    'originalName' => $assignmentAttachment->original_name,
                    'url' => $assignmentAttachment->path,
                ];
            });
            $teacherPhotos = $assignmentAttachments->filter(function ($file) {
                return $file->user && $file->user->role === User::ROLE_TEACHER;
            })
                ->filter(function ($file) {
                    return $file->type === 'image';
                })
                ->map(function ($assignmentAttachment) {
                    return [
                        'id' => $assignmentAttachment->id,
                        'originalName' => $assignmentAttachment->original_name,
                        'url' => Storage::disk('public')->url($assignmentAttachment->path),
                    ];
                });
            $teacherVideos = $assignmentAttachments->filter(function ($file) {
                return $file->user && $file->user->role === User::ROLE_TEACHER;
                })
                ->filter(function ($file) {
                    return $file->type === 'video';
                })
                ->map(function ($assignmentAttachment) {
                    return [
                        'id' => $assignmentAttachment->id,
                        'originalName' => $assignmentAttachment->original_name,
                        'url' => Storage::disk('public')->url($assignmentAttachment->path),
                    ];
                });

            $learnerFiles = $assignmentAttachments->filter(function ($file) {
                    return $file->user && $file->user->isLearner();
                })
                ->filter(function ($file) {
                    return $file->type === 'file';
                })
                ->map(function ($assignmentAttachment) {
                    return [
                        'id' => $assignmentAttachment->id,
                        'originalName' => $assignmentAttachment->original_name,
                        'url' => Storage::disk('public')->url($assignmentAttachment->path),
                    ];
                });
            $learnerPhotos = $assignmentAttachments->filter(function ($file) {
                    return $file->user && $file->user->isLearner();
                })
                ->filter(function ($file) {
                    return $file->type === 'image';
                })
                ->map(function ($assignmentAttachment) {
                    return [
                        'id' => $assignmentAttachment->id,
                        'originalName' => $assignmentAttachment->original_name,
                        'url' => Storage::disk('public')->url($assignmentAttachment->path),
                    ];
                });
            $learnerVideos = $assignmentAttachments->filter(function ($file) {
                    return $file->user && $file->user->isLearner();
                })
                ->filter(function ($file) {
                    return $file->type === 'video';
                })
                ->map(function ($assignmentAttachment) {
                    return [
                        'id' => $assignmentAttachment->id,
                        'originalName' => $assignmentAttachment->original_name,
                        'url' => Storage::disk('public')->url($assignmentAttachment->path),
                    ];
                });

            return [
                'id' => $assignment->id,
                'type_id' => $assignment->assignment_type_id,
                'type' => AssignmentType::find($assignment->assignment_type_id)->name ?? null,
                'description' => $assignment->description ?? null,
                'status' => $assignment->status ?? null,
                'attachments' => [
                    'teacherFiles' => $teacherFiles,
                    'teacherPhotos' => $teacherPhotos,
                    'teacherVideos' => $teacherVideos,
                    'learnerFiles' => $learnerFiles,
                    'learnerPhotos' => $learnerPhotos,
                    'learnerVideos' => $learnerVideos,
                ],
                'mark' => $assignment->mark,
            ];
        });

        return $assignments;
    }
}
