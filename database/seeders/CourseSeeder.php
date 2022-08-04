<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $department = new Department();
        $department->name = 'Instituto de Computação';
        $department->acronym = 'IC';
        $department->save();
        
        DB::table('courses')->insert([
            [
                'name' => 'Ciência da Computação',
                'department_id' => $department->id
            ],
            [
                'name' => 'Engenharia da Computação',
                'department_id' => $department->id
            ]
        ]);

    }
}
