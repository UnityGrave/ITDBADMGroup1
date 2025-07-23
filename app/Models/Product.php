<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Enums\ProductCondition;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'card_id',
        'condition',
        'price',
        'sku',
    ];

    protected $casts = [
        'condition' => ProductCondition::class,
        'price' => 'decimal:2',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $this->load(['card.set', 'card.rarity', 'card.category', 'inventory']);

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'condition' => $this->condition->value,
            'price' => $this->price,
            'stock' => $this->inventory?->stock ?? 0,
            // Card details
            'card_name' => $this->card->name,
            'collector_number' => $this->card->collector_number,
            // Set details
            'set_name' => $this->card->set->name,
            // Rarity details
            'rarity_name' => $this->card->rarity->name,
            // Category details
            'category_name' => $this->card->category->name,
            // Combined searchable text
            'searchable_text' => implode(' ', [
                $this->card->name,
                $this->card->set->name,
                $this->card->rarity->name,
                $this->card->category->name,
                $this->condition->value,
                $this->sku,
            ]),
        ];
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        // Only make products searchable if they have stock or are visible
        return $this->inventory && $this->inventory->stock > 0;
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
} 