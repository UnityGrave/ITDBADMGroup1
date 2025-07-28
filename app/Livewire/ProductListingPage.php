<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Set;
use App\Models\Category;
use App\Models\Rarity;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductListingPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 12;

    #[Url]
    public array $filters = [
        'sets' => [],
        'categories' => [],
        'rarityId' => null,
        'sort' => 'created_at_desc',
    ];

    #[Url]
    public ?string $search = null;

    public array $selectedSets = [];
    public array $selectedCategories = [];
    public ?int $selectedRarityId = null;
    public string $sort = 'created_at_desc';

    public function mount()
    {
        // Handle category from URL query parameter
        $category = Request::query('category');
        if ($category) {
            $categoryId = Category::where('name', 'LIKE', $category)->first()?->id;
            if ($categoryId) {
                $this->filters['categories'] = [$categoryId];
            }
        }

        // Handle search from URL query parameter
        $this->search = Request::query('search');

        // Initialize selected values from filters
        $this->selectedSets = array_map('intval', array_filter($this->filters['sets'] ?? [], 'is_numeric'));
        $this->selectedCategories = array_map('intval', array_filter($this->filters['categories'] ?? [], 'is_numeric'));
        $this->selectedRarityId = $this->filters['rarityId'] ? (int) $this->filters['rarityId'] : null;
        $this->sort = $this->filters['sort'] ?? 'created_at_desc';
    }

    public function updatedFilters($value, $key)
    {
        // Update the selected values from filters
        // Ensure we're working with integers for IDs
        $this->selectedSets = array_map('intval', array_filter($this->filters['sets'] ?? [], 'is_numeric'));
        $this->selectedCategories = array_map('intval', array_filter($this->filters['categories'] ?? [], 'is_numeric'));
        $this->selectedRarityId = $this->filters['rarityId'] ? (int) $this->filters['rarityId'] : null;
        $this->sort = $this->filters['sort'] ?? 'created_at_desc';
        
        // Reset to first page when filters change
        $this->resetPage();
        
        // Force a re-render to ensure the component updates
        $this->dispatch('filters-updated');
        

    }

    public function updatedSearch()
    {
        // Reset to first page when search changes
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = null;
        $this->resetPage();
    }

    public function clearAllFilters()
    {
        $this->search = null;
        $this->filters = [
            'sets' => [],
            'categories' => [],
            'rarityId' => null,
            'sort' => 'created_at_desc',
        ];
        $this->selectedSets = [];
        $this->selectedCategories = [];
        $this->selectedRarityId = null;
        $this->sort = 'created_at_desc';
        $this->resetPage();
        

    }

    public function applyFilters()
    {
        // This method is now redundant since filters are applied automatically via wire:model.live
        // But keeping it for the button in case it's needed
        $this->selectedSets = array_map('intval', $this->filters['sets'] ?? []);
        $this->selectedCategories = array_map('intval', $this->filters['categories'] ?? []);
        $this->selectedRarityId = $this->filters['rarityId'] ? (int) $this->filters['rarityId'] : null;
        $this->sort = $this->filters['sort'] ?? 'created_at_desc';
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        try {
            // Start with a base query
            $query = Product::query();

            // If we have a search term, get product IDs from Scout
            if ($this->search && strlen($this->search) >= 2) {
                try {
                    $searchResults = Product::search($this->search)
                        ->get()
                        ->pluck('id')
                        ->toArray();

                    if (!empty($searchResults)) {
                        $query->whereIn('id', $searchResults);
                    } else {
                        // Return empty paginator if no search results
                        return new LengthAwarePaginator(
                            [], // Empty array of items
                            0,  // Total items
                            $this->perPage, // Items per page
                            $this->page ?? 1 // Current page
                        );
                    }
                } catch (\Exception $e) {
                    // If search fails, fall back to basic text search
                    $query->whereHas('card', function ($q) {
                        $q->where('name', 'LIKE', '%' . $this->search . '%');
                    });
                }
            }

            // Apply eager loading with error handling
            $query->with(['card.set', 'card.category', 'card.rarity', 'inventory']);
            
            // Ensure we only get products that have all required relationships
            $query->whereHas('card', function ($q) {
                $q->whereHas('set')
                  ->whereHas('category')
                  ->whereHas('rarity');
            });

            // Apply filters
            if (!empty($this->selectedSets)) {
                $query->whereHas('card.set', fn ($q) => $q->whereIn('id', $this->selectedSets));
            }

            if (!empty($this->selectedCategories)) {
                $query->whereHas('card.category', fn ($q) => $q->whereIn('id', $this->selectedCategories));
            }

            if ($this->selectedRarityId) {
                $query->whereHas('card.rarity', fn ($q) => $q->where('id', $this->selectedRarityId));
            }

            // Apply sorting
            match ($this->sort) {
                'price_asc' => $query->orderBy('base_price_cents', 'asc'),
                'price_desc' => $query->orderBy('base_price_cents', 'desc'),
                'name_asc' => $query
                    ->join('cards', 'products.card_id', '=', 'cards.id')
                    ->orderBy('cards.name', 'asc'),
                'set_asc' => $query
                    ->join('cards', 'products.card_id', '=', 'cards.id')
                    ->join('sets', 'cards.set_id', '=', 'sets.id')
                    ->orderBy('sets.name', 'asc'),
                default => $query->orderBy('products.created_at', 'desc'),
            };

            $results = $query->paginate($this->perPage);
            

            
            return $results;
        } catch (\Exception $e) {
            // Return empty results on error
            return new LengthAwarePaginator(
                [], // Empty array of items
                0,  // Total items
                $this->perPage, // Items per page
                $this->page ?? 1 // Current page
            );
        }
    }

    public function getSetsProperty() { return Set::orderBy('name')->get(); }
    public function getCategoriesProperty() { return Category::orderBy('name')->get(); }
    public function getRaritiesProperty() { return Rarity::orderBy('name')->get(); }

    /**
     * Debug method to check filter state
     */
    public function debugFilters()
    {
        return [
            'search' => $this->search,
            'filters' => $this->filters,
            'selectedSets' => $this->selectedSets,
            'selectedCategories' => $this->selectedCategories,
            'selectedRarityId' => $this->selectedRarityId,
            'sort' => $this->sort,
            'productCount' => $this->products->total(),
        ];
    }



    public function render()
    {
        return view('livewire.pages.product-listing-page');
    }
}
