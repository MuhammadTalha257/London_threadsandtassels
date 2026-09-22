<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@londonthreads.test'],
            ['name' => 'London Threads Admin', 'password' => 'password', 'is_admin' => true]
        );

        $returning = User::query()->updateOrCreate(
            ['email' => 'amelia@example.test'],
            [
                'name' => 'Amelia Clarke',
                'password' => 'password',
                'is_admin' => false,
                'phone' => '+44 7700 900123',
                'address' => "14 Rosewood Lane\nRichmond, London\nTW9 2AF, United Kingdom",
            ]
        );

        // The brand's own real product photography — three hand-finished suits, each shot from
        // multiple angles so a shopper can see the full garment before choosing a size.
        $suits = [
            [
                'name' => 'Zohra Ivory Jacket & Tie-Dye Skirt Set',
                'price' => 259.00,
                'color' => 'Ivory Zari', 'color_hex' => '#f1e4c9',
                'colors' => [['name' => 'Ivory Zari', 'hex' => '#f1e4c9'], ['name' => 'Sunset Orange', 'hex' => '#ff6a1f'], ['name' => 'Magenta Pink', 'hex' => '#e6007e']],
                'stock' => 9, 'is_featured' => true, 'is_new_arrival' => true,
                'image_path' => 'products/ivory-orange-front.jpg',
                'gallery' => ['products/gallery/ivory-orange-back.jpg', 'products/gallery/ivory-orange-side.jpg'],
                'description' => 'An embroidered ivory zari jacket, cut in a relaxed silhouette and finished with a woven floral border, layered over a hand tie-dyed lehenga skirt in sunset orange and magenta scattered with mirror-bright thread work. Paired here with a saffron dupatta and a jhumka hair tassel for a look built for golden-hour celebrations.',
            ],
            [
                'name' => 'Meera Emerald Bandhani Kurta & Banarasi Palazzo Set',
                'price' => 225.00, 'sale_price' => 179.00, 'is_on_sale' => true,
                'color' => 'Emerald Bandhani', 'color_hex' => '#0b6e4f',
                'colors' => [['name' => 'Emerald Bandhani', 'hex' => '#0b6e4f'], ['name' => 'Rose Pink', 'hex' => '#e6407a']],
                'stock' => 11, 'is_featured' => true, 'is_new_arrival' => false,
                'image_path' => 'products/emerald-bandhani-front.jpg',
                'gallery' => ['products/gallery/emerald-bandhani-front2.jpg', 'products/gallery/emerald-bandhani-back.jpg'],
                'description' => 'A bandhani-dyed emerald kurta with a contrast rose-pink brocade yoke, sewn from hand-tied cotton silk. Paired with shimmering pink-and-gold Banarasi palazzos and a soft fuchsia dupatta, this set moves easily between a daytime mehndi and an evening dinner.',
            ],
            [
                'name' => 'Alia Aqua Floral Embroidered Top & Skirt Set',
                'price' => 289.00, 'sale_price' => 219.00, 'is_on_sale' => true,
                'color' => 'Aqua Floral', 'color_hex' => '#3fb6ad',
                'colors' => [['name' => 'Aqua Floral', 'hex' => '#3fb6ad'], ['name' => 'Ivory Gold', 'hex' => '#efe6c8']],
                'stock' => 4, 'is_featured' => true, 'is_new_arrival' => true,
                'image_path' => 'products/aqua-floral-front.jpg',
                'gallery' => [],
                'description' => 'A hand-embroidered aqua silk top scattered with metallic-thread floral blooms, layered over a shimmering ivory-gold pleated skirt finished with hand-sewn sequins. A statement piece for receptions and sangeet nights.',
            ],
        ];

        // Remove any older placeholder products that are not part of the current real-photo edit.
        Product::query()->whereNotIn('slug', collect($suits)->map(fn ($s) => Str::slug($s['name']))->all())->delete();

        $products = collect();
        foreach ($suits as $suit) {
            $products->push(Product::query()->updateOrCreate(
                ['slug' => Str::slug($suit['name'])],
                [
                    ...$suit,
                    'slug' => Str::slug($suit['name']),
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'is_active' => true,
                ]
            ));
        }

        if (! $returning->orders()->exists()) {
            $first = $products->first();
            $second = $products->skip(1)->first();
            $subtotal = (float) $first->price + (float) $second->price;

            $order = Order::create([
                'user_id' => $returning->id,
                'order_number' => 'LTT-DEMO0001',
                'status' => 'paid',
                'subtotal' => $subtotal,
                'shipping' => 0,
                'total' => $subtotal,
                'shipping_name' => $returning->name,
                'shipping_email' => $returning->email,
                'shipping_phone' => $returning->phone,
                'shipping_address' => $returning->address,
                'created_at' => now()->subDays(18),
                'updated_at' => now()->subDays(18),
            ]);

            $order->items()->create(['product_id' => $first->id, 'product_name' => $first->name, 'size' => 'M', 'quantity' => 1, 'unit_price' => $first->price, 'line_total' => $first->price]);
            $order->items()->create(['product_id' => $second->id, 'product_name' => $second->name, 'size' => 'S', 'quantity' => 1, 'unit_price' => $second->price, 'line_total' => $second->price]);

            Payment::create([
                'order_id' => $order->id,
                'provider' => 'paypal',
                'provider_reference' => 'DEMO-LTT0001',
                'amount' => $subtotal,
                'currency' => 'GBP',
                'status' => 'completed',
                'paid_at' => now()->subDays(18),
            ]);
        }

        Promotion::query()->updateOrCreate(
            ['title' => 'FESTIVE SALE'],
            [
                'message' => 'Up to 35% off selected suits, while stocks last.',
                'cta_label' => 'Shop the sale',
                'cta_url' => '/#sale',
                'is_active' => true,
            ]
        );

        $spotlightProduct = $products->skip(2)->first() ?? $products->first();

        Campaign::query()->updateOrCreate(
            ['title' => 'THE FLAGSHIP VELVET'],
            [
                'subtitle' => 'Our most intricate hand-embroidered piece this season.',
                'body' => 'Photographed in full for the festive edit.',
                'image_path' => 'campaigns/model-flagship-velvet.jpg',
                'image_url' => null,
                'cta_label' => 'Shop the piece',
                'product_id' => $spotlightProduct?->id,
                'is_active' => true,
            ]
        );
    }
}
