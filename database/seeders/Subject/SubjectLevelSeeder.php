<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\Subject;
use App\Models\Subject\SubjectLevel;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectLevelSeeder extends Seeder
{
    /**
     * Уровень знания языков
     */
    public function run(): void
    {
        $subjects = Subject::all();
        $teacher = User::where('role', User::ROLE_TEACHER)->first();
        $students = User::whereNotIn('role', [User::ROLE_TEACHER, User::ROLE_ADMIN])->get();

        foreach ($subjects as $subject)
        {
            SubjectLevel::create([
                'user_id' => $teacher->id,
                'subject_id' => $subject->id,
                'level' => 'C1',
                'evaluated_by' => $teacher->id,
            ]);
        }

        foreach ($students as $student)
        {
            SubjectLevel::create([
                'user_id' => $student->id,
                'subject_id' => 1,
                'level' => 'A1',
                'evaluated_by' => $teacher->id,
            ]);
        }

        $this->command->info("Добавлены уровни языков для пользователей");
    }
}
