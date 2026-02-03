<?php

namespace Database\Seeders\User;

use App\Models\User\PersonalData;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class PersonalDataSeeder extends Seeder
{
    /**
     * Создание персональных данных тестовых пользователей
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user)
        {
            if ($user->role == User::ROLE_TEACHER)
            {
                PersonalData::create([
                    'user_id' => $user->id,
                    'last_name' => 'Иванова',
                    'first_name' => 'Алена',
                    'middle_name' => 'Ивановна',
                    'date_of_birth' => '2004-09-10'
                ]);
            }
            if ($user->role == User::ROLE_STUDENT)
            {
                PersonalData::create([
                    'user_id' => $user->id,
                    'last_name' => 'Иванов',
                    'first_name' => 'Михаил',
                    'middle_name' => 'Иванович',
                    'date_of_birth' => '2004-09-10'
                ]);
            }
            if ($user->role == User::ROLE_CHILDREN)
            {
                PersonalData::create([
                    'user_id' => $user->id,
                    'last_name' => 'Петров',
                    'first_name' => 'Левон',
                    'middle_name' => 'Максимович',
                    'date_of_birth' => '2010-05-10'
                ]);
            }
            if ($user->role == User::ROLE_ADULT)
            {
                PersonalData::create([
                    'user_id' => $user->id,
                    'last_name' => 'Петрова',
                    'first_name' => 'Елена',
                    'middle_name' => 'Викторовна',
                    'date_of_birth' => '1974-04-12'
                ]);
            }
        }
    }
}
