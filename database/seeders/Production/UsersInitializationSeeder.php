<?php

namespace Database\Seeders\Production;

use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersInitializationSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'firstteacher',
            'password' => Hash::make('currentPassword_secure'),
            'role' => User::ROLE_TEACHER,
        ]);

        $this->command->info("Пользователь firstteacher добавлен");
    }
}
