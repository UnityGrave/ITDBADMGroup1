<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Card;
use App\Models\Inventory;
use App\Enums\ProductCondition;

class ProductsPage extends Component
{
    public $products;
    public $card_id;
    public $condition;
    public $price;
    public $sku;
    public $stock;
    public $editingId = null;

    protected $rules = [
        'card_id' => 'required|exists:cards,id',
        'condition' => 'required|string',
        'price' => 'required|numeric|min:0',
        'sku' => 'required|string|unique:products,sku',
        'stock' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->products = Product::with(['card', 'inventory'])->get();
    }

    public function render()
    {
        return view('livewire.admin.products-page', [
            'cards' => Card::all(),
            'conditions' => ProductCondition::cases(),
        ]);
    }

    public function create()
    {
        $this->validate();
        
        // Convert price from decimal USD to cents
        $priceInCents = (int)($this->price * 100);
        
        $product = Product::create([
            'card_id' => $this->card_id,
            'condition' => $this->condition,
            'base_price_cents' => $priceInCents,
            'sku' => $this->sku,
        ]);
        Inventory::create([
            'product_id' => $product->id,
            'stock' => $this->stock,
        ]);
        $this->reset(['card_id', 'condition', 'price', 'sku', 'stock']);
        $this->products = Product::with(['card', 'inventory'])->get();
    }

    public function edit($id)
    {
        $product = Product::with('inventory')->findOrFail($id);
        $this->editingId = $id;
        $this->card_id = $product->card_id;
        $this->condition = $product->condition->value;
        // Convert from cents to decimal USD for display
        $this->price = $product->base_price_cents / 100;
        $this->sku = $product->sku;
        $this->stock = $product->inventory->stock ?? 0;
    }

    public function update()
    {
        $this->validate([
            'card_id' => 'required|exists:cards,id',
            'condition' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sku' => 'required|string|unique:products,sku,' . $this->editingId,
            'stock' => 'required|integer|min:0',
        ]);
        
        $product = Product::findOrFail($this->editingId);
        
        // Convert price from decimal USD to cents
        $priceInCents = (int)($this->price * 100);
        
        $product->update([
            'card_id' => $this->card_id,
            'condition' => $this->condition,
            'base_price_cents' => $priceInCents,
            'sku' => $this->sku,
        ]);
        $product->inventory()->updateOrCreate(
            ['product_id' => $product->id],
            ['stock' => $this->stock]
        );
        $this->reset(['card_id', 'condition', 'price', 'sku', 'stock', 'editingId']);
        $this->products = Product::with(['card', 'inventory'])->get();
    }

    public function delete($id)
    {
        Product::destroy($id);
        $this->products = Product::with(['card', 'inventory'])->get();
    }
} 