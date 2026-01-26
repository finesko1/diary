<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = ['english', 'spanish', 'chinese'];

        foreach ($subjects as $subject)
        {
            Subject::create(['name' => $subject]);

        }

        $this->command->info(count($subjects) . " предмета было добавлено в subjects_table");
    }
}
