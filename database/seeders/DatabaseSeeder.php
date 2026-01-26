<?php

namespace Database\Seeders;

use Database\Seeders\Subject\AssignmentAttachmentSeeder;
use Database\Seeders\Subject\AssignmentSeeder;
use Database\Seeders\Subject\AssignmentTypeSeeder;
use Database\Seeders\Subject\SubjectLevelSeeder;
use Database\Seeders\Subject\SubjectSeeder;
use Database\Seeders\Subject\TopicSeeder;
use Database\Seeders\Subject\UserTopicSeeder;
use Database\Seeders\User\FriendshipSeeder;
use Database\Seeders\User\PersonalDataSeeder;
use Database\Seeders\User\UserContactDataSeeder;
use Database\Seeders\User\UserEducationDataSeeder;
use Database\Seeders\User\UserSeeder;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Пользователи, их персональные данные и свящи между ними
        $this->call([
            UserSeeder::class,
            PersonalDataSeeder::class,
            UserContactDataSeeder::class,
            UserEducationDataSeeder::class,
            FriendshipSeeder::class,
        ]);

        // Занятия и их прикрепление к пользователям
        $this->call([
            SubjectSeeder::class,
            SubjectLevelSeeder::class,
            TopicSeeder::class,
            UserTopicSeeder::class,
            AssignmentTypeSeeder::class,
            AssignmentSeeder::class,
            AssignmentAttachmentSeeder::class,
        ]);
    }
}
