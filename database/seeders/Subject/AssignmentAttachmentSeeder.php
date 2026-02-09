<?php

namespace Database\Seeders\Subject;

use App\Models\Subject\Assignment;
use App\Models\Subject\AssignmentAttachment;
use App\Models\Subject\Lesson;
use App\Models\Subject\UserTopic;
use App\Models\Subject\UserTopicAssignment;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentAttachmentSeeder extends Seeder
{
    /**
     * Добавление вложений к заданиям
     */
    public function run(): void
    {
        $lessons = Lesson::all();

        // Пример файлов для случайного выбора
        $sampleFiles = [
            [
                'name' => 'document.pdf',
                'path' => 'myself/testfile.png',
                'type' => 'image',
                'mime' => 'image/png',
            ],
            [
                'name' => 'textfile.txt',
                'path' => 'myself/testfile2.png',
                'type' => 'image',
                'mime' => 'image/png',
            ],
            [
                'name' => 'video.jpg',
                'path' => 'myself/testvideo.mp4',
                'type' => 'video',
                'mime' => 'mp4',
            ],
            [
                'name' => 'video2.jpg',
                'path' => 'myself/testvideo2.mp4',
                'type' => 'video',
                'mime' => 'mp4',
            ],
        ];

        $counter = 0;

        foreach ($lessons as $lesson)
        {
            $lessonUserTopics = UserTopic::where('lesson_id', $lesson->id)->get();

            foreach ($lessonUserTopics as $userTopic) {
                $userTopicAssignments = UserTopicAssignment::where('user_topic_id', $userTopic->id)->get();
                foreach ($userTopicAssignments as $userTopicAssignment)
                {
                    $assignment = Assignment::find($userTopicAssignment->assignment_id);
                    $countTeacherAttachments = rand(1, 5);

                    for ($i = 0; $i < $countTeacherAttachments; $i++) {
                        $randomFile = $sampleFiles[array_rand($sampleFiles)];

                        $assignmentAttachment = $assignment->files()->create([
                            'type' => $randomFile['type'],
                            'user_id' => $lesson->teacher_id,
                            'path' => $randomFile['path'],
                            'original_name' => $randomFile['name'],
                            'mime_type' => $randomFile['mime'],
                        ]);
                    }

                    $countStudentAttachments = rand(1, 5);

                    for ($i = 0; $i < $countStudentAttachments; $i++) {
                        $randomFile = $sampleFiles[array_rand($sampleFiles)];

                        $assignmentAttachment = $assignment->files()->create([
                            'type' => $randomFile['type'],
                            'user_id' => $lesson->student_id,
                            'path' => $randomFile['path'],
                            'original_name' => $randomFile['name'],
                            'mime_type' => $randomFile['mime'],
                        ]);
                    }

                    $counter += ($countTeacherAttachments + $countStudentAttachments);
                }
            }
        }

        $this->command->info("Добавлено $counter вложений(я)");
    }
}
