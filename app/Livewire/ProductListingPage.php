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

class ProductListingPage extends Component
{
    use WithPagination;

    public $perPage = 12;

    #[Url]
    public array $filters = [
        'sets' => [],
        'categories' => [],
        'rarityId' => null,
        'sort' => 'created_at_desc',
    ];

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

        $this->applyFilters();
    }

    public function updatedFilters($value, $key)
    {
        $this->applyFilters();
    }

    public function applyFilters()
    {
        $this->selectedSets = array_map('intval', $this->filters['sets'] ?? []);
        $this->selectedCategories = array_map('intval', $this->filters['categories'] ?? []);
        $this->selectedRarityId = $this->filters['rarityId'] ? (int) $this->filters['rarityId'] : null;
        $this->sort = $this->filters['sort'] ?? 'created_at_desc';
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        $query = Product::with(['card.set', 'card.category', 'card.rarity', 'inventory']);

        if ($this->selectedSets) {
            $query->whereHas('card.set', fn ($q) => $q->whereIn('id', $this->selectedSets));
        }

        if ($this->selectedCategories) {
            $query->whereHas('card.category', fn ($q) => $q->whereIn('id', $this->selectedCategories));
        }

        if ($this->selectedRarityId) {
            $query->whereHas('card.rarity', fn ($q) => $q->where('id', $this->selectedRarityId));
        }

        match ($this->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query
                ->join('cards', 'products.card_id', '=', 'cards.id')
                ->orderBy('cards.name', 'asc'),
            'set_asc' => $query
                ->join('cards', 'products.card_id', '=', 'cards.id')
                ->join('sets', 'cards.set_id', '=', 'sets.id')
                ->orderBy('sets.name', 'asc'),
            default => $query->orderBy('products.created_at', 'desc'),
        };

        return $query->paginate($this->perPage);
    }

    public function getSetsProperty() { return Set::orderBy('name')->get(); }
    public function getCategoriesProperty() { return Category::orderBy('name')->get(); }
    public function getRaritiesProperty() { return Rarity::orderBy('name')->get(); }

    public function render()
    {
        return view('livewire.pages.product-listing-page');
    }
}
