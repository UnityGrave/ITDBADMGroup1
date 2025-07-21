<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Set;

class SetsPage extends Component
{
    public $sets;
    public $name;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:sets,name',
    ];

    public function mount()
    {
        $this->sets = Set::all();
    }

    public function render()
    {
        return view('livewire.admin.sets-page');
    }

    public function create()
    {
        $this->validate();
        Set::create(['name' => $this->name]);
        $this->reset(['name']);
        $this->sets = Set::all();
    }

    public function edit($id)
    {
        $set = Set::findOrFail($id);
        $this->editingId = $id;
        $this->name = $set->name;
    }

    public function update()
    {
        $this->validate(['name' => 'required|string|max:255|unique:sets,name,' . $this->editingId]);
        $set = Set::findOrFail($this->editingId);
        $set->update(['name' => $this->name]);
        $this->reset(['name', 'editingId']);
        $this->sets = Set::all();
    }

    public function delete($id)
    {
        Set::destroy($id);
        $this->sets = Set::all();
    }
} 