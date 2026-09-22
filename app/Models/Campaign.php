<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Campaign extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'body', 'image_path', 'image_url',
        'cta_label', 'cta_url', 'product_id', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Campaign $campaign): void {
            if ($campaign->is_active) {
                static::query()->where('id', '!=', $campaign->id)->update(['is_active' => false]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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

    public static function current(): ?self
    {
        try {
            return static::with('product')->where('is_active', true)->latest()->first();
        } catch (\Throwable $e) {
            // Table not migrated yet — fail soft so the storefront still renders.
            return null;
        }
    }
}
