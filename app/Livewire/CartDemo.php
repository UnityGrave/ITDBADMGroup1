<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class CartDemo extends Component
{
    public $products = [];

    public function mount()
    {
        // Get some sample products for the demo
        $this->products = Product::with(['category', 'inventory'])->take(6)->get();
    }

    public function render()
    {
        return view('livewire.cart-demo')->layout('layouts.app');
    }
}
