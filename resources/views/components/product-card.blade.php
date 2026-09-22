@props(['product'])
<a
    href="{{ route('products.show', $product) }}"
    class="product-card group"
    data-motion
    data-cursor-view
    @if(count($product->gallery_urls) > 1) data-gallery='@json($product->gallery_urls)' @endif
>
    <div class="product-card__image-wrap glow" data-glow>
        @if($product->isSoldOut())
            <span class="badge badge--sold">Sold Out</span>
        @elseif($product->isOnSale())
            <span class="badge badge--sale">Sale &minus;{{ $product->discount_percent }}%</span>
        @elseif($product->is_new_arrival)
            <span class="badge badge--new">New In</span>
        @endif
        <img src="{{ $product->photo }}" class="product-card__image" alt="{{ $product->name }}" loading="lazy">
        <div class="card-chip">
            <span class="card-chip__name">{{ $product->name }}</span>
            <span class="card-chip__value">£{{ number_format($product->effective_price, 0) }}</span>
        </div>
    </div>
    <div class="product-card__details mt-3 flex items-start justify-between gap-3">
        <div>
            <p class="mono-label">{{ $product->name }}</p>
            <div class="mt-1 flex items-center gap-2">
                @foreach(array_slice($product->color_options, 0, 4) as $opt)
                    <span class="swatch-dot" style="background:{{ $opt['hex'] }}" title="{{ $opt['name'] }}"></span>
                @endforeach
                <span class="text-xs text-ink/55">{{ count($product->color_options) > 1 ? count($product->color_options).' colours' : $product->color }}</span>
            </div>
            @if(!$product->isSoldOut() && $product->stock <= 3)
                <p class="mt-1 text-[10px] uppercase tracking-wide text-signal-orange">Only {{ $product->stock }} left</p>
            @endif
        </div>
        <p class="whitespace-nowrap text-xs">
            @if($product->isOnSale())
                <span class="text-ink/40 line-through">£{{ number_format($product->price, 2) }}</span>
                <span class="ml-1 font-semibold text-signal-orange">£{{ number_format($product->effective_price, 2) }}</span>
            @else
                £{{ number_format($product->price, 2) }}
            @endif
        </p>
    </div>
</a>
