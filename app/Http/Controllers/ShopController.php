<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        try {
            $products = Product::available()->with('category')->latest()->get();
        } catch (\Throwable $e) {
            $products = new \Illuminate\Support\Collection;
        }

        // Distinct colour options across the catalogue, for the filter bar.
        $colors = $products
            ->flatMap(fn (Product $p) => collect($p->color_options)->pluck('name'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        try {
            $categories = \App\Models\Category::active()->get();
        } catch (\Throwable $e) {
            $categories = new \Illuminate\Support\Collection;
        }

        return view('store.shop', [
            'products' => $products,
            'colors' => $colors,
            'categories' => $categories,
        ]);
    }
}
