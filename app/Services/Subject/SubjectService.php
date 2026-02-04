<?php

namespace App\Services\Subject;

use App\Http\Requests\Subject\AssignmentDeleteRequest;
use App\Http\Requests\Subject\AssignmentTypeDeleteRequest;
use App\Http\Requests\Subject\AssignmentTypesGetRequest;
use App\Http\Requests\Subject\CreateAssignmentPostRequest;
use App\Http\Requests\Subject\CreateAssignmentTypePostRequest;
use App\Http\Requests\Subject\CreateSubjectPostRequest;
use App\Http\Requests\Subject\CreateTopicPostRequest;
use App\Http\Requests\Subject\CreateUserTopicAssignmentPostRequest;
use App\Http\Requests\Subject\CreateUserTopicPostRequest;
use App\Http\Requests\Subject\TopicDeleteRequest;
use App\Http\Requests\Subject\TopicsGetRequest;
use App\Http\Requests\Subject\UpdateAssignmentMarkPatchRequest;
use App\Http\Requests\Subject\UpdateAssignmentPutRequest;
use App\Http\Requests\Subject\UpdateUserTopicPutRequest;
use App\Http\Requests\Subject\UserTopicDeleteRequest;
use App\Models\Subject\Assignment;
use App\Models\Subject\AssignmentType;
use App\Models\Subject\Lesson;
use App\Models\Subject\Topic;
use App\Models\Subject\UserTopic;
use App\Models\Subject\UserTopicAssignment;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class SubjectService
{
    public function getTopics(TopicsGetRequest $request)
    {
        return Topic::where('subject_id', $request->subject_id)
            ->where('user_id', auth()->user()->id)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function deleteTopic(TopicDeleteRequest $request)
    {
        Topic::find($request->topic_id)
            ->where('id', $request->topic_id)
            ->where('user_id', auth()->user()->id)
            ->delete();
    }

    public function createAssignmentType(CreateAssignmentTypePostRequest $request)
    {
        try
        {
            $assignment = AssignmentType::create([
                'name' => $request->name,
                'user_id' => auth()->user()->id,
            ]);
            return [
                'id' => $assignment->id,
                'name' => $assignment->name,
            ];
        }
        catch (UniqueConstraintViolationException $e)
        {
            throw new \InvalidArgumentException('Тип задания уже существует', 400);
        }
    }

    public function getAssignmentTypes(AssignmentTypesGetRequest $request)
    {
        $assignmentTypes = AssignmentType::where('user_id', auth()->user()->id)->get();

        return $assignmentTypes->map(function ($assignmentType) {
            return [
                'id' => $assignmentType->id,
                'name' => $assignmentType->name,
            ];
        });
    }

    public function deleteAssignmentType(AssignmentTypeDeleteRequest $request)
    {
        AssignmentType::where('user_id', auth()->user()->id)
            ->where('id', $request->assignment_type_id)
            ->delete();
    }

    public function create(CreateSubjectPostRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $topics = $request->topics;

            if (!$topics)
            {
                UserTopic::create([
                    'subject_id' => $request->subject_id,
                    'teacher_id' => auth()->user()->id,
                    'student_id' => $request->user_id,
                    'topic_id' => null,
                    'date' => Carbon::parse($request->datetime),
                ]);

                DB::commit();
                return;
            }

            foreach ($topics as $event) {
                // создаем тему согласно предмету при необходимости
                $topic = Topic::where('id', $event['topic_id'])
                    ->where('user_id', auth()->user()->id)->first();

                if (!$topic && !empty($event['topic_name']))
                {
                    $topic = Topic::create([
                        'name' => $event['topic_name'],
                        'subject_id' => $request->subject_id,
                        'user_id' => auth()->user()->id,
                        'description' => $event['topic_name'] ?? null,
                    ]);
                }

                // прикрепляем тему к ученику + оценка + дата
                $userTopic = UserTopic::create([
                    'subject_id' => $request->subject_id,
                    'teacher_id' => auth()->user()->id,
                    'student_id' => $request->user_id,
                    'topic_id' => $topic->id ?? null,
                    'date' => Carbon::parse($request->datetime),
                ]);

                // создаем записи заданий к теме
                $assignments = $event['assignments'] ?? collect([]);

                foreach ($assignments as $task)
                {
                    $assignmentType = AssignmentType::where('id', $task['assignment_id'])
                        ->where('user_id', auth()->user()->id)->first();

                    if (!$assignmentType && !empty($task['assignment_name']))
                    {
                        $assignmentType = AssignmentType::create([
                            'name' => $task['assignment_name'],
                            'user_id' => auth()->user()->id,
                            'description' => $task['assignment_name'] ?? null,
                        ]);
                    }

                    $assignment = Assignment::create([
                        'assignment_type_id' => $assignmentType->id ?? null,
                        'description' => $task['assignment_description'] ?? null,
                    ]);

                    UserTopicAssignment::create([
                        'user_topic_id' => $userTopic->id,
                        'assignment_id' => $assignment->id,
                    ]);
                }
            }

            DB::commit();
        }
        catch (ModelNotFoundException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException($e->getMessage());
        }
        catch (UniqueConstraintViolationException $e) {
            DB::rollBack();
            throw new \InvalidArgumentException("Данная запись существует");
        }
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
            UserTopic::create([
                'lesson_id' => $request->lesson_id,
                'topic_id' => $request->topic_id ?? null,
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

    public function deleteUserTopic(UserTopicDeleteRequest $request)
    {
        $userTopic = UserTopic::find($request->id);

        $lesson = Lesson::find($userTopic->lesson_id);

        if ($lesson->teacher_id !== auth()->user()->id)
            throw new \InvalidArgumentException('Недоступно', 400);

        $userTopic->delete();
    }

    public function updateUserTopic(UpdateUserTopicPutRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $userTopic = UserTopic::find($request->id);

            if ($request->topic_id) {
                $topic = Topic::findOrFail($request->topic_id);
                $topic->update([
                    'name' => $request->topic_name,
                    'description' => $request->topic_description,
                ]);
            } else {
                $topic = Topic::updateOrCreate([
                    'subject_id' => $request->subject_id,
                    'user_id' => auth()->user()->id,
                    'name' => $request->topic_name,
                ], [
                    'subject_id' => $request->subject_id,
                    'user_id' => auth()->user()->id,
                    'name' => $request->topic_name,
                    'description' => $request->topic_description,
                ]);
            }

            if ($topic->user_id !== auth()->user()->id)
                throw new \InvalidArgumentException('Недоступно', 400);

            $userTopic->update([
                'topic_id' => $topic->id ?? null,
            ]);

            DB::commit();
        }
        catch (ModelNotFoundException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    public function createAssignment(CreateAssignmentPostRequest $request)
    {
        Assignment::create([
            'assignment_type_id' => $request->assignment_type_id,
            'description' => $request->assignment_description
        ]);
    }

    public function updateAssignment(UpdateAssignmentPutRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $lesson = Lesson::find($request->lesson_id);
            $userTopic = UserTopic::find($request->user_topic_id);

            $assignment = Assignment::find($request->assignment_id);

            $assignment->update([
                'assignment_type_id' => $request->assignment_type_id,
                'description' => $request->description,
                'status' => $request->status,
                'mark' => $request->mark ?? null,
            ]);

            DB::commit();
        }
        catch (ModelNotFoundException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    public function deleteAssignment(AssignmentDeleteRequest $request)
    {
        $assignment = Assignment::find($request->assignment_id);
        $assignmentType = AssignmentType::find($assignment->assignment_type_id);

        if ($assignmentType->user_id !== auth()->user()->id)
            throw new \InvalidArgumentException('Недоступно');

        $assignment->delete();
    }

    public function updateAssignmentMark(UpdateAssignmentMarkPatchRequest $request)
    {
        $assignment = Assignment::find($request->assignment_id)->update([
            'mark' => $request->mark
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
            throw new \InvalidArgumentException("Тема {$topicId} не найдена");

        if ($topic->subject_id != $subjectId)
            throw new \InvalidArgumentException("Для данного предмета не существует указанной темы");
    }
}
