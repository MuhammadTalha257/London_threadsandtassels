<x-layouts.store :transparent="true">

    {{-- ============ EDITORIAL HERO: featured article over a running slider ============ --}}
    <section id="hero" class="editorial-hero" data-hero>
        <div class="editorial-hero__bg" data-hero-bg>
            <div class="morph-stage"
                 data-morph='@json($heroSlides->pluck('image')->values())'
                 data-transition="melt"></div>
        </div>
        <div class="editorial-hero__veil"></div>

        <div class="editorial-hero__inner" data-hero-article>
            @php($lead = $heroSlides->first())
            <article class="feature-article">
                <div class="feature-article__kicker">
                    <span class="feature-article__flag">{{ $lead['eyebrow'] ?? 'The Feature' }}</span>
                </div>

                <h1 class="feature-article__title" data-hero-title>{{ $lead['headline'] ?? 'Tailored for the moment' }}</h1>

                <p class="feature-article__excerpt">{{ $lead['subhead'] ?? 'Hand-embroidered occasion suits, made in small batches in London.' }}</p>

                <div class="feature-article__actions">
                    <a href="{{ route('shop') }}" class="button-dark button-lg">Read the feature</a>
                    <a href="#stories" class="button-light button-lg">All stories</a>
                </div>

                <div class="feature-article__meta">
                    <span>Words &amp; styling — Akansha</span>
                    <span>London atelier</span>
                    <span data-hero-counter>01 / {{ str_pad((string) $heroSlides->count(), 2, '0', STR_PAD_LEFT) }}</span>
                </div>
            </article>
        </div>

        {{-- The classy article image: travels down on scroll and lands among the stories --}}
        {{-- Breakout figure: the model stands out of her frame, card UI in front --}}
        @php($feature = $campaign?->product ?? ($lead['product'] ?? null))
        @php($featureImg = $campaign?->photo ?? ($feature?->photo) ?? ($lead['image'] ?? null))
        @if(true)
            <figure class="feature-figure">
              <div class="feature-figure__inner" data-feature-figure>
                <div class="breakout__frame">
                    <figcaption class="breakout__meta">
                        <span class="breakout__plate">
                            <span class="breakout__label">{{ $feature?->name ?? 'The Feature' }}</span>
                            <span class="breakout__value">
                                @if($feature)
                                    £{{ number_format($feature->effective_price, 0) }}
                                @else
                                    {{ now()->format('M Y') }}
                                @endif
                            </span>
                        </span>
                        <a class="breakout__pill" href="{{ $feature ? route('products.show', $feature) : route('shop') }}">
                            {{ $feature ? 'Shop this suit' : 'Shop the edit' }}
                        </a>
                    </figcaption>
                </div>

                <div class="breakout__badges">
                    <div class="breakout__badge">Hand-embroidered<span>Small batch</span></div>
                    <div class="breakout__badge">Free UK delivery<span>14-day returns</span></div>
                </div>
              </div>
            </figure>
        @endif

        <div class="scroll-cue"><span>Scroll</span><span class="scroll-cue__line"></span></div>

        @if($heroSlides->count() > 1)
            <div class="hero-nav">
                <button type="button" class="hero-ctrl" data-hero-prev aria-label="Previous">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                </button>
                <button type="button" class="hero-ctrl" data-hero-next aria-label="Next">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            <div class="hero-dots">
                @foreach($heroSlides as $i => $slide)
                    <button type="button" class="hero-dot {{ $i === 0 ? 'is-active' : '' }}" data-hero-dot aria-label="Go to slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </section>

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
