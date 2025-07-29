<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Pokémon'],
            ['name' => 'Trainer'],
            ['name' => 'Energy'],
            ['name' => 'Pokémon ex'],
            ['name' => 'Pokémon GX'],
            ['name' => 'Pokémon V'],
            ['name' => 'Pokémon VMAX'],
            ['name' => 'Pokémon VSTAR'],
            ['name' => 'Full Art'],
            ['name' => 'Alternate Art'],
            ['name' => 'Rainbow Rare'],
            ['name' => 'Gold Card'],
            ['name' => 'Promo'],
            ['name' => 'Japanese Exclusive'],
        ]);
    }
} 