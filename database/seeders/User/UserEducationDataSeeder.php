<?php

namespace Database\Seeders\User;

use App\Models\User\User;
use App\Models\User\UserEducationData;
use Illuminate\Database\Seeder;

class UserEducationDataSeeder extends Seeder
{
    /**
     * Создание данных об обучении
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user)
        {
            if ($user->role == User::ROLE_TEACHER)
            {
                UserEducationData::create([
                    'user_id' => $user->id,
                    'beginning_of_teaching' => '2019-01-01',
                ]);
            }
            if ($user->role == User::ROLE_STUDENT)
            {
                UserEducationData::create([
                    'user_id' => $user->id,
                    'course' => '1',
                ]);
            }
            if ($user->role == User::ROLE_CHILDREN)
            {
                UserEducationData::create([
                    'user_id' => $user->id,
                    'course' => '8',
                ]);
            }
        }
    }
}
