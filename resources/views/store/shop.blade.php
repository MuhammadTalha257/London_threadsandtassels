<x-layouts.store
    title="Shop Indian Occasion Wear Online — Anarkali, Bandhani &amp; Embroidered Suits | London Threads &amp; Tassels"
    metaDescription="Shop hand-embroidered Indian occasion suits online: Anarkali-style jacket sets, bandhani kurtas, embroidered co-ords and festive wear. Free UK delivery, easy 14-day returns."
    metaKeywords="shop Indian occasion wear, Anarkali suit online, bandhani kurta UK, Indian salwar kameez shop, embroidered suit online, festive wear shop London">
    <section class="section-shell pt-14">
        <div class="mb-8" data-motion>
            <p class="section-kicker section-kicker--accent">The Collection</p>
            <h1 class="section-title">Shop all suits</h1>
            <p class="t-body mt-4 max-w-[52ch] text-graphite-warm">Filter and sort instantly — nothing reloads.</p>
        </div>
    </section>

    {{-- Filter bar: everything here is handled client-side --}}
    <div class="section-shell">
        <div class="shop-toolbar" data-shop-toolbar>
            <div class="shop-toolbar__row">
                <button type="button" class="filter-pill is-active" data-filter="all">All <span class="filter-pill__count">{{ $products->count() }}</span></button>
                <button type="button" class="filter-pill" data-filter="new">New In <span class="filter-pill__count">{{ $products->where('is_new_arrival', true)->count() }}</span></button>
                <button type="button" class="filter-pill" data-filter="sale">Sale <span class="filter-pill__count">{{ $products->filter->isOnSale()->count() }}</span></button>
                <button type="button" class="filter-pill" data-filter="instock">In Stock <span class="filter-pill__count">{{ $products->reject->isSoldOut()->count() }}</span></button>

                @if($colors->isNotEmpty())
                    <select class="sort-select" data-color-filter aria-label="Filter by colour">
                        <option value="">All colours</option>
                        @foreach($colors as $color)
                            <option value="{{ \Illuminate\Support\Str::slug($color) }}">{{ $color }}</option>
                        @endforeach
                    </select>
                @endif

                @if($categories->isNotEmpty())
                    <select class="sort-select" data-category-filter aria-label="Filter by category">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                @endif

                <select class="sort-select" data-sort aria-label="Sort products">
                    <option value="newest">Newest first</option>
                    <option value="price-asc">Price: low to high</option>
                    <option value="price-desc">Price: high to low</option>
                    <option value="name">Name: A–Z</option>
                </select>

                <span class="shop-count ml-auto" data-shop-count>{{ $products->count() }} styles</span>
            </div>
        </div>
    </div>

    <section class="section-shell pb-24">
        <div class="shop-grid" data-shop-grid>
            @foreach($products as $product)
                <div class="shop-item"
                     data-shop-item
                     data-new="{{ $product->is_new_arrival ? '1' : '0' }}"
                     data-sale="{{ $product->isOnSale() ? '1' : '0' }}"
                     data-instock="{{ $product->isSoldOut() ? '0' : '1' }}"
                     data-price="{{ (float) $product->effective_price }}"
                     data-name="{{ $product->name }}"
                     data-colors="{{ collect($product->color_options)->pluck('name')->map(fn ($c) => \Illuminate\Support\Str::slug($c))->implode(' ') }}"
                     data-category="{{ $product->category?->slug }}">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>

        <div class="shop-empty hidden" data-shop-empty>
            <p class="t-body text-graphite-warm">No suits match those filters.</p>
            <button type="button" class="button-light mt-5" data-shop-reset>Clear filters</button>
        </div>
    </section>
</x-layouts.store>
