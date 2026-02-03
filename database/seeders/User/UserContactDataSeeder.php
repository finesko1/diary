<?php

namespace Database\Seeders\User;

use App\Models\User\User;
use App\Models\User\UserContactData;
use Illuminate\Database\Seeder;

class UserContactDataSeeder extends Seeder
{
    /**
     * Создание данных для связи с тестовыми пользователями
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user)
        {
            if ($user->role == User::ROLE_TEACHER)
            {
                // user_id, city, telephone, whatsapp, telegram, vk, calls_platform
                UserContactData::create([
                    'user_id' => $user->id,
                    'city' => 'Москва',
                    'telephone' => '+7-999-888-77-66',
                    'whatsapp' => '+7-999-888-77-66',
                    'telegram' => '@alena',
                    'vk' => '',
                    'calls_platform' => 'Место для созвона - skype'
                ]);
            }
            if ($user->role == User::ROLE_STUDENT)
            {
                UserContactData::create([
                    'user_id' => $user->id,
                    'city' => 'Иваново',
                    'telephone' => '+7-999-888-77-66',
                    'whatsapp' => '+7-999-888-77-66',
                    'telegram' => '@mike',
                    'vk' => '',
                    'calls_platform' => 'Место для созвона - skype'
                ]);
            }
            if ($user->role == User::ROLE_CHILDREN)
            {
                UserContactData::create([
                    'user_id' => $user->id,
                    'city' => 'Кемерово',
                    'telephone' => '+7-999-888-77-66',
                    'whatsapp' => '+7-999-888-77-66',
                    'telegram' => '@levon',
                    'vk' => '',
                    'calls_platform' => 'Место для созвона - skype'
                ]);
            }
            if ($user->role == User::ROLE_ADULT)
            {
                UserContactData::create([
                    'user_id' => $user->id,
                    'city' => 'Красногорск',
                    'telephone' => '+7-999-888-77-66',
                    'whatsapp' => '+7-999-888-77-66',
                    'telegram' => '@elena',
                    'vk' => '',
                    'calls_platform' => 'Место для созвона - skype'
                ]);
            }
        }
    }
}
