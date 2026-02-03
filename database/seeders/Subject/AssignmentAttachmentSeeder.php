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
                'path' => 'myself/testfile.pdf',
                'type' => 'file',
                'mime' => 'application/pdf',
                'size' => 204800
            ],
            [
                'name' => 'textfile.txt',
                'path' => 'myself/testfile.txt',
                'type' => 'file',
                'mime' => 'text/plain',
                'size' => 10240
            ],
            [
                'name' => 'image.jpg',
                'path' => 'myself/testfile.jpg',
                'type' => 'image',
                'mime' => 'image/jpeg',
                'size' => 51200
            ],
        ];

        $counter = 0;

        foreach ($lessons as $lesson)
        {
            $lessonUserTopics = UserTopic::where('lesson_id', $lesson->id)->get();

            foreach ($lessonUserTopics as $userTopic) {
                $assignments = UserTopicAssignment::where('user_topic_id', $userTopic->id)->get();
                foreach ($assignments as $assignment)
                {
                    $countTeacherAttachments = rand(1, 5);

                    for ($i = 0; $i < $countTeacherAttachments; $i++) {
                        $randomFile = $sampleFiles[array_rand($sampleFiles)];

                        AssignmentAttachment::create([
                            'assignment_id' => $assignment->id,
                            'user_id' => $lesson->teacher_id,
                            'type' => $randomFile['type'],
                            'description' => 'Вложение к заданию ' . $assignment->id,
                            'path' => $randomFile['path'],
                            'original_name' => $randomFile['name'],
                            'mime_type' => $randomFile['mime'],
                            'size' => $randomFile['size'],
                        ]);
                    }

                    $countStudentAttachments = rand(1, 5);

                    for ($i = 0; $i < $countStudentAttachments; $i++) {
                        $randomFile = $sampleFiles[array_rand($sampleFiles)];

                        AssignmentAttachment::create([
                            'assignment_id' => $assignment->id,
                            'user_id' => $lesson->student_id,
                            'type' => $randomFile['type'],
                            'description' => 'Вложение к заданию ' . $assignment->id,
                            'path' => $randomFile['path'],
                            'original_name' => $randomFile['name'],
                            'mime_type' => $randomFile['mime'],
                            'size' => $randomFile['size'],
                        ]);
                    }

                    $counter += ($countTeacherAttachments + $countStudentAttachments);
                }
            }
        }

        $this->command->info("Добавлено $counter вложений(я)");
    }
}
