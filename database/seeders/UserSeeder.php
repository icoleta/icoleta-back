<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Person;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {

        $users = [
            [
                'email' => 'companhia@ic.ufal.br',
                'role_name' => 'company',
                'password' => Hash::make('companhia'),
            ],
            [
                'email' => 'admin@ic.ufal.br',
                'role_name' => 'admin',
                'password' => Hash::make('admin'),
            ]
        ];

        foreach ($users as $user) {
            $role = Role::where('name', $user['role_name'])->first()->id;
            $newUser = User::create([
                'email' => $user['email'],
                'role_id' => $role,
                'password' => $user['password'],
            ]);

            if ($user['role_name'] === 'company') {
                Company::create([
                    'user_id' => $newUser->id,
                    'name' => 'Companhia',
                    'phone' => '82999999999',
                ]);
            }

            else if ($user['role_name'] === 'admin') {
                Person::create([
                    'user_id' => $newUser->id,
                    'name' => 'Jorge dos Santos',
                    'course_id' => '1',
                    'semester_id' => '1'
                ]);
            }
        }
    }
}