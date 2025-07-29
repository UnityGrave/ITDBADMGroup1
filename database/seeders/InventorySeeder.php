<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $inventory = [];

        // 28 cards × 5 conditions + 2 special products = 142 total products
        $totalProducts = 142;

        foreach (range(1, $totalProducts) as $productId) {
            $stock = $this->getStockForProduct($productId);
            
            $inventory[] = [
                "product_id" => $productId,
                "stock" => $stock,
            ];
        }

        DB::table("inventory")->insert($inventory);
    }

    private function getStockForProduct($productId): int
    {
        // High value products (Charizard variants)
        $highValueProducts = [1, 6, 11, 16, 21, 45, 50, 55, 60, 65, 141, 142]; // Charizard conditions + specials
        
        // Medium value products (Popular Pokemon)
        $mediumValueProducts = range(2, 80); // Most base set and V cards
        
        // Common products (Energy cards)
        $commonProducts = range(96, 125); // Energy and trainer products
        
        if (in_array($productId, $highValueProducts)) {
            return rand(0, 2); // 0-2 stock for premium cards
        } elseif (in_array($productId, $mediumValueProducts)) {
            return rand(1, 6); // 1-6 stock for medium cards
        } elseif (in_array($productId, $commonProducts)) {
            return rand(3, 15); // 3-15 stock for common cards
        } else {
            return rand(0, 8); // 0-8 stock for everything else
        }
    }
}
