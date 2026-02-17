<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\Profile\AddMaterialPostRequest;
use App\Http\Requests\Profile\MaterialDeleteRequest;
use App\Http\Requests\Profile\MaterialPatchRequest;
use App\Http\Requests\Profile\MaterialsGetRequest;
use App\Models\File;
use App\Models\ProfileMaterial;
use App\Models\User\Friendship;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;

class ProfileMaterialsService
{
    public function addMaterial(AddMaterialPostRequest $request): array
    {
        DB::beginTransaction();
        $teacher = auth()->user();

        $friendshipRelationship = Friendship::where([
            ['user_id', $teacher->id],
            ['friend_id', $request->user_id]
        ])->orWhere([
            ['user_id', $request->user_id],
            ['friend_id', $teacher->id]
        ])->first();

        if (!$friendshipRelationship || $friendshipRelationship->status !== Friendship::STATUS_ACCEPTED || $teacher->isLearner()) {
            DB::rollBack();
            throw new ApiException('Недоступно', 403);
        }

        // Создание материала по предмету от учителя на странице пользователя
        $profileMaterial = ProfileMaterial::create([
            'user_id' => $request->user_id,
            'subject_id' => $request->subject_id,
            'description' => $request->description,
        ]);

        // Добавление записи о файле в БД
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $filepath = 'users/' . $request->user_id . '/profile/materials/' . $teacher->id;

        $fullPath = Storage::disk('public')->putFile($filepath, $file);

        throw_if(!$fullPath,
            new ApiException('Ошибка сохранения', 500)
        );

        $fileRelationship = $profileMaterial->files()->create([
            'disk' => 'public',
            'path' => $filepath,
            'original_name' => $originalName,
            'filename' => basename($fullPath),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'type' => app(FileService::class)->determineType($file->getMimeType()),
            'user_id' => $teacher->id,
        ]);

        DB::commit();

        return [
            'id' => $profileMaterial->id,
            'subjectId' => $profileMaterial->subject_id,
            'description' => $profileMaterial->description,
            'url' => Storage::url($fileRelationship->path . '/' . $fileRelationship->filename),
            'originalName' => $fileRelationship->original_name,
            'type' => $fileRelationship->type,
        ];
    }

    public function delMaterial(MaterialDeleteRequest $request): array
    {
        DB::beginTransaction();
        $teacher = auth()->user();

        $friendshipRelationship = Friendship::where([
            ['user_id', $teacher->id],
            ['friend_id', $request->user_id]
        ])->orWhere([
            ['user_id', $request->user_id],
            ['friend_id', $teacher->id]
        ])->first();

        if (!$friendshipRelationship ||
            $friendshipRelationship->status !== Friendship::STATUS_ACCEPTED ||
            $teacher->isLearner())
        {
            DB::rollBack();
            throw new ApiException('Недоступно', 403);
        }

        $profileMaterial = ProfileMaterial::where('id', $request->profile_material_id)->first();
        $fileRelationship = $profileMaterial->files()->first();

        throw_if(!Storage::disk('public')->delete($fileRelationship->path . $fileRelationship->filename),
            new ApiException("Материал не найден", 404)
        );

        $profileMaterial->files()->delete();
        $profileMaterial->delete();

        DB::commit();

        return [
            'subject_id' => $profileMaterial->subject_id,
            'material_id' => $profileMaterial->id,
        ];
    }

    public function index()
    {

    }

    public function updateMaterial(MaterialPatchRequest $request): array
    {
        DB::beginTransaction();
        $profileMaterialRelationship = ProfileMaterial::find($request->id);
        $fileRelationship = $profileMaterialRelationship->files()->first();

        $profileMaterialRelationship->update([
            'description' => $request->description,
        ]);

        $fileRelationship->update([
            'original_name' => $request->name,
        ]);


        DB::commit();

        return [
            'id' => $profileMaterialRelationship->refresh()->id,
            'name' => $fileRelationship->refresh()->original_name,
            'description' => $profileMaterialRelationship->refresh()->description,
        ];
    }

    public function show(MaterialsGetRequest $request): array
    {
        $user = auth()->user();

        // смотрим профиль другого пользователя, для учителя
        if ($user->id !== $request->user_id)
        {
            $isFriend = app(UserService::class)->isFriend($user->id, $request->user_id);

            throw_if(!$isFriend || $user->isLearner(),
                new ApiException('Доступно учителям пользователя', 403)
            );

            return $this->showMaterialsForTeacher($request);
        }
        // смотрим свой профиль
        else
        {
            return $this->showMaterialsForUser($request);
        }
    }

    public function showMaterialsForTeacher(MaterialsGetRequest $request): array
    {
        $user = auth()->user();
        $profileMaterials = ProfileMaterial::where('user_id', $request->user_id)->get();

        $response = collect([
            "user" => app(UserService::class)->getUsersDataForListById($user->id)[0],
            "subjects" => collect(),
        ]);

        foreach ($profileMaterials as $profileMaterial) {
            $fileRelationship = $profileMaterial->files()->first();

            if ($fileRelationship->user_id !== $user->id)
            {
                continue;
            }

            // Ищем существующий subject в массиве
            $subjectIndex = $response['subjects']->search(function ($subject) use ($profileMaterial) {
                return $subject['id'] === $profileMaterial->subject_id;
            });

            $materialData = [
                'originalName' => $fileRelationship->original_name,
                'url' => Storage::url($fileRelationship->path . '/' . $fileRelationship->filename),
                'type' => $fileRelationship->type,
                'mimeType' => $fileRelationship->mime_type,
                'description' => $profileMaterial->description,
                'id' => $profileMaterial->id,
            ];

            if ($subjectIndex === false)
            {
                $response['subjects']->push([
                    'id' => $profileMaterial->subject_id,
                    'materials' => collect([$materialData])
                ]);
            }
            else
            {
                $response['subjects'][$subjectIndex]['materials']->push($materialData);
            }
        }

        return $response->toArray();
    }

    public function showMaterialsForUser(MaterialsGetRequest $request): array
    {
        $user = auth()->user();
        $profileMaterials = ProfileMaterial::where('user_id', $request->user_id)->get();

        $response = collect();

        // Материалы профиля пользователя
        foreach ($profileMaterials as $profileMaterial) {
            // файл материала профиля пользователя
            $fileRelationship = $profileMaterial->files()->first();
            $teacherId = $fileRelationship->user_id;

            $teacherIndex = $response->search(function ($array) use ($teacherId) {
                return $array['user']['id'] === $teacherId;
            });

            $materialData = [
                'originalName' => $fileRelationship->original_name,
                'url' => Storage::url($fileRelationship->path . '/' . $fileRelationship->filename),
                'type' => $fileRelationship->type,
                'mimeType' => $fileRelationship->mime_type,
                'description' => $profileMaterial->description,
                'id' => $profileMaterial->id,
            ];

            if ($teacherIndex === false)
            {
                $response->push([
                    "user" => app(UserService::class)->getUsersDataForListById($teacherId)[0],
                    "subjects" => collect([
                        "id" => $profileMaterial->subject_id,
                        "materials" => collect([$materialData])
                    ]),
                ]);
            }
            else
            {
                $subjectIndex = $response[$teacherIndex]['subjects']->search(function ($subjectArray) use ($profileMaterial) {
                    return $subjectArray === $profileMaterial->subject_id;
                });

                if ($subjectIndex === false)
                {
                    $response[$teacherIndex]['subjects']->push([
                        "id" => $profileMaterial->subject_id,
                        "materials" => collect([$materialData])
                    ]);
                }
                else
                {
                    $response[$teacherIndex]['subjects']['materials']->push($materialData);
                }

            }
        }

        return $response->toArray();
    }
}
