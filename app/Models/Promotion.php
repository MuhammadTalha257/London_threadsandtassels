<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Promotion extends Model
{
    protected $fillable = ['title', 'message', 'discount_label', 'ends_at', 'cta_label', 'cta_url', 'image_path', 'image_url', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'ends_at' => 'datetime'];
    }

    /**
     * The resolved banner image: an uploaded file takes priority over a linked URL.
     */
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->image_path ? Storage::disk('public')->url($this->image_path) : $this->image_url,
        );
    }

    protected static function booted(): void
    {
        static::saving(function (Promotion $promotion): void {
            if ($promotion->is_active) {
                static::query()->where('id', '!=', $promotion->id)->update(['is_active' => false]);
            }
        });
    }

    /**
     * The live festival / event offer shown in the header ribbon.
     * An offer whose end date has passed switches itself off.
     */
    public static function current(): ?self
    {
        try {
            $promotion = static::where('is_active', true)->latest()->first();
        } catch (\Throwable $e) {
            // Table not migrated yet — fail soft so the storefront still renders.
            return null;
        }

        if ($promotion?->ends_at?->isPast()) {
            return null;
        }

        return $promotion;
    }
}
