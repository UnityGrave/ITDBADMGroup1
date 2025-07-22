<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Set;
use App\Models\Category;
use App\Models\Rarity;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sets = Set::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $rarities = Rarity::orderBy('name')->get();

        $query = Product::with(['card.set', 'card.category', 'card.rarity', 'inventory']);

        if ($request->filled('sets')) {
            $query->whereHas('card.set', fn ($q) => $q->whereIn('id', $request->input('sets')));
        }

        if ($request->filled('categories')) {
            $query->whereHas('card.category', fn ($q) => $q->whereIn('id', $request->input('categories')));
        }

        if ($request->filled('rarity')) {
            $query->whereHas('card.rarity', fn ($q) => $q->where('id', $request->input('rarity')));
        }

        $sort = $request->input('sort', 'created_at_desc');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->join('cards', 'products.card_id', '=', 'cards.id')->orderBy('cards.name', 'asc'),
            'set_asc' => $query->join('cards', 'products.card_id', '=', 'cards.id')
                                ->join('sets', 'cards.set_id', '=', 'sets.id')
                                ->orderBy('sets.name', 'asc'),
            default => $query->orderBy('products.created_at', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('products.index', compact('products', 'sets', 'categories', 'rarities', 'sort'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
