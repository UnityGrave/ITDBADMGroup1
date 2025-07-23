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

        return Product::search($this->query)
            ->query(function ($builder) {
                $builder->with(['card.set', 'card.rarity', 'card.category', 'inventory']);
            })
            ->take(5)
            ->get();
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
