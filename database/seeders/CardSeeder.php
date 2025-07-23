<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [];

        foreach (range(1, 24) as $i) {
            $cards[] = [
                "name" => "Pokemon Card #{$i}",
                "collector_number" => "00{$i}",
                "set_id" => rand(1, 5),
                "rarity_id" => rand(1, 5),
                "category_id" => rand(1, 4),
            ];
        }

        DB::table("cards")->insert($cards);
    }
}
