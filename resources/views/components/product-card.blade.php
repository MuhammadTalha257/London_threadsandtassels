@props(['product'])
@php($hoverImage = $product->gallery_urls[1] ?? null)
<a href="{{ route('products.show', $product) }}" class="product-card group" data-motion data-cursor-view>
    <div class="product-card__image-wrap">
        @if($product->isSoldOut())
            <span class="badge badge--sold">Sold out</span>
        @elseif($product->isOnSale())
            <span class="badge badge--sale">{{ $product->discount_percent }}% off</span>
        @elseif($product->is_new_arrival)
            <span class="badge badge--new">New in</span>
        @endif
        <img src="{{ $product->photo }}" class="product-card__image" alt="{{ $product->name }}" loading="lazy">
        @if($hoverImage)
            <img src="{{ $hoverImage }}" class="product-card__image product-card__image--hover" alt="" aria-hidden="true" loading="lazy">
        @endif
    </div>
    <div class="product-card__details">
        <p class="product-card__name">{{ $product->name }}</p>
        <p class="product-card__brand">{{ $product->category?->name ?? 'Hand-embroidered' }}</p>
        <p class="product-card__price">
            @if($product->isOnSale())
                <span class="product-card__was">&pound;{{ number_format((float) $product->price, 2) }}</span>
                <span class="product-card__now">&pound;{{ number_format((float) $product->effective_price, 2) }}</span>
            @else
                &pound;{{ number_format((float) $product->price, 2) }}
            @endif
        </p>
        @if(!$product->isSoldOut() && $product->stock <= 3)
            <p class="product-card__low">Only {{ $product->stock }} left</p>
        @endif
    </div>
</a>
