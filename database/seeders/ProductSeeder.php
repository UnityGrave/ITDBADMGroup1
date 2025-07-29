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
        $cardCount = 28;

        // Price ranges based on card popularity and rarity (in cents)
        $priceRanges = [
            // Premium cards
            1 => [150000, 400000], // Charizard: $1500-$4000
            9 => [12000, 30000],   // Charizard V: $120-$300
            12 => [20000, 50000],  // Charizard VMAX: $200-$500
            14 => [10000, 25000],  // Charizard ex: $100-$250
            17 => [8000, 18000],   // Rayquaza Amazing: $80-$180
            18 => [15000, 35000],  // Umbreon Alt Art: $150-$350
            19 => [25000, 60000],  // Charizard Rainbow: $250-$600
            
            // Popular classics
            2 => [6000, 15000],    // Blastoise: $60-$150
            3 => [5000, 12000],    // Venusaur: $50-$120
            5 => [8000, 20000],    // Mewtwo: $80-$200
            
            // Error/Promo cards
            28 => [20000, 50000],  // Alakazam Error: $200-$500
            27 => [15000, 30000],  // Birthday Pikachu: $150-$300
        ];
        
        // Default prices by rarity
        $defaultPrices = [
            1 => [50, 200],       // Common: $0.50-$2
            2 => [200, 600],      // Uncommon: $2-$6
            3 => [500, 1200],     // Rare: $5-$12
            4 => [1000, 3000],    // Ultra Rare: $10-$30
            5 => [2000, 8000],    // Secret Rare: $20-$80
        ];

        foreach (range(1, $cardCount) as $cardId) {
            $conditions = [
                ProductCondition::NM,
                ProductCondition::LP,
                ProductCondition::MP,
                ProductCondition::HP,
                ProductCondition::DMG
            ];
            
            // Condition multipliers
            $conditionMultipliers = [
                ProductCondition::NM->value => 1.0,
                ProductCondition::LP->value => 0.85,
                ProductCondition::MP->value => 0.65,
                ProductCondition::HP->value => 0.45,
                ProductCondition::DMG->value => 0.25,
            ];

            foreach ($conditions as $condition) {
                // Get price range
                if (isset($priceRanges[$cardId])) {
                    $baseMin = $priceRanges[$cardId][0];
                    $baseMax = $priceRanges[$cardId][1];
                } else {
                    // Default based on rarity
                    $rarityId = $this->getRarityForCard($cardId);
                    $baseMin = $defaultPrices[$rarityId][0];
                    $baseMax = $defaultPrices[$rarityId][1];
                }
                
                // Apply condition multiplier
                $conditionMultiplier = $conditionMultipliers[$condition->value];
                $minPrice = (int)($baseMin * $conditionMultiplier);
                $maxPrice = (int)($baseMax * $conditionMultiplier);
                
                $price = rand($minPrice, $maxPrice);
                $price = max($price, 25); // Minimum $0.25
                
                $products[] = [
                    "card_id" => $cardId,
                    "condition" => $condition->value,
                    "base_price_cents" => $price,
                    "base_currency_code" => "USD",
                    "sku" => sprintf("PKM-%03d-%s", $cardId, strtoupper(substr($condition->value, 0, 2))),
                ];
            }
        }

        // Add some special graded variants
        $specialProducts = [
            [
                "card_id" => 1, // Charizard
                "condition" => ProductCondition::NM->value,
                "base_price_cents" => 800000, // $8000 PSA 10
                "base_currency_code" => "USD",
                "sku" => "PKM-001-PSA10",
            ],
            [
                "card_id" => 27, // Birthday Pikachu
                "condition" => ProductCondition::NM->value,
                "base_price_cents" => 35000, // $350
                "base_currency_code" => "USD",
                "sku" => "PKM-027-JP-NM",
            ],
        ];

        $products = array_merge($products, $specialProducts);

        DB::table("products")->insert($products);
    }

    private function getRarityForCard($cardId): int
    {
        // Map card positions to rarities based on CardSeeder
        if (in_array($cardId, [4, 20, 21, 22, 23])) return 1; // Common (Energy, basic cards)
        if (in_array($cardId, [24, 25, 26])) return 2; // Uncommon (Special energy, trainers)
        if (in_array($cardId, [6, 7, 8, 25])) return 3; // Rare (Jungle set, Computer Search)
        if (in_array($cardId, [1, 2, 3, 5, 9, 10, 11, 12, 13, 14, 15, 26])) return 4; // Ultra Rare
        return 5; // Secret Rare (Special cards, promos, errors)
    }
}
