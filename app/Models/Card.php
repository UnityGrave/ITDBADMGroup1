<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Card extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'collector_number',
        'set_id',
        'rarity_id',
        'category_id',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        // Eager load relationships to avoid N+1 queries
        $this->load(['set', 'rarity', 'category']);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'collector_number' => $this->collector_number,
            // Set details
            'set_name' => $this->set->name,
            // Rarity details
            'rarity_name' => $this->rarity->name,
            // Category details
            'category_name' => $this->category->name,
            // Combined searchable text
            'searchable_text' => implode(' ', [
                $this->name,
                $this->collector_number,
                $this->set->name,
                $this->rarity->name,
                $this->category->name,
            ]),
        ];
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function rarity()
    {
        return $this->belongsTo(Rarity::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
} 