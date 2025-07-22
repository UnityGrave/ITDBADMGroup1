<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductDetailController extends Controller
{
    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product->load('card.set', 'card.rarity', 'card.category', 'inventory'),
        ]);
    }
}
