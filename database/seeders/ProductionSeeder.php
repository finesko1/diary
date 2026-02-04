<?php

namespace Database\Seeders;

use Database\Seeders\Production\UsersInitializationSeeder;
use Database\Seeders\Subject\SubjectSeeder;
use Database\Seeders\User\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UsersInitializationSeeder::class,
            SubjectSeeder::class,
        ]);
    }
}
