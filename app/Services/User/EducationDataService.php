<?php

namespace App\Services\User;

use App\Exceptions\ApiException;
use App\Http\Requests\EducationData\UpdateBeginningOfTeachingPostRequest;
use App\Http\Requests\EducationData\UpdateCoursePostRequest;
use App\Http\Requests\EducationData\UpdateLanguageLevelByUserIdPostRequest;
use App\Http\Requests\EducationData\UpdateLanguageLevelPostRequest;
use App\Models\Subject\SubjectLevel;
use App\Models\User\Friendship;
use App\Models\User\User;
use App\Models\User\UserEducationData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class EducationDataService
{
    public function getData($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $user = User::find($userId);

        if (!$user)
            throw new InvalidArgumentException("Пользователь не найден");

        $educationData = $user->educationData;
        $userSubjectLevels = $user->subjectLevels;

        if (!$educationData)
        {
            return ['error' => 'Данные об образовании не найдены'];
        }
        else
        {
            $educationDataResponse = collect();

            // Поля для учителей
            if ($user->isTeacher()) {
                $educationDataResponse = $educationDataResponse->merge([
                    'beginning_of_teaching' => $educationData->beginning_of_teaching
                        ? Carbon::parse($educationData->beginning_of_teaching)->format('d-m-Y')
                        : null,
                    'friends' => $user->friends()->count(),
                ]);
            }

            // Поле для учеников
            if ($user->isLearner()) {
                $educationDataResponse = $educationDataResponse->merge([
                    'course' => $educationData->course ?? null,
                ]);
            }
        }

        $languages = $userSubjectLevels->mapWithKeys(function ($item) {
            return [
                $item->subject_id => $item->level,
            ];
        });

        $educationData = array_merge($educationDataResponse->toArray(), ['languages' => $languages->toArray()]);

        return $educationData;
    }

    public function updateBeginningOfTeaching(UpdateBeginningOfTeachingPostRequest $request): String
    {
        $user = auth()->user();

        throw_if(
            !$user->isTeacher(),
            new ApiException('Действие доступно только для учителей', 403)
        );

        $beginningOfTeaching = $educationData = UserEducationData::updateOrCreate(
            ['user_id' => $user->id],
            ['beginning_of_teaching' => $request->beginningOfTeaching]
        )
            ->refresh()
            ->beginning_of_teaching;

        return Carbon::parse($beginningOfTeaching)->format('d-m-Y');
    }

    public function updateCourse(UpdateCoursePostRequest $request): String
    {
        $user = auth()->user();
        $course = $request->course;

        if (!$user->isLearner() || $user->isAdult())
            throw new InvalidArgumentException("Доступно только для обучающихся");

        throw_if(!$user->isLearner() || $user->isAdult(),
            new ApiException("Доступно только для обучающихся", 403)
        );

        $maxCourse = [
            User::ROLE_CHILDREN => 11,
            User::ROLE_STUDENT => 6
        ];

        throw_if($course <= 0 || $course > $maxCourse[$user->role],
            new ApiException("Значение должно быть от 1 до " . $maxCourse[$user->role])
        );

        return UserEducationData::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
            'course' => $course
            ])
            ->refresh()
            ->course;
    }

    public function updateLanguageLevel(UpdateLanguageLevelPostRequest $request)
    {
        $teacher = auth()->user();
        $learner = User::find($request->user_id);

        if ($teacher->id !== $learner->id)
        {
            $friendship = Friendship
                ::where([['user_id', $teacher->id], ['friend_id', $learner->id]])
                ->orWhere([['user_id', $learner->id], ['friend_id', $teacher->id]])->first();

            throw_if(!$teacher->isTeacher() || !($friendship->status === 'accepted'),
                new ApiException('Доступно только учителям пользователя', 403)
            );
        }

        $subjectLevelRelationship = $learner->subjectLevels()->updateOrCreate(
            [
                'user_id' => $learner->id,
                'subject_id' => $request->language_id,
            ],
            [
                'level' => $request->level,
                'evaluated_by' => $teacher->id,
            ]
        )->refresh();

        return [
            'subject_id' => $subjectLevelRelationship->subject_id,
            'level' => $subjectLevelRelationship->level,
            'evaluated_by' => $learner->id,
        ];
    }


}
