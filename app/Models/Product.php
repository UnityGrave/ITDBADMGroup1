<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'condition',
        'price',
        'sku',
    ];

    protected $casts = [
        'price' => 'float',
        'condition' => \App\Enums\ProductCondition::class,
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
} 