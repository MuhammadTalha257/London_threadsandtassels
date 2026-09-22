<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class StorefrontController extends Controller
{
    public function index(): View
    {
        // Products/columns may not exist until migrations run — never blank the page.
        try {
            $heroProducts = Product::available()->where('is_featured', true)->latest()->take(4)->get();
        } catch (\Throwable $e) {
            $heroProducts = new Collection;
        }

        // The brand's own campaign photography — two slides only.
        // The eyebrow's year is computed at request time so it never goes stale.
        $editYear = now()->year;
        $backgrounds = [
            ['image' => '/images/hero/hero-1.jpg', 'eyebrow' => "The {$editYear} occasion edit", 'headline' => 'Tailored for the moment', 'subhead' => 'Embroidered suits made for entrances, anniversaries, late dinners and every celebration in between.'],
            ['image' => '/images/hero/hero-2.jpg', 'eyebrow' => 'Thread by thread', 'headline' => 'Made to hold a room', 'subhead' => 'Hand-embroidered in small batches, finished entirely by hand in our London atelier.'],
        ];

        $heroSlides = new Collection;
        foreach ($backgrounds as $index => $background) {
            $heroSlides->push([...$background, 'product' => $heroProducts[$index] ?? null]);
        }

        $safe = function (callable $query) {
            try { return $query(); } catch (\Throwable $e) { return new Collection; }
        };

        $products = $safe(fn () => Product::available()->latest()->get());

        // A short, curated "Trending Styles" rail — each of our own pieces given a style label,
        // in the spirit of the category carousels shoppers expect from Indian-occasion-wear sites.
        $styleLabels = [
            'zohra-ivory-jacket-tie-dye-skirt-set' => 'Jacket & Tie-Dye Skirt Sets',
            'meera-emerald-bandhani-kurta-banarasi-palazzo-set' => 'Bandhani Kurta & Palazzo Sets',
            'alia-aqua-floral-embroidered-top-skirt-set' => 'Floral Embroidered Sets',
        ];
        $trendingStyles = $products
            ->sortBy(fn ($product) => array_search($product->slug, array_keys($styleLabels)) ?: 99)
            ->map(fn ($product) => ['label' => $styleLabels[$product->slug] ?? 'Occasion Sets', 'product' => $product])
            ->values();

        return view('store.index', [
            'heroSlides' => $heroSlides,
            'campaign' => Campaign::current(),
            'saleProducts' => $safe(fn () => Product::available()->onSale()->latest()->take(8)->get()),
            'newArrivals' => $safe(fn () => Product::available()->newArrivals()->latest()->take(8)->get()),
            'products' => $products,
            'trendingStyles' => $trendingStyles,
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('store.show', [
            'product' => $product,
            'related' => Product::available()->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get(),
        ]);
    }
}
