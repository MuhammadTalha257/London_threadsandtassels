<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index(): View
    {
        return view('admin.campaigns.index', ['campaigns' => Campaign::with('product')->latest()->get()]);
    }

    public function create(): View
    {
        return view('admin.campaigns.form', [
            'campaign' => new Campaign,
            'products' => Product::orderBy('name')->get(),
            'action' => route('admin.campaigns.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Campaign::create($this->validated($request));

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign spotlight saved.');
    }

    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.form', [
            'campaign' => $campaign,
            'products' => Product::orderBy('name')->get(),
            'action' => route('admin.campaigns.update', $campaign),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($this->validated($request, $campaign));

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign spotlight updated.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        if ($campaign->image_path) {
            Storage::disk('public')->delete($campaign->image_path);
        }
        $campaign->delete();

        return back()->with('success', 'Campaign removed.');
    }

    private function validated(Request $request, ?Campaign $campaign = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:160'],
            'body' => ['nullable', 'string', 'max:400'],
            'image' => ['nullable', 'image', 'max:6144'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'cta_label' => ['nullable', 'string', 'max:40'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'exists:products,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($campaign?->image_path) {
                Storage::disk('public')->delete($campaign->image_path);
            }
            $data['image_path'] = $request->file('image')->store('campaigns', 'public');
        }
        unset($data['image']);

        return $data;
    }
}
