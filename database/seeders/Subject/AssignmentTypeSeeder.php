<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\AssignmentType;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class AssignmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherId = User::where('role', User::ROLE_TEACHER)->first()->id;
        $assignmentTypes = [
            'Чтение', 'Диктант', 'Перевод', 'Грамматика'
        ];

        foreach ($assignmentTypes as $type)
        {
            AssignmentType::create([
                'user_id' => $teacherId,
                'name' => $type
            ]);
        }

        $this->command->info("Добавлено " . count($assignmentTypes)
            . " вида(ов) задания");
    }
}
