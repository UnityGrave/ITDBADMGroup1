<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Rarity;

class RaritiesPage extends Component
{
    public $rarities;
    public $name;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:rarities,name',
    ];

    public function mount()
    {
        $this->rarities = Rarity::all();
    }

    public function render()
    {
        return view('livewire.admin.rarities-page');
    }

    public function create()
    {
        $this->validate();
        Rarity::create(['name' => $this->name]);
        $this->reset(['name']);
        $this->rarities = Rarity::all();
    }

    public function edit($id)
    {
        $rarity = Rarity::findOrFail($id);
        $this->editingId = $id;
        $this->name = $rarity->name;
    }

    public function update()
    {
        $this->validate(['name' => 'required|string|max:255|unique:rarities,name,' . $this->editingId]);
        $rarity = Rarity::findOrFail($this->editingId);
        $rarity->update(['name' => $this->name]);
        $this->reset(['name', 'editingId']);
        $this->rarities = Rarity::all();
    }

    public function delete($id)
    {
        Rarity::destroy($id);
        $this->rarities = Rarity::all();
    }
} 