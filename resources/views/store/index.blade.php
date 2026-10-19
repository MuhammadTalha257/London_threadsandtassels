<x-layouts.store :transparent="true">

    {{-- ============ OFFER SLIDER + LANDING COLLAGE ============
         First screen: a full-screen slider of the admin's offer banners
         (Admin → Header images). Scrolling shrinks the current slide down
         into the middle of a collage of the collection, which rises up
         around it. --}}
    @if($heroScenes->isNotEmpty())
        <section id="hero" class="mela" data-mela aria-roledescription="carousel" aria-label="Offers and featured looks">
            <div class="mela__sticky">
                <div class="mela__bg" aria-hidden="true"></div>

                {{-- the collage the slide lands in (Revolut-style) --}}
                <div class="mela__collage" data-mela-collage>
                    @foreach($galleryScenes as $scene)
                        <a href="{{ $scene['link'] ?? route('shop') }}" class="mela__tile" data-mela-tile tabindex="-1" aria-label="{{ $scene['headline'] ?: 'Shop this look' }}">
                            <img src="{{ $scene['image'] }}" alt="" loading="lazy">
                        </a>
                    @endforeach
                </div>

                <div class="mela__lander" data-mela-lander aria-hidden="true">
                    <img src="{{ $heroScenes->first()['image'] }}" alt="" data-mela-lander-img>
                </div>

                <div class="mela__headline" data-mela-headline>
                    <h2 class="mela__headline-title">The festive edit is open</h2>
                    <div class="mela__headline-actions">
                        <a href="{{ route('shop') }}" class="button-zari">Shop the collection</a>
                        <a href="#collection" class="button-ghost-dark">See the suits</a>
                    </div>
                </div>

                {{-- the offer slider --}}
                <div class="mela__slider" data-mela-slider>
                    @foreach($heroScenes as $i => $scene)
                        @php($offer = trim((string) ($scene['discount'] ?? '')))
                        @php(preg_match('/(\d+\s?%)/', $offer, $offerMatch))
                        @php($offerBig = $offerMatch[1] ?? null)
                        @php($offerRest = $offerBig ? trim(str_replace($offerBig, '', $offer)) : null)
                        <div class="mela__slide {{ $i === 0 ? 'is-active' : '' }}" data-mela-slide data-image="{{ $scene['image'] }}" role="group" aria-roledescription="slide" aria-label="{{ $i + 1 }} of {{ $heroScenes->count() }}" @if($i !== 0) aria-hidden="true" @endif>
                            <img class="mela__slide-img" src="{{ $scene['image'] }}" alt="{{ $scene['headline'] ?: 'Occasion suit from London Threads & Tassels' }}" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                            <div class="mela__slide-veil" aria-hidden="true"></div>
                            <div class="mela__caption">
                                @if($offer !== '')
                                    <span class="mela__offer-pill">{{ $offer }}</span>
                                @endif
                                @if(filled($scene['kicker'] ?? null))
                                    <p class="mela__kicker">{{ $scene['kicker'] }}</p>
                                @endif
                                @if($i === 0)
                                    <h1 class="mela__title">{{ $scene['headline'] ?: 'Dressed for every celebration' }}</h1>
                                @else
                                    <p class="mela__title">{{ $scene['headline'] ?: 'Dressed for every celebration' }}</p>
                                @endif
                                @if(filled($scene['subhead']))
                                    <p class="mela__sub">{{ $scene['subhead'] }}</p>
                                @endif
                                <div class="mela__caption-actions">
                                    <a href="{{ $scene['cta_url'] }}" class="button-zari" @if($i !== 0) tabindex="-1" @endif>{{ $scene['cta_label'] }}</a>
                                    @if($scene['product'])
                                        <span class="mela__price">{{ $scene['product']->name }} &mdash; &pound;{{ number_format((float) $scene['product']->effective_price, 0) }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($offer !== '')
                                <div class="mela__seal" aria-label="Offer: {{ $offer }}">
                                    <svg class="mela__seal-ring" viewBox="0 0 200 200" aria-hidden="true">
                                        <defs><path id="seal-path-{{ $i }}" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs>
                                        <text><textPath href="#seal-path-{{ $i }}">festive offer &#10022; limited time &#10022; festive offer &#10022; limited time &#10022;</textPath></text>
                                    </svg>
                                    <span class="mela__seal-face">
                                        @if($offerBig)
                                            <b>{{ $offerBig }}</b>
                                            @if($offerRest !== '')<small>{{ $offerRest }}</small>@endif
                                        @else
                                            <b class="is-text">{{ $offer }}</b>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if($heroScenes->count() > 1)
                        <div class="mela__controls">
                            <div class="mela__tabs" role="tablist" aria-label="Choose offer">
                                @foreach($heroScenes as $i => $scene)
                                    <button type="button" class="mela__tab {{ $i === 0 ? 'is-active' : '' }}" data-mela-dot role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Show slide {{ $i + 1 }}">
                                        <span class="mela__tab-label">{{ \Illuminate\Support\Str::limit($scene['headline'] ?: 'Slide '.($i + 1), 26) }}@if(filled($scene['discount'] ?? null)) <em>{{ $scene['discount'] }}</em>@endif</span>
                                        <i></i>
                                    </button>
                                @endforeach
                            </div>
                            <div class="mela__arrows">
                                <button type="button" class="mela__arrow" data-mela-prev aria-label="Previous slide">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                                </button>
                                <button type="button" class="mela__arrow" data-mela-next aria-label="Next slide">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mela__progress" aria-hidden="true"><i></i></div>
            </div>
        </section>
    @endif

    {{-- ============ STORIES: the articles that rise as the feature scrolls away ============ --}}
    <div id="reveal-cover" class="reveal-cover bg-warm-parchment">

    <section id="stories" class="section-block">
        <div class="section-shell">
            <div class="section-head" data-motion>
                <div>
                    <p class="section-kicker section-kicker--accent">The Journal</p>
                    <h2 class="section-title">Stories from<br>the atelier</h2>
                </div>
                <a href="{{ route('shop') }}" class="button-light">Shop the collection</a>
            </div>

            <div class="article-grid">
                @php($stories = [
                    ['n' => '01', 'title' => 'Thread by thread, tassel by tassel', 'meta' => 'Craft — 6 min read', 'img' => asset('storage/products/ivory-orange-front.jpg')],
                    ['n' => '02', 'title' => 'How to wear occasion colour', 'meta' => 'Styling — 4 min read', 'img' => asset('storage/products/emerald-bandhani-front.jpg')],
                    ['n' => '03', 'title' => 'Inside a small-batch run', 'meta' => 'Atelier — 8 min read', 'img' => asset('storage/products/aqua-floral-front.jpg')],
                ])
                @foreach($stories as $story)
                    <a href="{{ route('shop') }}" class="article-card" data-motion data-cursor-view>
                        <div class="article-card__media glow" data-glow>
                            <span class="article-card__num">{{ $story['n'] }}</span>
                            <img src="{{ $story['img'] }}" alt="{{ $story['title'] }}" loading="lazy">
                            <div class="card-chip">
                                <span class="card-chip__name">{{ $story['title'] }}</span>
                                <span class="card-chip__value">Read</span>
                            </div>
                        </div>
                        <h3 class="article-card__title">{{ $story['title'] }}</h3>
                        <p class="article-card__meta">{{ $story['meta'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($trendingStyles->isNotEmpty())
        <section id="trending" class="section-block">
            <div class="section-shell">
                <div class="section-head" data-motion>
                    <div>
                        <p class="section-kicker section-kicker--accent">Right now</p>
                        <h2 class="section-title">Trending styles</h2>
                    </div>
                    <div class="trend-nav">
                        <button type="button" class="hero-ctrl" data-trend-prev aria-label="Previous style">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4 text-ink"><path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" class="hero-ctrl" data-trend-next aria-label="Next style">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4 text-ink"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>

                <div class="trend-rail" data-trend-rail data-motion>
                    @foreach($trendingStyles as $style)
                        @php($product = $style['product'])
                        @if($product)
                            <a href="{{ route('products.show', $product) }}" class="trend-card" data-cursor-view>
                                <div class="trend-card__media">
                                    <span class="trend-card__tag">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3"><path d="M13 2 3 14h6l-2 8 10-12h-6l2-8Z"/></svg>
                                        Trending
                                    </span>
                                    <img src="{{ $product->photo }}" alt="{{ $product->name }}" loading="lazy">
                                </div>
                                <div class="trend-card__foot">
                                    <div>
                                        <p class="trend-card__cat">{{ $style['label'] }}</p>
                                        <p class="trend-card__label">{{ $product->name }}</p>
                                    </div>
                                    <p class="trend-card__price">&pound;{{ number_format((float) $product->effective_price, 0) }}</p>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($saleProducts->isNotEmpty())
        <section id="sale" class="section-block">
            <div class="section-shell">
                <div class="section-head" data-motion>
                    <div>
                        <p class="section-kicker section-kicker--accent">Limited time</p>
                        <h2 class="section-title">On sale now</h2>
                    </div>
                    <span class="shop-count">{{ $saleProducts->count() }} styles reduced</span>
                </div>
                <div class="shop-grid">
                    @foreach($saleProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($newArrivals->isNotEmpty())
        <section id="new-arrivals" class="section-block">
            <div class="section-shell">
                <div class="section-head" data-motion>
                    <div>
                        <p class="section-kicker">Just landed</p>
                        <h2 class="section-title">New in</h2>
                    </div>
                    <span class="shop-count">{{ $newArrivals->count() }} new styles</span>
                </div>
                <div class="shop-grid">
                    @foreach($newArrivals as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="collection" class="section-block">
        <div class="section-shell">
            <div class="section-head" data-motion>
                <div>
                    <p class="section-kicker">The collection</p>
                    <h2 class="section-title">The suit edit</h2>
                </div>
                <a href="{{ route('shop') }}" class="button-light">View all &amp; filter</a>
            </div>
            <div class="shop-grid">
                @forelse($products->take(8) as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="t-body">The new collection is being prepared.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section id="story" class="section-block bg-soft-linen">
        <div class="section-shell grid items-center gap-12 lg:grid-cols-2">
            <div data-motion>
                <p class="section-kicker section-kicker--accent">The Atelier</p>
                <h2 class="section-title">Made to hold<br>a room</h2>
                <p class="t-body mt-7 max-w-[52ch] text-graphite-warm">We believe occasion wear should feel as individual as the woman who chooses it. Our suits pair considered tailoring with the familiar richness of South Asian craft — hand-embroidered, thread by thread, tassel by tassel.</p>
                <div class="mt-10 grid grid-cols-2 gap-8 border-t border-pale-stone pt-8 sm:grid-cols-4">
                    <div data-motion><p class="dash-stat__value">100%</p><p class="dash-stat__label">Hand-embroidered</p></div>
                    <div data-motion><p class="dash-stat__value">UK</p><p class="dash-stat__label">Free delivery</p></div>
                    <div data-motion><p class="dash-stat__value">14d</p><p class="dash-stat__label">Easy returns</p></div>
                    <div data-motion><p class="dash-stat__value">1:1</p><p class="dash-stat__label">Small batch</p></div>
                </div>
            </div>
            <div class="aspect-[4/5] overflow-hidden rounded-[18px] bg-cover bg-center" style="background-image:url('{{ asset('storage/products/gallery/ivory-orange-side.jpg') }}')" data-motion="image"></div>
        </div>
    </section>

    @if($campaign)
        <section id="campaign" class="campaign">
            <div class="aurora"></div>

            <div class="campaign__meta">
                <div>
                    <p class="femme text-white/50">NEW THIS SEASON</p>
                    <p class="mt-2 max-w-[16rem] text-xs leading-6 text-white/60">{{ $campaign->subtitle }}</p>
                </div>
                <div class="campaign__meta-links nav-links text-[11px] text-white/55">
                    <a class="transition hover:text-signal-orange" href="#" target="_blank" rel="noopener">Instagram</a>
                    <a class="transition hover:text-signal-orange" href="#" target="_blank" rel="noopener">Facebook</a>
                    <a class="transition hover:text-signal-orange" href="#" target="_blank" rel="noopener">TikTok</a>
                    <a class="transition hover:text-signal-orange" href="#" target="_blank" rel="noopener">YouTube</a>
                    <a class="transition hover:text-signal-orange" href="#" target="_blank" rel="noopener">Pinterest</a>
                </div>
            </div>

            <div class="campaign__stage">
                <p class="campaign__bigtext" data-campaign-text>{{ $campaign->title }}</p>
                @if($campaign->photo)
                    <div class="campaign__figure" data-parallax-card>
                        <img src="{{ $campaign->photo }}" alt="{{ $campaign->title }}">
                    </div>
                @endif
            </div>

            <div class="campaign__foot">
                <div class="nav-links text-[11px] leading-6 text-white/55">
                    @if($campaign->body)
                        <p class="max-w-xs">{{ $campaign->body }}</p>
                    @endif
                </div>
                @if($campaign->product)
                    <a href="{{ route('products.show', $campaign->product) }}" class="button-light">{{ $campaign->cta_label ?: 'Shop the piece' }}</a>
                @elseif($campaign->cta_url)
                    <a href="{{ $campaign->cta_url }}" class="button-light">{{ $campaign->cta_label ?: 'Discover' }}</a>
                @endif
                <p class="femme text-right text-white/40">SEASON {{ now()->format('Y') }}</p>
            </div>
        </section>
    @endif

    <section id="social" class="px-5 py-16 text-center md:px-10">
        <p class="mono-label text-signal-orange" data-motion>Join us</p>
        <h2 class="display mt-3 text-2xl md:text-4xl" data-motion>FOLLOW THE ATELIER</h2>
        <p class="mx-auto mt-4 max-w-md text-sm leading-7 text-ink/60" data-motion>Behind the scenes of every hand-embroidered piece, new arrivals and styling notes — come and say hello.</p>
        <div class="mt-7 flex justify-center gap-4" data-motion>
            <a href="#" target="_blank" rel="noopener" class="social-cta" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1"/></svg></a>
            <a href="#" target="_blank" rel="noopener" class="social-cta" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M16 3c.3 2 1.6 3.6 3.5 3.9v2.4c-1.3 0-2.5-.4-3.5-1.1v5.9a5.4 5.4 0 1 1-5.4-5.4c.3 0 .5 0 .8.1v2.5a2.9 2.9 0 1 0 2.1 2.8V3H16Z"/></svg></a>
            <a href="#" target="_blank" rel="noopener" class="social-cta" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M21.6 7.2s-.2-1.4-.8-2c-.7-.8-1.6-.8-2-.9C16 4 12 4 12 4s-4 0-6.8.3c-.4.1-1.3.1-2 .9-.6.6-.8 2-.8 2S2.2 8.8 2.2 10.4v1.2C2.2 13.2 2.4 14.8 2.4 14.8s.2 1.4.8 2c.7.8 1.7.8 2.1.9C6.9 20 12 20 12 20s4 0 6.8-.3c.4-.1 1.3-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.2c0-1.6-.2-3.2-.2-3.2ZM10 14.5v-5l4.2 2.5-4.2 2.5Z"/></svg></a>
            <a href="#" target="_blank" rel="noopener" class="social-cta" aria-label="Pinterest"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><circle cx="12" cy="12" r="9"/><path d="M9.5 17c1-3 1.3-4.7 1.3-4.7m0 0c-.3-.6-.5-1.9.4-2.8.9-.9 2.4-.5 2.7.6.3 1.1-.6 2.6-.9 3.4-.3.8.2 1.6 1.1 1.6 1.6 0 2.7-2 2.7-3.8 0-1.7-1.3-3.4-3.7-3.4-2.8 0-4.4 2-4.4 4 0 .8.3 1.6.6 2"/></svg></a>
        </div>
    </section>

    </div>

</x-layouts.store>
