<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            // Base Set Classics
            ['name' => 'Charizard', 'collector_number' => '004/102', 'set_id' => 1, 'rarity_id' => 4, 'category_id' => 1],
            ['name' => 'Blastoise', 'collector_number' => '002/102', 'set_id' => 1, 'rarity_id' => 4, 'category_id' => 1],
            ['name' => 'Venusaur', 'collector_number' => '015/102', 'set_id' => 1, 'rarity_id' => 4, 'category_id' => 1],
            ['name' => 'Pikachu', 'collector_number' => '058/102', 'set_id' => 1, 'rarity_id' => 1, 'category_id' => 1],
            ['name' => 'Mewtwo', 'collector_number' => '010/102', 'set_id' => 1, 'rarity_id' => 4, 'category_id' => 1],

            // Jungle Set
            ['name' => 'Scyther', 'collector_number' => '010/64', 'set_id' => 2, 'rarity_id' => 3, 'category_id' => 1],
            ['name' => 'Vaporeon', 'collector_number' => '012/64', 'set_id' => 2, 'rarity_id' => 3, 'category_id' => 1],
            ['name' => 'Jolteon', 'collector_number' => '004/64', 'set_id' => 2, 'rarity_id' => 3, 'category_id' => 1],

            // Modern V Cards (Sword & Shield)
            ['name' => 'Charizard V', 'collector_number' => '019/189', 'set_id' => 6, 'rarity_id' => 4, 'category_id' => 6],
            ['name' => 'Pikachu V', 'collector_number' => '043/185', 'set_id' => 6, 'rarity_id' => 4, 'category_id' => 6],
            ['name' => 'Rayquaza V', 'collector_number' => '100/203', 'set_id' => 7, 'rarity_id' => 4, 'category_id' => 6],

            // VMAX Cards (Sword & Shield)
            ['name' => 'Charizard VMAX', 'collector_number' => '020/189', 'set_id' => 6, 'rarity_id' => 4, 'category_id' => 7],
            ['name' => 'Pikachu VMAX', 'collector_number' => '044/185', 'set_id' => 6, 'rarity_id' => 4, 'category_id' => 7],

            // ex Cards (Scarlet & Violet era)
            ['name' => 'Charizard ex', 'collector_number' => '006/165', 'set_id' => 10, 'rarity_id' => 4, 'category_id' => 4],
            ['name' => 'Miraidon ex', 'collector_number' => '081/198', 'set_id' => 10, 'rarity_id' => 4, 'category_id' => 4],

            // Special Cards
            ['name' => 'Rayquaza (Amazing Rare)', 'collector_number' => '109/185', 'set_id' => 8, 'rarity_id' => 5, 'category_id' => 1],
            ['name' => 'Umbreon VMAX (Alt Art)', 'collector_number' => '215/203', 'set_id' => 7, 'rarity_id' => 5, 'category_id' => 10],
            ['name' => 'Charizard VMAX (Rainbow)', 'collector_number' => '074/073', 'set_id' => 14, 'rarity_id' => 5, 'category_id' => 11],

            // Energy Cards (Base Set)
            ['name' => 'Fire Energy', 'collector_number' => '092/102', 'set_id' => 1, 'rarity_id' => 1, 'category_id' => 3],
            ['name' => 'Water Energy', 'collector_number' => '093/102', 'set_id' => 1, 'rarity_id' => 1, 'category_id' => 3],
            ['name' => 'Lightning Energy', 'collector_number' => '094/102', 'set_id' => 1, 'rarity_id' => 1, 'category_id' => 3],
            ['name' => 'Psychic Energy', 'collector_number' => '095/102', 'set_id' => 1, 'rarity_id' => 1, 'category_id' => 3],

            // Special Energy
            ['name' => 'Double Colorless Energy', 'collector_number' => '100/102', 'set_id' => 1, 'rarity_id' => 2, 'category_id' => 3],
            ['name' => 'Rainbow Energy', 'collector_number' => '017/109', 'set_id' => 4, 'rarity_id' => 3, 'category_id' => 3],

            // Trainer Cards (Base Set)
            ['name' => 'Professor Oak', 'collector_number' => '088/102', 'set_id' => 1, 'rarity_id' => 2, 'category_id' => 2],
            ['name' => 'Computer Search', 'collector_number' => '071/102', 'set_id' => 1, 'rarity_id' => 3, 'category_id' => 2],
            ['name' => 'Professor\'s Research', 'collector_number' => '178/202', 'set_id' => 6, 'rarity_id' => 2, 'category_id' => 2],

            // Full Art Trainer (Sword & Shield)
            ['name' => 'Professor\'s Research (Full Art)', 'collector_number' => '201/202', 'set_id' => 6, 'rarity_id' => 4, 'category_id' => 9],

            // Promo Cards (Celebrations)
            ['name' => 'Birthday Pikachu', 'collector_number' => 'PROMO', 'set_id' => 14, 'rarity_id' => 5, 'category_id' => 13],
            ['name' => 'Alakazam (Error)', 'collector_number' => '001/102', 'set_id' => 1, 'rarity_id' => 5, 'category_id' => 1],
        ];

        DB::table('cards')->insert($cards);
    }
}
