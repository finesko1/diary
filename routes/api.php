<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Subject\SubjectController;
use App\Http\Controllers\Subject\UserTopicController;
use App\Http\Controllers\User\FriendshipController;
use App\Http\Controllers\User\PersonalDataController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserContactDataController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserEducationDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::get('/storage/{path}', function ($path) {
    $storagePath = storage_path('app/public/' . $path);

    if (!File::exists($storagePath)) {
        abort(404);
    }

    return response()->file($storagePath);
})->where('path', '.*');

Route::get('login', function () {
    return redirect('/');
})->name('login');

Route::post('login', [AuthController::class, 'login']);

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::post('upload-photo', [UserController::class, 'uploadPhoto']);

    Route::get('', [UserController::class, 'show']);

    Route::post('createLearner', [UserController::class, 'createLearner']);

    Route::prefix('profileData')
        ->group(function () {
        Route::get('', [ProfileController::class, 'getProfileData']);
    });

    Route::prefix('personalData')
        ->group(function () {
        Route::post('dateOfBirth', [PersonalDataController::class, 'updateDateOfBirth']);
        Route::get('fullName', [PersonalDataController::class, 'getFullName']);
        Route::post('fullName', [PersonalDataController::class, 'updateFullName']);
        Route::post('email', [PersonalDataController::class, 'updateEmail']);
    });


    Route::prefix('contactData')
        ->group(function () {
        Route::post('city', [UserContactDataController::class, 'updateCity']);
        Route::post('telephone', [UserContactDataController::class, 'updateTelephone']);
        Route::post('whatsapp', [UserContactDataController::class, 'updateWhatsApp']);
        Route::post('telegram', [UserContactDataController::class, 'updateTelegram']);
        Route::post('vk', [UserContactDataController::class, 'updateVk']);
        Route::post('callsPlatform', [UserContactDataController::class, 'updateCallsPlatform']);
    });

    Route::prefix('educationData')
        ->group(function () {
        Route::post('beginningOfTeaching', [UserEducationDataController::class, 'updateBeginningOfTeaching']);
        Route::post('course', [UserEducationDataController::class, 'updateCourse']);
        Route::post('languageLevel', [UserEducationDataController::class, 'updateLanguageLevel']);
    });

});

Route::prefix('friends')->middleware('auth:sanctum')->group(function () {
    Route::prefix('requests')->group(function () {
        Route::get('', [FriendshipController::class, 'getRequests'])->name('friends.requests.index');
        Route::post('{friendId}', [FriendshipController::class, 'sendRequest'])->name('friends.requests.send');
        Route::put('{friendId}/accept', [FriendshipController::class, 'acceptRequest'])
            ->name('friends.requests.accept');
        Route::delete('{friendId}/decline', [FriendshipController::class, 'declineRequest'])
            ->name('friends.requests.decline');
    });

    Route::get('', [FriendshipController::class, 'index']);
    Route::get('{friendId}', [FriendshipController::class, 'show']);
    Route::delete('{friendId}/block', [FriendshipController::class, 'block']);
    Route::put('{friendId}/unblock', [FriendshipController::class, 'unblock']);
    Route::delete('{friendId}/delete', [FriendshipController::class, 'delete']);
});

Route::prefix('subjects')->middleware('auth:sanctum')->group(function () {
    // получение занятия+тем+заданий
//    Route::get('', [SubjectController::class, 'index'])->name('subjects.index');
    // создание занятие+темы+задания
    Route::post('', [SubjectController::class, 'create'])->name('subjects.create');
    // создание задания. нет создания типа задания!!!!
    Route::get('assignments/types', [SubjectController::class, 'getAssignmentTypes'])->name('assignments.types.index');
    Route::delete('assignments/types/{assignmentTypeId}', [SubjectController::class, 'deleteAssignmentType'])->name('assignments.types.delete');
    Route::post('assignments/types', [SubjectController::class, 'createAssignmentType'])->name('assignments.types.create');

    Route::post('assignments', [SubjectController::class, 'createAssignment'])->name('assignments.create');


    Route::prefix('{subjectId}')->group(function () {
        // получение темы
//        Route::get('', [SubjectController::class, 'showTopic'])->name('subjects.show');

        Route::prefix('topics')->group(function () {
            // получение списка тем
            Route::get('', [SubjectController::class, 'getTopics'])->name('topics.index');
            // Удаление темы
            Route::delete('{topicId}', [SubjectController::class, 'deleteTopic'])->name('topics.delete');

            // добавление темы
            Route::post('', [SubjectController::class, 'createTopic'])->name('topics.create');
        });

        Route::prefix('userTopics')->group(function () {
            // добавление темы
            Route::put('', [SubjectController::class, 'updateUserTopic'])->name('userTopics.update');
            Route::delete('{userTopicId}', [SubjectController::class, 'deleteUserTopic'])->name('userTopics.delete');
            // добавление задания в userTopic
//            Route::post('{userTopicId}/assignments',
//                [SubjectController::class, 'createUserTopicAssignment'])->name('userTopics.assignments.create');
        });
    });
});

Route::prefix('monthevents')->middleware('auth:sanctum')->group(function () {
    Route::get('{date?}', [UserTopicController::class, 'getMonthEvents'])->name('monthevents.index');
    Route::get('{date}/dayevents', [UserTopicController::class, 'getDayEvents'])->name('monthevents.day.index');
});

Route::get('lessons/{lessonId}', [UserTopicController::class, 'getEventTopics'])
    ->middleware('auth:sanctum');
Route::post('lessons/{lessonId}/userTopics', [SubjectController::class, 'createUserTopic'])->name('userTopics.create')
    ->middleware('auth:sanctum');
Route::get('lessons/{lessonId}/userTopics/{userTopicId}/assignments', [UserTopicController::class, 'getUserTopicAssignments'])->name('userTopics.show')
    ->middleware('auth:sanctum');
Route::post('lessons/{lessonId}/userTopics/{userTopicId}/assignments', [SubjectController::class, 'createUserTopicAssignment'])
    ->middleware('auth:sanctum');
Route::put('lessons/{lessonId}/userTopics/{userTopicId}/assignments/{assignmentId}', [SubjectController::class, 'updateAssignment'])
    ->middleware('auth:sanctum');
Route::patch('assignments/{assignmentId}/mark', [SubjectController::class, 'updateAssignmentMark'])
    ->middleware('auth:sanctum');
Route::delete('lessons/{lessonId}/userTopics/{userTopicId}/assignments/{assignmentId}', [SubjectController::class, 'deleteAssignment'])
    ->middleware('auth:sanctum');


Route::get('test', function (Request $request) {
    return response()->json("hello");
})->middleware('auth:sanctum');
