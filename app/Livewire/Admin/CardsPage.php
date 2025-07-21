<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Card;
use App\Models\Set;
use App\Models\Rarity;
use App\Models\Category;

class CardsPage extends Component
{
    public $cards;
    public $name;
    public $collector_number;
    public $set_id;
    public $rarity_id;
    public $category_id;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'collector_number' => 'required|string|max:255',
        'set_id' => 'required|exists:sets,id',
        'rarity_id' => 'required|exists:rarities,id',
        'category_id' => 'required|exists:categories,id',
    ];

    public function mount()
    {
        $this->cards = Card::with(['set', 'rarity', 'category'])->get();
    }

    public function render()
    {
        return view('livewire.admin.cards-page', [
            'sets' => Set::all(),
            'rarities' => Rarity::all(),
            'categories' => Category::all(),
        ]);
    }

    public function create()
    {
        $this->validate();
        Card::create([
            'name' => $this->name,
            'collector_number' => $this->collector_number,
            'set_id' => $this->set_id,
            'rarity_id' => $this->rarity_id,
            'category_id' => $this->category_id,
        ]);
        $this->reset(['name', 'collector_number', 'set_id', 'rarity_id', 'category_id']);
        $this->cards = Card::with(['set', 'rarity', 'category'])->get();
    }

    public function edit($id)
    {
        $card = Card::findOrFail($id);
        $this->editingId = $id;
        $this->name = $card->name;
        $this->collector_number = $card->collector_number;
        $this->set_id = $card->set_id;
        $this->rarity_id = $card->rarity_id;
        $this->category_id = $card->category_id;
    }

    public function update()
    {
        $this->validate();
        $card = Card::findOrFail($this->editingId);
        $card->update([
            'name' => $this->name,
            'collector_number' => $this->collector_number,
            'set_id' => $this->set_id,
            'rarity_id' => $this->rarity_id,
            'category_id' => $this->category_id,
        ]);
        $this->reset(['name', 'collector_number', 'set_id', 'rarity_id', 'category_id', 'editingId']);
        $this->cards = Card::with(['set', 'rarity', 'category'])->get();
    }

    public function delete($id)
    {
        Card::destroy($id);
        $this->cards = Card::with(['set', 'rarity', 'category'])->get();
    }
} 