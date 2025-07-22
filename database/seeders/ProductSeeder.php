<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\ProductCondition;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [];

        foreach (range(1, 24) as $i) {
            $products[] = [
                "card_id" => $i,
                "condition" => ProductCondition::NM->value,
                "price" => rand(100, 500),
                "sku" => "PKM-00{$i}",
            ];
        }

        DB::table("products")->insert($products);
    }
}
