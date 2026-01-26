<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\AssignmentType;
use Illuminate\Database\Seeder;

class AssignmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignmentTypes = [
            'Чтение', 'Диктант', 'Перевод', 'Грамматика'
        ];

        foreach ($assignmentTypes as $type)
        {
            AssignmentType::create(['name' => $type]);
        }

        $this->command->info("Добавлено " . count($assignmentTypes)
            . " вида(ов) задания");
    }
}
