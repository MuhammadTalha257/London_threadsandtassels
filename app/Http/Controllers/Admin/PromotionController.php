<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index(): View
    {
        return view('admin.promotions.index', ['promotions' => Promotion::latest()->get()]);
    }

    public function create(): View
    {
        return view('admin.promotions.form', ['promotion' => new Promotion, 'action' => route('admin.promotions.store'), 'method' => 'POST']);
    }

    public function store(Request $request): RedirectResponse
    {
        Promotion::create($this->validated($request));

        return redirect()->route('admin.promotions.index')->with('success', 'Festival offer saved.');
    }

    public function edit(Promotion $promotion): View
    {
        return view('admin.promotions.form', ['promotion' => $promotion, 'action' => route('admin.promotions.update', $promotion), 'method' => 'PUT']);
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($this->validated($request, $promotion));

        return redirect()->route('admin.promotions.index')->with('success', 'Festival offer updated.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        if ($promotion->image_path) {
            Storage::disk('public')->delete($promotion->image_path);
        }
        $promotion->delete();

        return back()->with('success', 'Festival offer removed.');
    }

    private function validated(Request $request, ?Promotion $promotion = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:160'],
            'discount_label' => ['nullable', 'string', 'max:40'],
            'ends_at' => ['nullable', 'date'],
            'cta_label' => ['nullable', 'string', 'max:40'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:6144'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($promotion?->image_path) {
                Storage::disk('public')->delete($promotion->image_path);
            }
            $data['image_path'] = $request->file('image')->store('promotions', 'public');
        }
        unset($data['image']);

        return $data;
    }
}
