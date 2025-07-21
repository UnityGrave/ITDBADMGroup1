<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'collector_number',
        'set_id',
        'rarity_id',
        'category_id',
    ];

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
} 