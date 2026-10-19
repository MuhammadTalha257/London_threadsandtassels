<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class HeroSlide extends Model
{
    protected $fillable = ['kicker', 'headline', 'subhead', 'discount_label', 'cta_label', 'cta_url', 'image_path', 'image_url', 'product_id', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The resolved image: an uploaded file takes priority over a linked URL.
     */
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->image_path ? Storage::disk('public')->url($this->image_path) : $this->image_url,
        );
    }

    /**
     * Live header images for the storefront. Fails soft (empty collection)
     * when the table hasn't been migrated yet.
     *
     * @return Collection<int, self>
     */
    public static function live(): Collection
    {
        try {
            return static::with('product')->active()->get()->filter(fn (self $slide) => filled($slide->photo))->values();
        } catch (\Throwable $e) {
            return new Collection;
        }
    }
}
