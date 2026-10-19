<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroSlideTest extends TestCase
{
    use RefreshDatabase;

    private function suit(): Product
    {
        return Product::query()->create([
            'name' => 'Noor Suit', 'slug' => 'noor-suit', 'description' => 'Embroidered tailoring.',
            'price' => 249, 'sizes' => ['S', 'M'], 'stock' => 3, 'is_active' => true,
        ]);
    }

    public function test_homepage_plays_the_live_header_images_in_order(): void
    {
        HeroSlide::create(['image_url' => 'https://example.com/second.jpg', 'sort_order' => 2]);
        HeroSlide::create(['image_url' => 'https://example.com/first.jpg', 'headline' => 'Dressed for Diwali', 'sort_order' => 1]);
        HeroSlide::create(['image_url' => 'https://example.com/hidden.jpg', 'sort_order' => 0, 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['https://example.com/first.jpg', 'Dressed for Diwali', 'https://example.com/second.jpg'], false)
            ->assertDontSee('https://example.com/hidden.jpg', false);
    }

    public function test_admin_can_remove_the_suit_linked_to_a_header_image(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $slide = HeroSlide::create(['image_url' => 'https://example.com/look.jpg', 'product_id' => $this->suit()->id]);

        $this->actingAs($admin)
            ->patch(route('admin.hero-slides.detach-product', $slide))
            ->assertRedirect();

        $this->assertNull($slide->fresh()->product_id);
        $this->assertNotNull($slide->fresh()->photo);
    }

    public function test_admin_can_delete_a_header_image(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $slide = HeroSlide::create(['image_url' => 'https://example.com/look.jpg']);

        $this->actingAs($admin)->delete(route('admin.hero-slides.destroy', $slide))->assertRedirect();

        $this->assertModelMissing($slide);
    }

    public function test_festival_offer_shows_in_the_header_until_it_ends(): void
    {
        Promotion::create(['title' => 'Diwali Festive Edit', 'message' => 'On every suit', 'discount_label' => '20% off', 'ends_at' => now()->addDays(3), 'is_active' => true]);

        $this->get('/')->assertSee('Diwali Festive Edit')->assertSee('20% off');

        $this->travel(4)->days();

        $this->get('/')->assertDontSee('Diwali Festive Edit');
    }
}
