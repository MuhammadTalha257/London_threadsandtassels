<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index(): View
    {
        return view('admin.hero-slides.index', [
            'slides' => HeroSlide::with('product')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.hero-slides.form', [
            'slide' => new HeroSlide(['is_active' => true, 'sort_order' => (int) HeroSlide::max('sort_order') + 1]),
            'products' => Product::orderBy('name')->get(),
            'action' => route('admin.hero-slides.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        HeroSlide::create($this->validated($request));

        return redirect()->route('admin.hero-slides.index')->with('success', 'Header image added.');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        return view('admin.hero-slides.form', [
            'slide' => $heroSlide,
            'products' => Product::orderBy('name')->get(),
            'action' => route('admin.hero-slides.update', $heroSlide),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->update($this->validated($request, $heroSlide));

        return redirect()->route('admin.hero-slides.index')->with('success', 'Header image updated.');
    }

    /**
     * Detach the suit linked to a header image, leaving the image itself in place.
     */
    public function detachProduct(HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->update(['product_id' => null]);

        return back()->with('success', 'Linked suit removed from that header image.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        if ($heroSlide->image_path) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }
        $heroSlide->delete();

        return back()->with('success', 'Header image removed.');
    }

    private function validated(Request $request, ?HeroSlide $slide = null): array
    {
        $data = $request->validate([
            'kicker' => ['nullable', 'string', 'max:60'],
            'headline' => ['nullable', 'string', 'max:80'],
            'subhead' => ['nullable', 'string', 'max:180'],
            'discount_label' => ['nullable', 'string', 'max:40'],
            'cta_label' => ['nullable', 'string', 'max:40'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'image' => [$slide?->photo ? 'nullable' : 'required_without:image_url', 'nullable', 'image', 'max:8192'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'product_id' => ['nullable', 'exists:products,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            if ($slide?->image_path) {
                Storage::disk('public')->delete($slide->image_path);
            }
            $data['image_path'] = $request->file('image')->store('hero', 'public');
        }
        unset($data['image']);

        return $data;
    }
}
