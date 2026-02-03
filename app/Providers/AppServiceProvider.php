<?php

namespace App\Providers;

use App\Services\Phone\RussianPhoneService;
use App\Services\Subject\SubjectService;
use App\Services\Subject\UserTopicService;
use App\Services\User\ContactDataService;
use App\Services\User\EducationDataService;
use App\Services\User\FriendshipService;
use App\Services\User\PersonalDataService;
use App\Services\User\ProfileService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PersonalDataService::class, function ($app) {
            return new PersonalDataService();
        });

        $this->app->singleton(ContactDataService::class, function ($app) {
            return new ContactDataService($app->make(RussianPhoneService::class));
        });

        $this->app->singleton(EducationDataService::class, function ($app) {
            return new EducationDataService();
        });

        $this->app->singleton(ProfileService::class, function ($app) {
            return new ProfileService(
                $app->make(PersonalDataService::class),
                $app->make(EducationDataService::class),
                $app->make(ContactDataService::class)
            );
        });

        $this->app->singleton(RussianPhoneService::class, function ($app) {
            return new RussianPhoneService();
        });

        $this->app->singleton(FriendshipService::class, function ($app) {
            return new FriendshipService(
                $app->make(UserService::class)
            );
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });

        $this->app->singleton(SubjectService::class, function ($app) {
            return new SubjectService();
        });

        $this->app->singleton(UserTopicService::class, function ($app) {
            return new UserTopicService($this->app->make(UserService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
