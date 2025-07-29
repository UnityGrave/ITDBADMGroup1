<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;

class ProductSearch extends Component
{
    #[Url]
    public string $query = '';

    public bool $showResults = false;

    protected $queryString = [
        'query' => ['except' => '']
    ];

    public function mount()
    {
        $this->showResults = strlen($this->query) > 0;
    }

    public function updatedQuery()
    {
        $this->showResults = strlen($this->query) > 0;
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        // Check if the query contains a price search pattern
        if ($this->isPriceQuery($this->query)) {
            return $this->searchByPrice($this->query);
        }

        // Use Scout for text-based searches
        return Product::search($this->query)
            ->query(function ($builder) {
                $builder->with(['card.set', 'card.rarity', 'card.category', 'inventory'])
                        ->whereHas('inventory', function($q) {
                            $q->where('stock', '>', 0);
                        });
            })
            ->take(5)
            ->get();
    }

    /**
     * Check if the query is a price-based search
     */
    private function isPriceQuery(string $query): bool
    {
        // Detect price patterns like "$5", "5-10", "under 50", etc.
        return preg_match('/[\$£€¥]?\d+(\.\d{2})?(\s*-\s*[\$£€¥]?\d+(\.\d{2})?)?|(under|over|above|below)\s+[\$£€¥]?\d+(\.\d{2})?/i', $query);
    }

    /**
     * Search products by price range
     */
    private function searchByPrice(string $query)
    {
        $activeCurrency = \App\Models\Currency::getActiveCurrencyObject();
        $isUSD = $activeCurrency->code === 'USD';
        
        // Extract price values and convert to cents
        preg_match_all('/[\$£€¥]?(\d+(?:\.\d{2})?)/', $query, $matches);
        $prices = array_map(function($price) {
            return (float)$price * 100; // Convert to cents
        }, $matches[1]);

        $queryBuilder = Product::query()
            ->with(['card.set', 'card.rarity', 'card.category', 'inventory'])
            ->whereHas('inventory', function($q) {
                $q->where('stock', '>', 0);
            });

        if (preg_match('/under|below/i', $query) && !empty($prices)) {
            // "under $50" or "below $30"
            $maxPrice = $prices[0];
            if ($isUSD) {
                $queryBuilder->where('base_price_cents', '<=', $maxPrice);
            } else {
                // For non-USD, need to convert price range
                $queryBuilder->whereRaw('CAST(base_price_cents * ? AS SIGNED) <= ?', [
                    $activeCurrency->exchange_rate, $maxPrice
                ]);
            }
        } elseif (preg_match('/over|above/i', $query) && !empty($prices)) {
            // "over $100" or "above $200"
            $minPrice = $prices[0];
            if ($isUSD) {
                $queryBuilder->where('base_price_cents', '>=', $minPrice);
            } else {
                $queryBuilder->whereRaw('CAST(base_price_cents * ? AS SIGNED) >= ?', [
                    $activeCurrency->exchange_rate, $minPrice
                ]);
            }
        } elseif (count($prices) >= 2) {
            // Price range like "$5-$20" or "10-50"
            $minPrice = min($prices);
            $maxPrice = max($prices);
            if ($isUSD) {
                $queryBuilder->whereBetween('base_price_cents', [$minPrice, $maxPrice]);
            } else {
                $queryBuilder->whereRaw('CAST(base_price_cents * ? AS SIGNED) BETWEEN ? AND ?', [
                    $activeCurrency->exchange_rate, $minPrice, $maxPrice
                ]);
            }
        } elseif (!empty($prices)) {
            // Single price like "$15" - search around that price (±20%)
            $targetPrice = $prices[0];
            $minPrice = $targetPrice * 0.8;
            $maxPrice = $targetPrice * 1.2;
            if ($isUSD) {
                $queryBuilder->whereBetween('base_price_cents', [$minPrice, $maxPrice]);
            } else {
                $queryBuilder->whereRaw('CAST(base_price_cents * ? AS SIGNED) BETWEEN ? AND ?', [
                    $activeCurrency->exchange_rate, $minPrice, $maxPrice
                ]);
            }
        }

        return $queryBuilder->orderBy('base_price_cents', 'asc')->take(5)->get();
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
