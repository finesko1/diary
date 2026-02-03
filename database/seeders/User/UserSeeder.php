<?php

namespace Database\Seeders\User;

use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email' => 'admin@school.ru',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'email' => 'teacher@school.ru',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_TEACHER,
        ]);

        User::create([
            'email' => 'children@school.ru',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_CHILDREN,
        ]);

        User::create([
            'email' => 'student@school.ru',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_STUDENT,
        ]);

        User::create([
            'email' => 'adult@school.ru',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ADULT,
        ]);
    }
}
