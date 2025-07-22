<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaritySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rarities')->insert([
            ['name' => 'Common'],
            ['name' => 'Uncommon'],
            ['name' => 'Rare'],
            ['name' => 'Ultra Rare'],
            ['name' => 'Secret Rare'],
        ]);
    }
} 