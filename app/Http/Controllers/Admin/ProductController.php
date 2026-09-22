<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.products.index', ['products' => Product::latest()->paginate(15)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product, 'action' => route('admin.products.store'), 'method' => 'POST', 'categories' => Category::orderBy('sort_order')->orderBy('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['gallery'] = $this->uploadGallery($request, []);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Suit added to the collection.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.form', ['product' => $product, 'action' => route('admin.products.update', $product), 'method' => 'PUT', 'categories' => Category::orderBy('sort_order')->orderBy('name')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);

        $remaining = collect($product->gallery ?? [])
            ->reject(fn (string $path) => in_array($path, (array) $request->input('remove_gallery', []), true))
            ->values()
            ->all();

        foreach ((array) $request->input('remove_gallery', []) as $path) {
            Storage::disk('public')->delete($path);
        }

        $data['gallery'] = $this->uploadGallery($request, $remaining);

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Suit updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Suit removed.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_on_sale' => ['nullable', 'boolean'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'gallery' => ['nullable', 'array', 'max:5'],
            'gallery.*' => ['image', 'max:4096'],
            'color' => ['nullable', 'string', 'max:80'],
            'color_hex' => ['nullable', 'string', 'max:7'],
            'colors' => ['nullable', 'string', 'max:400'],
            'sizes' => ['required', 'string', 'max:120'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_new_arrival' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.($product?->id ?? Str::lower(Str::random(5)));
        $data['sizes'] = array_values(array_filter(array_map('trim', explode(',', $data['sizes']))));
        $data['colors'] = $this->parseColors($request->input('colors'), $data['color_hex'] ?? null);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_new_arrival'] = $request->boolean('is_new_arrival');
        $data['is_on_sale'] = $request->boolean('is_on_sale');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        unset($data['image'], $data['gallery']);

        return $data;
    }

    /**
     * Parse a "Ruby=#7a1f2b, Emerald=#0b6e4f" style string into structured colour options.
     *
     * @return array<int, array{name: string, hex: string}>
     */
    private function parseColors(?string $raw, ?string $fallbackHex): array
    {
        $colors = [];
        foreach (array_filter(array_map('trim', explode(',', (string) $raw))) as $chunk) {
            [$name, $hex] = array_pad(array_map('trim', explode('=', $chunk, 2)), 2, null);
            if ($name === null || $name === '') {
                continue;
            }
            $hex = $hex ?: ($fallbackHex ?: '#151009');
            if (! preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
                $hex = $fallbackHex ?: '#151009';
            }
            $colors[] = ['name' => $name, 'hex' => $hex];
        }

        return $colors;
    }

    /**
     * Merge newly uploaded gallery photos onto whatever is left after removals, capped at 5.
     *
     * @param  array<int, string>  $remaining
     * @return array<int, string>
     */
    private function uploadGallery(Request $request, array $remaining): array
    {
        foreach ($request->file('gallery', []) as $file) {
            if (count($remaining) >= 5) {
                break;
            }
            $remaining[] = $file->store('products/gallery', 'public');
        }

        return array_slice($remaining, 0, 5);
    }
}
