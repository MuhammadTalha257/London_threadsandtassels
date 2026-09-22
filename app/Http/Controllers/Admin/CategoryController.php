<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('products')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category, 'action' => route('admin.categories.store'), 'method' => 'POST']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category added.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category, 'action' => route('admin.categories.update', $category), 'method' => 'PUT']);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request);
        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Products in this category simply fall back to "uncategorised" — nothing is deleted.
        $category->delete();

        return back()->with('success', 'Category removed.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /**
     * Slugify a category name, disambiguating with -2, -3, … if it collides
     * with an existing category (excluding the one currently being saved).
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $suffix = 2;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereNot('id', $ignoreId))->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
