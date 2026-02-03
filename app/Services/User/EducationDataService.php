<?php

namespace App\Services\User;

use App\Http\Requests\EducationData\UpdateBeginningOfTeachingPostRequest;
use App\Http\Requests\EducationData\UpdateCoursePostRequest;
use App\Http\Requests\EducationData\UpdateLanguageLevelPostRequest;
use App\Models\Subject\SubjectLevel;
use App\Models\User\Friendship;
use App\Models\User\User;
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

    public function updateBeginningOfTeaching(UpdateBeginningOfTeachingPostRequest $request)
    {
        $user = auth()->user();
        if ($user->isTeacher())
        {
            $user->educationData()->updateOrCreate(
                ['user_id' => $user->id],
                ['beginning_of_teaching' => $request->beginningOfTeaching]
            );
        }
        else
        {
            throw new InvalidArgumentException("Доступно для учителей");
        }
    }

    public function updateCourse(UpdateCoursePostRequest $request)
    {
        $user = auth()->user();
        $course = $request->course;

        if (!$user->isLearner() || $user->isAdult())
            throw new InvalidArgumentException("Доступно только для учеников");

        $maxCourse = [
            User::ROLE_CHILDREN => 11,
            User::ROLE_STUDENT => 6
        ];

        if ($course <= 0 || $course > $maxCourse[$user->role])
            throw new InvalidArgumentException("Значение должно быть от 1 до " . $maxCourse[$user->role]);

        $user->educationData()->updateOrCreate(
            ['user_id' => $user->id],
            ['course' => $request->course]
        );
    }

    public function updateLanguageLevel(UpdateLanguageLevelPostRequest $request)
    {
        $user = auth()->user();

        $learner = User::find($request->user_id);

        if ($request->user_id !== $user->id)
        {
            $friendship = Friendship
                ::where([['user_id', $user->id], ['friend_id', $learner->id]])
                ->orWhere([['user_id', $learner->id], ['friend_id', $user->id]])->first();

            if (!$user->isTeacher() || !($friendship->status === 'accepted'))
                throw new \InvalidArgumentException('Доступно только учителям пользователя');
        }

        $learner->subjectLevels()->updateOrCreate(
            [
                'user_id' => $learner->id,
                'subject_id' => $request->language_id,
            ],
            [
                'level' => $request->level,
                'evaluated_by' => $user->id,
            ]
        );

    }

}
