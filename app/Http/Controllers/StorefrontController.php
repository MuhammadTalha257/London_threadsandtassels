<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class StorefrontController extends Controller
{
    public function index(): View
    {
        $safe = function (callable $query) {
            try { return $query(); } catch (\Throwable $e) { return new Collection; }
        };

        $products = $safe(fn () => Product::available()->latest()->get());

        // Header images for the scroll scene. The admin manages these under
        // "Header images"; until any exist we fall back to the brand's own
        // campaign photography plus the current suits.
        $heroScenes = HeroSlide::live()->map(function (HeroSlide $slide) {
            $product = $slide->product?->is_active ? $slide->product : null;

            return [
                'image' => $slide->photo,
                'kicker' => $slide->kicker,
                'headline' => $slide->headline,
                'subhead' => $slide->subhead,
                'discount' => $slide->discount_label,
                'cta_label' => $slide->cta_label ?: ($product ? 'Shop this look' : 'Shop the collection'),
                'cta_url' => $slide->cta_url ?: ($product ? route('products.show', $product) : route('shop')),
                'product' => $product,
            ];
        });

        if ($heroScenes->isEmpty()) {
            $heroScenes = collect([
                ['image' => '/images/hero/hero-1.jpg', 'kicker' => 'The festive edit', 'headline' => 'Dressed for every celebration', 'subhead' => 'Hand-embroidered occasion suits, finished by hand in small batches in our London atelier.', 'discount' => null, 'cta_label' => 'Shop the collection', 'cta_url' => route('shop'), 'product' => null],
                ['image' => '/images/hero/hero-2.jpg', 'kicker' => 'Thread by thread', 'headline' => 'Made to hold a room', 'subhead' => 'Designed by Akansha, tailored in small batches in London.', 'discount' => null, 'cta_label' => 'Shop the collection', 'cta_url' => route('shop'), 'product' => null],
            ]);
        }

        // The collage the header slide lands in on scroll: the current suits
        // first, topped up with the other header images.
        $galleryScenes = $products->filter(fn (Product $product) => filled($product->photo))
            ->map(fn (Product $product) => [
                'image' => $product->photo, 'headline' => $product->name, 'link' => route('products.show', $product),
            ])
            ->concat($heroScenes->map(fn (array $scene) => [
                'image' => $scene['image'], 'headline' => $scene['headline'], 'link' => $scene['cta_url'],
            ]))
            ->unique('image')
            ->take(8)
            ->values();

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
            'heroScenes' => $heroScenes,
            'galleryScenes' => $galleryScenes,
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
