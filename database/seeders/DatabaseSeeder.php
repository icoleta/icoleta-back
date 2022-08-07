<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CitySeeder::class,
            CourseSeeder::class,
            ResiduumSeeder::class,
            RoleSeeder::class,
            SemesterSeeder::class
        ]);
    }
}
