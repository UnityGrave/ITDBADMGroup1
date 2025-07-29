<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sets')->insert([
            // Classic WOTC Sets
            ['name' => 'Base Set'],
            ['name' => 'Jungle'],
            ['name' => 'Fossil'],
            ['name' => 'Team Rocket'],
            ['name' => 'Neo Genesis'],
            
            // Modern Sets
            ['name' => 'Sword & Shield'],
            ['name' => 'Evolving Skies'],
            ['name' => 'Brilliant Stars'],
            ['name' => 'Lost Origin'],
            
            // Scarlet & Violet Era
            ['name' => 'Scarlet & Violet'],
            ['name' => '151'],
            ['name' => 'Paldea Evolved'],
            
            // Special Sets
            ['name' => 'Hidden Fates'],
            ['name' => 'Celebrations'],
        ]);
    }
} 