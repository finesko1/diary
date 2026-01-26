<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\Subject;
use App\Models\Subject\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = ['Кухня', 'Спальня', 'Гостиная', 'Притяжательные местоимения', 'Личные местоимения'];
        $subjects = Subject::all();
        foreach ($subjects as $subject)
        {
            foreach ($topics as $topic)
            {
                Topic::create([
                    'subject_id' => $subject->id,
                    'name' => $topic
                ]);
            }
        }

        $this->command->info(count($topics) . " вида(ов) занятий было добавлено в "
            . count($subjects) . " предмета");
    }
}
