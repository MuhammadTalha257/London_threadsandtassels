<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_lists_only_available_suits(): void
    {
        $availableSuit = Product::query()->create([
            'name' => 'Noor Suit', 'slug' => 'noor-suit', 'description' => 'Embroidered tailoring.',
            'price' => 249, 'sizes' => ['S', 'M'], 'stock' => 3, 'is_active' => true,
        ]);
        Product::query()->create([
            'name' => 'Hidden Suit', 'slug' => 'hidden-suit', 'description' => 'Not for sale.',
            'price' => 249, 'sizes' => ['S', 'M'], 'stock' => 0, 'is_active' => true,
        ]);

        $this->get('/')
            ->assertSee($availableSuit->name)
            ->assertDontSee('Hidden Suit');
    }
}
