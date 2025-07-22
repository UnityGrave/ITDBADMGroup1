<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $inventory = [];

        foreach (range(1, 24) as $i) {
            $inventory[] = [
                "product_id" => $i,
                "stock" => rand(0, 20), // Random stock between 0 and 20
            ];
        }

        DB::table("inventory")->insert($inventory);
    }
}
