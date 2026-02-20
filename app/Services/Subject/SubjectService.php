<?php

namespace App\Services\Subject;

use App\Exceptions\ApiException;
use App\Http\Requests\Subject\AddAssignmentInTopicPostRequest;
use App\Http\Requests\Subject\AddAttachmentInAssignmentPostRequest;
use App\Http\Requests\Subject\AssignmentDeleteRequest;
use App\Http\Requests\Subject\AssignmentTypeDeleteRequest;
use App\Http\Requests\Subject\AssignmentTypesGetRequest;
use App\Http\Requests\Subject\CreateAssignmentPostRequest;
use App\Http\Requests\Subject\CreateAssignmentTypePostRequest;
use App\Http\Requests\Subject\CreateSubjectPostRequest;
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
use App\Http\Requests\UserTopic\RemoveAttachmentInUserTopicDeleteRequest;
use App\Models\Subject\Assignment;
use App\Models\Subject\AssignmentType;
use App\Models\Subject\Lesson;
use App\Models\Subject\Subject;
use App\Models\Subject\SubjectLevel;
use App\Models\Subject\Topic;
use App\Models\Subject\UserTopic;
use App\Models\Subject\UserTopicAssignment;
use App\Models\User\User;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $query = SubjectLevel::where('user_id', $request->user_id)
                ->where('evaluated_by', auth()->user()->id)
                ->where('subject_id', $request->subject_id);

            if (!$query->first()) {
                SubjectLevel::create([
                    'user_id' => $request->user_id,
                    'evaluated_by' => auth()->user()->id,
                    'subject_id' => $request->subject_id,
                    'level' => ''
                ]);
            }

            $topics = $request->topics;
            $lesson = Lesson::create([
                'subject_id' => $request->subject_id,
                'teacher_id' => auth()->user()->id,
                'student_id' => $request->user_id,
                'date' => Carbon::parse($request->datetime),
            ]);
            if (!$topics)
            {
                UserTopic::create([
                    'lesson_id' => $lesson->id,
                    'topic_id' => null,
                ]);

                DB::commit();
                return;
            }

            foreach ($topics as $event) {
                // создаем тему согласно предмету при необходимости
                $topic = null;
                if (isset($event['topic_id']) && $event['topic_id'])
                {
                    $topic = Topic::where('id', $event['topic_id'])
                        ->where('user_id', auth()->user()->id)->first();
                }

                if (!$topic && !empty($event['topic_name']))
                {
                    $topic = Topic::where('name', $event['topic_name'])
                        ->where('user_id', auth()->user()->id)->first();

                    if (!$topic)
                    {
                        $topic = Topic::create([
                            'name' => $event['topic_name'],
                            'subject_id' => $request->subject_id,
                            'user_id' => auth()->user()->id,
                            'description' => $event['topic_description'] ?? null,
                        ]);
                    }
                    else
                    {
                        $topic->update([
                            'name' => $event['topic_name'],
                            'description' => $event['topic_description'],
                        ]);
                    }
                }

                $userTopic = UserTopic::create([
                    'lesson_id' => $lesson->id,
                    'topic_id' => $topic->id ?? null,
                ]);

                // создаем записи заданий к теме
                $assignments = $event['assignments'] ?? collect([]);

                foreach ($assignments as $task)
                {
                    $assignmentType = null;

                    if (isset($task['assignment_id']) && $task['assignment_id'])
                    {
                        $assignmentType = AssignmentType::where('id', $task['assignment_id'])
                            ->where('user_id', auth()->user()->id)->first();
                    }

                    if (!$assignmentType && !empty($task['assignment_name']))
                    {
                        $assignmentType = AssignmentType::where('user_id', auth()->user()->id)
                            ->where('name', $task['assignment_name'])->first();

                        if (!$assignmentType)
                        {
                            $assignmentType = AssignmentType::create([
                                'name' => $task['assignment_name'],
                                'user_id' => auth()->user()->id,
                                'description' => $task['assignment_name'] ?? null,
                            ]);
                        }
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
            throw new \InvalidArgumentException($e->getMessage());
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

    public function addAssignmentInTopic(AddAssignmentInTopicPostRequest $request)
    {
        DB::beginTransaction();

        $data = [
            'assignment_type_id' => $request->assignment_type_id ?? null,
            'description' => $request->description ?? null,
            'mark' => $request->mark ?? null,
        ];

        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        $assignment = Assignment::create($data);

        UserTopicAssignment::create([
            'user_topic_id' => $request->user_topic_id,
            'assignment_id' => $assignment->id,
        ]);

        DB::commit();
    }

    public function addAttachmentInUserTopic(AddAttachmentInUserTopicPostRequest $request): array
    {
        DB::beginTransaction();

        $user = auth()->user();

        $lesson = Lesson::find($request->lesson_id);

        throw_if(($user->isLearner() && ($lesson->student_id !== $user->id)) ||
            (!$user->isLearner() && ($lesson->teacher_id !== $user->id)),
            new ApiException('Недоступно', 403)
        );

        $userTopic = UserTopic::where(function ($query) use ($request, $lesson) {
            $query->where('id', $request->user_topic_id)
                ->where('lesson_id', $lesson->id);
        })->first();

        throw_if(!$userTopic,
            new ApiException('Не существует занятия для урока', 404)
        );

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Определяем путь для сохранения
        $directory = "/lessons/{$lesson->id}/user_topics/{$userTopic->id}/";

        // Сохраняем файл в storage
        $path = Storage::disk('public')->putFile($directory, $file);

        $userTopic->files()->create([
            'disk' => 'public',
            'path' => $path,
            'original_name' => $originalName,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => $extension,
            'type' => app(FileService::class)->determineType($file->getMimeType()),
            'user_id' => $user->id,
        ]);

        DB::commit();

        return [];
    }

    public function delAttachmentInUserTopic(RemoveAttachmentInUserTopicDeleteRequest $request): array
    {
        $user = auth()->user();
        $lesson = Lesson::find($request->lesson_id);

        throw_if($user->isLearner() && $user->id !== $lesson->student_id ||
            !$user->isLearner() && $user->id !== $lesson->teacher_id,
            new ApiException('Недоступно', 403)
        );

        $userTopic = UserTopic::where('lesson_id', $lesson->id)
            ->where('id', $request->user_topic_id)->first();

        throw_if(!$userTopic,
            new ApiException('Не существует занятия для урока', 404)
        );

        $file = $userTopic->files()->where([
            ['user_id', $user->id],
            ['id', $request->attachment_id]
        ])->first();

        throw_if(!$file,
            new ApiException('Файла не существует', 404)
        );

        Storage::disk('public')->delete($file->path);

        $file->delete();

        return [
            'id' => $file->id,
        ];
    }

    public function addAttachmentInAssignment(AddAttachmentInAssignmentPostRequest $request)
    {
        DB::beginTransaction();
        $savedFilePath = null;

        try
        {
            $lesson = Lesson::find($request->lesson_id);
            $user = auth()->user();

            if ($user->isLearner() && $user->id !== $lesson->student_id)
            {
                throw new ApiException('Недоступно', 403);
            }
            else if (!$user->isLearner() && $user->id !== $lesson->teacher_id)
            {
                throw new ApiException('Недоступно', 403);
            }

            $assignment = Assignment::find($request->assignment_id);
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Определяем путь для сохранения
            $directory = "/lessons/{$lesson->id}/assignments/{$assignment->id}";

            // Сохраняем файл в storage
            $path = Storage::disk('public')->putFile($directory, $file);
            $savedFilePath = $path;

            // Создаем запись в БД
            $newFile = $assignment->files()->create([
                'disk' => 'public',
                'path' => $path,
                'original_name' => $originalName,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $extension,
                'type' => app(FileService::class)->determineType($file->getMimeType()),
                'user_id' => $user->id,
            ]);

            DB::commit();
        }
        catch (\InvalidArgumentException $e)
        {
            DB::rollBack();

            if ($savedFilePath && Storage::disk('public')->exists($savedFilePath)) {
                Storage::disk('public')->delete($savedFilePath);
            }

            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    public function deleteAttachmentInAssignment(RemoveAttachmentInAssignmentDeleteRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $lesson = Lesson::find($request->lesson_id);
            $user = auth()->user();

            if ($user->isLearner() && $user->id !== $lesson->student_id)
            {
                throw new \InvalidArgumentException('Недоступно');
            }
            else if (!$user->isLearner() && $user->id !== $lesson->teacher_id)
            {
                throw new \InvalidArgumentException('Недоступно');
            }

            $assignment = Assignment::find($request->assignment_id);
            $attachment = $assignment->files->find($request->attachment_id);

            $attachment->delete();
            Storage::disk($attachment->disk)->delete($attachment->path);

            DB::commit();
        }
        catch (\InvalidArgumentException $e)
        {
            DB::rollBack();
            throw new \InvalidArgumentException($e->getMessage());
        }
    }
}
