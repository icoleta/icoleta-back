<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResiduumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('residuum')->insert([
            [
                'name' => 'Pilhas'
            ],
            [
                'name' => 'Papel'
            ],
            [
                'name' => 'Vidro'
            ],
            [
                'name' => 'Metal'
            ],
            [
                'name' => 'Plástico'
            ],
        ]);
    }
}
