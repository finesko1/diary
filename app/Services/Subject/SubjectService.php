<?php

namespace App\Services\Subject;

use App\Http\Requests\Subject\CreateAssignmentPostRequest;
use App\Http\Requests\Subject\CreateSubjectPostRequest;
use App\Http\Requests\Subject\CreateTopicPostRequest;
use App\Http\Requests\Subject\CreateUserTopicAssignmentPostRequest;
use App\Http\Requests\Subject\CreateUserTopicPostRequest;
use App\Models\Subject\Assignment;
use App\Models\Subject\AssignmentType;
use App\Models\Subject\Topic;
use App\Models\Subject\UserTopic;
use App\Models\Subject\UserTopicAssignment;
use App\Models\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class SubjectService
{
    public function create(CreateSubjectPostRequest $request)
    {
        DB::transaction(function () use ($request) {
            $topics = $request->all();

            foreach ($topics as $event) {
                // создаем тему согласно предмету при необходимости
                $topic = $event['topic_id']
                    ? Topic::updateOrCreate([
                        'id' => $event['topic_id'],
                        'name' => $event['topic_name'],
                        'subject_id' => $event['subject_id'],
                        'description' => $event['topic_description'],
                    ])
                    : Topic::create([
                        'subject_id' => $event['subject_id'],
                        'name' => $event['topic_name'],
                        'description' => $event['topic_description'],
                    ]);

                // прикрепляем тему к ученику + оценка + дата
                $userTopic = UserTopic::create([
                    'teacher_id' => auth()->user()->id,
                    'student_id' => $event['user_id'],
                    'topic_id' => $topic->id,
                    'date' => $event['date'] . ' ' . $event['time'],
                ]);

                // создаем записи заданий к теме
                $assignments = $event['assignments'] ?? collect([]);

                foreach ($assignments as $task)
                {
                    $assignmentType = $task['assignment_type_id']
                        ? AssignmentType::find($task['assignment_type_id'])
                        : AssignmentType::create(['name' => $task['assignment_name']]);

                    // status и mark
                    $assignment = Assignment::create([
                        'assignment_type_id' => $assignmentType->id,
                        'description' => $task['assignment_description'],
                    ]);

                    UserTopicAssignment::create([
                        'user_topic_id' => $userTopic->id,
                        'assignment_id' => $assignment->id,
                    ]);
                }
            }
        });
    }

    public function createTopic(CreateTopicPostRequest $request)
    {
        Topic::create([
            'subject_id' => $request->subject_id,
            'name' => $request->topic_name,
            'description' => $request->topic_description,
        ]);
    }

    public function createUserTopic(CreateUserTopicPostRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $this->isSubjectTopic($request->topic_id, $request->subject_id);

            if (!User::find($request->student_id)->isLearner())
                throw new ModelNotFoundException("Пользователь не является обучающимся");

            $friendship = auth()->user()->friends()->where(function ($q) use ($request) {
                $q->where('user_id', $request->student_id)
                    ->orWhere('friend_id', $request->student_id);
            })->first();

            if (!$friendship)
                throw new ModelNotFoundException("Пользователь не в друзьях");

            UserTopic::create([
                'teacher_id' => auth()->id(),
                'student_id' => $request->student_id,
                'topic_id' => $request->topic_id,
                'date' => $request->datetime
            ]);

            DB::commit();
        }
        catch (ModelNotFoundException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException($e->getMessage(), 404, $e);
        }
        catch (UniqueConstraintViolationException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException("Занятие уже существует");
        }
    }

    public function createAssignment(CreateAssignmentPostRequest $request)
    {
        Assignment::create([
            'assignment_type_id' => $request->assignment_type_id,
            'description' => $request->assignment_description
        ]);
    }

    public function createUserTopicAssignment(CreateUserTopicAssignmentPostRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $userTopic = UserTopic::find($request->user_topic_id);

            $this->isSubjectTopic($userTopic->topic_id, $request->subject_id);

            UserTopicAssignment::create([
                'user_topic_id' => $request->user_topic_id,
                'assignment_id' => $request->assignment_id,
            ]);

            DB::commit();
        }
        catch (ModelNotFoundException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException($e->getMessage(), 404, $e);
        }
        catch (UniqueConstraintViolationException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException("Занятие уже существует");
        }
    }



    protected function isSubjectTopic($topicId, $subjectId)
    {
        $topic = Topic::find($topicId);

        if (!$topic)
            throw new \InvalidArgumentException("Тема с ID {$topicId} не найдена");

        if ($topic->subject_id != $subjectId)
            throw new \InvalidArgumentException("Для данного предмета не существует указанной темы");
    }
}
