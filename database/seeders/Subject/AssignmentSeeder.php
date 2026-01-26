<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\Assignment;
use App\Models\Subject\AssignmentType;
use App\Models\Subject\UserTopic;
use App\Models\Subject\UserTopicAssignment;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Создание заданий
     */
    public function run(): void
    {
        $teacher = User::where('role', User::ROLE_TEACHER)->first();
        $assignmentTypes = AssignmentType::all();

        $userTopics = userTopic::where('teacher_id', $teacher->id)->get();
        $countAssignmentsArray = [];
        $index = 0;
        foreach ($userTopics as $userTopic)
        {
            $countAssignments = rand(1, 5);
            $countAssignmentsArray[$index] = $countAssignments;
            for ($i = 0; $i < $countAssignments; $i++)
            {
                $currentAssignment = Assignment::create([
                    'assignment_type_id' => $assignmentTypes->random()->id,
                    'description' => 'Промежуточное описание задания',
                ]);

                UserTopicAssignment::create([
                    'assignment_id' => $currentAssignment->id,
                    'user_topic_id' => $userTopic->id,
                ]);
            }
            $index++;
        }

        $this->command->info('Добавлено ' . array_sum($countAssignmentsArray)
            . ' заданий для ' . count($userTopics) . ' занятий');
    }
}
