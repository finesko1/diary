<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\Lesson;
use App\Models\Subject\Topic;
use App\Models\Subject\UserTopic;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::where('role', User::ROLE_TEACHER)->first();
        $students = User::whereNotIn('role', [User::ROLE_TEACHER, User::ROLE_ADMIN])->get();
        $topics = Topic::all();

        $stats = [
            User::ROLE_CHILDREN => 0,
            User::ROLE_STUDENT => 0,
            User::ROLE_ADULT => 0,
        ];

        foreach ($students as $student) {
            $countForStudent = rand(1, 5);
            for ($i = 0; $i < $countForStudent; $i++) {
                $currentTopic = $topics->random();
                $lesson = Lesson::create([
                    'subject_id' => $currentTopic->subject_id,
                    'date' => now()->addDay(rand(-5, 5)),
                    'teacher_id' => $teacher->id,
                    'student_id' => $student->id,
                ]);

                UserTopic::create([
                    'topic_id' => $currentTopic->id,
                    'lesson_id' => $lesson->id,
                    'mark' => rand(3, 5),
                ]);
            }

            $stats[$student->role] = $countForStudent;
        }

        $this->command->info("Добавлено " . array_sum($stats) . " занятий для "
            . count($students) . " пользователей");
        $this->command->info("\t - " . $stats[User::ROLE_CHILDREN] . " для " . User::ROLES[User::ROLE_CHILDREN]);
        $this->command->info("\t - " . $stats[User::ROLE_STUDENT] . " для " . User::ROLES[User::ROLE_STUDENT]);
        $this->command->info("\t - " . $stats[User::ROLE_ADULT] . " для " . User::ROLES[User::ROLE_ADULT]);
    }
}
