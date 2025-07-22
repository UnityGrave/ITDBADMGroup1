<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sets')->insert([
            ['name' => 'Base Set'],
            ['name' => 'Jungle'],
            ['name' => 'Fossil'],
            ['name' => 'Team Rocket'],
            ['name' => 'Gym Heroes'],
        ]);
    }
} 