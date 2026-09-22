<x-layouts.store
    :title="$product->name.' — Buy Online | London Threads & Tassels'"
    :metaDescription="\Illuminate\Support\Str::limit(strip_tags($product->description), 155)"
    :metaKeywords="$product->name.', Indian occasion wear, hand embroidered suit, '.$product->color.', buy Indian suit online UK'"
    :metaImage="$product->photo">

    <x-slot:head>
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => strip_tags($product->description),
            'image' => $product->galleryUrls,
            'sku' => (string) $product->id,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'GBP',
                'price' => (string) $product->effective_price,
                'availability' => $product->isSoldOut() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock',
                'url' => url()->current(),
            ],
        ], JSON_UNESCAPED_SLASHES) !!}
        </script>
    </x-slot:head>

    <section class="grid lg:grid-cols-2">
        <div>
            <div class="product-gallery" data-product-gallery data-gallery-images="{{ json_encode($product->gallery_urls) }}">
                <div class="product-gallery__frame relative min-h-[420px] overflow-hidden bg-soft-linen lg:min-h-[620px]" data-motion="image" data-gallery-frame>
                    @if($product->isSoldOut())
                        <span class="badge badge--sold absolute left-5 top-5 z-10">Sold Out</span>
                    @elseif($product->isOnSale())
                        <span class="badge badge--sale absolute left-5 top-5 z-10">Sale &minus;{{ $product->discount_percent }}%</span>
                    @elseif($product->is_new_arrival)
                        <span class="badge badge--new absolute left-5 top-5 z-10">New In</span>
                    @endif

                    <img src="{{ $product->photo }}" id="product-main-image" class="product-hero-image h-full w-full object-cover" alt="{{ $product->name }}">

                    @if(count($product->gallery_urls) > 1)
                        <div class="gallery-nav">
                            <button type="button" class="gallery-arrow" data-gallery-prev aria-label="Previous image">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button type="button" class="gallery-arrow" data-gallery-next aria-label="Next image">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <span class="gallery-counter" data-gallery-counter>1 / {{ count($product->gallery_urls) }}</span>
                    @endif

                    <button type="button" class="gallery-zoom-btn" data-gallery-zoom aria-label="Zoom image">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3M11 8v6M8 11h6" stroke-linecap="round"/></svg>
                    </button>
                </div>

                @if(count($product->gallery_urls) > 1)
                    <div class="mt-3 flex gap-3 px-1" data-thumb-strip>
                        @foreach($product->gallery_urls as $i => $url)
                            <button type="button" class="product-thumb {{ $i === 0 ? 'is-active' : '' }}" data-thumb="{{ $url }}" data-thumb-index="{{ $i }}">
                                <img src="{{ $url }}" alt="{{ $product->name }} view {{ $i + 1 }}" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="p-7 md:p-14">
            <a class="nav-link text-signal-orange" href="{{ route('store.index') }}#collection">&larr; Back to the collection</a>

            @php($colorOptions = $product->color_options)
            <div class="mt-14" data-motion>
                @if(count($colorOptions))
                    <p class="mono-label">Colour: <span id="selected-color-name">{{ $colorOptions[0]['name'] }}</span></p>
                    <div class="mt-3 flex flex-wrap items-center gap-2" data-color-picker>
                        @foreach($colorOptions as $i => $opt)
                            <button type="button"
                                class="color-swatch {{ $i === 0 ? 'is-active' : '' }}"
                                style="--sw:{{ $opt['hex'] }}"
                                data-color-name="{{ $opt['name'] }}"
                                aria-label="{{ $opt['name'] }}"
                                title="{{ $opt['name'] }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
            <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>{{ $product->name }}</h1>
            <p class="mt-4 text-lg" data-motion>
                @if($product->isOnSale())
                    <span class="text-ink/40 line-through">£{{ number_format($product->price, 2) }}</span>
                    <span class="ml-2 font-semibold text-signal-orange">£{{ number_format($product->effective_price, 2) }}</span>
                @else
                    £{{ number_format($product->price, 2) }}
                @endif
            </p>
            <p class="mt-8 max-w-md text-sm leading-7 text-ink/70" data-motion>{{ $product->description }}</p>

            @if($product->isSoldOut())
                <div class="mt-10 border border-ink/15 p-6" data-motion>
                    <p class="mono-label">Currently sold out</p>
                    <p class="mt-2 max-w-sm text-xs leading-6 text-ink/60">This style has sold through its hand-embroidered run. New batches are made in small numbers — check back soon or explore the rest of the collection.</p>
                </div>
            @else
                <form class="mt-10" method="POST" action="{{ route('cart.store', $product) }}" data-motion>
                    @csrf
                    <input type="hidden" name="color" id="selected-color-input" value="{{ count($product->color_options) ? $product->color_options[0]['name'] : $product->color }}">
                    <label class="mono-label">Select your size</label>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($product->sizes as $size)
                            <label>
                                <input class="peer sr-only" type="radio" name="size" value="{{ $size }}" required>
                                <span class="size-option">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>

                    <label class="mono-label mt-7 block">Quantity</label>
                    <input class="field mt-2 w-24" type="number" name="quantity" min="1" max="{{ min(10, $product->stock) }}" value="1">

                    <button class="button-dark mt-7 w-full">Add to bag — £{{ number_format($product->effective_price, 2) }}</button>
                </form>
            @endif

            <p class="mt-5 text-[10px] uppercase tracking-wider text-ink/50" data-motion>
                @unless($product->isSoldOut())
                    Only {{ $product->stock }} remaining &middot;
                @endunless
                Complimentary UK delivery
            </p>
        </div>
    </section>

    <div id="image-lightbox" class="image-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Zoomed product image">
        <button type="button" class="image-lightbox__close" data-lightbox-close aria-label="Close zoom">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>
        </button>
        @if(count($product->gallery_urls) > 1)
            <button type="button" class="image-lightbox__arrow image-lightbox__arrow--prev" data-lightbox-prev aria-label="Previous image">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button type="button" class="image-lightbox__arrow image-lightbox__arrow--next" data-lightbox-next aria-label="Next image">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        @endif
        <div class="image-lightbox__stage" data-lightbox-stage>
            <img id="lightbox-image" src="" alt="{{ $product->name }}">
        </div>
        <p class="image-lightbox__hint">Click the image to zoom &middot; click again to zoom out</p>
    </div>

    @if($related->isNotEmpty())
        <section class="px-5 py-16 md:px-10">
            <p class="mono-label text-signal-orange" data-motion>You may also like</p>
            <h2 class="display mt-3 text-2xl md:text-4xl" data-motion>MORE FROM THE EDIT</h2>
            <div class="motion-stagger mt-9 grid gap-x-4 gap-y-9 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.store>
