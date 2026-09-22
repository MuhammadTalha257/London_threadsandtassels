<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true).' Suit';

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 120, 350),
            'image_url' => 'https://images.unsplash.com/photo-1645862755924-9f4e7f200b83?auto=format&fit=crop&w=1200&q=85',
            'color' => fake()->safeColorName(),
            'color_hex' => fake()->hexColor(),
            'sizes' => ['S', 'M', 'L', 'XL'],
            'stock' => fake()->numberBetween(0, 20),
            'is_active' => true,
            'is_featured' => false,
            'is_new_arrival' => false,
        ];
    }
}
