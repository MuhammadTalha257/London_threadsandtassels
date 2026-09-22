<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'sale_price', 'image_url', 'image_path', 'gallery',
        'color', 'color_hex', 'colors', 'sizes', 'stock', 'is_active', 'is_featured', 'is_new_arrival', 'is_on_sale',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'sizes' => 'array',
            'gallery' => 'array',
            'colors' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_on_sale' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNewArrivals(Builder $query): Builder
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeOnSale(Builder $query): Builder
    {
        return $query->where('is_on_sale', true);
    }

    /**
     * The resolved primary image: an uploaded file takes priority over a linked URL.
     */
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->image_path ? Storage::disk('public')->url($this->image_path) : $this->image_url,
        );
    }

    /**
     * Every image for this product (primary photo first), for the hover slideshow.
     *
     * @return array<int, string>
     */
    protected function galleryUrls(): Attribute
    {
        return Attribute::make(get: function (): array {
            $urls = [];
            if ($this->photo) {
                $urls[] = $this->photo;
            }
            foreach ((array) $this->gallery as $path) {
                $urls[] = str_starts_with((string) $path, 'http') ? $path : Storage::disk('public')->url($path);
            }

            return array_values(array_unique($urls));
        });
    }

    /**
     * Normalised colour options: [['name' => 'Ruby', 'hex' => '#7a1f2b'], ...].
     * Falls back to the single colour/hex pair when no multi-colour list is set.
     *
     * @return array<int, array{name: string, hex: string}>
     */
    protected function colorOptions(): Attribute
    {
        return Attribute::make(get: function (): array {
            $options = [];
            foreach ((array) $this->colors as $entry) {
                if (is_array($entry) && ! empty($entry['name'])) {
                    $options[] = ['name' => (string) $entry['name'], 'hex' => (string) ($entry['hex'] ?? '#151009')];
                }
            }
            if ($options === [] && $this->color) {
                $options[] = ['name' => (string) $this->color, 'hex' => (string) ($this->color_hex ?: '#151009')];
            }

            return $options;
        });
    }

    /**
     * The price a shopper actually pays right now.
     */
    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->isOnSale() ? $this->sale_price : $this->price,
        );
    }

    protected function discountPercent(): Attribute
    {
        return Attribute::make(get: function (): ?int {
            if (! $this->isOnSale() || (float) $this->price <= 0) {
                return null;
            }

            return (int) round((1 - ((float) $this->sale_price / (float) $this->price)) * 100);
        });
    }

    public function isOnSale(): bool
    {
        return $this->is_on_sale && $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    public function isSoldOut(): bool
    {
        return $this->stock <= 0;
    }
}
