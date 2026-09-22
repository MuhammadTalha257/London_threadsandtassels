@props(['transparent' => false])
@php
    $seoTitle = $title ?? 'London Threads & Tassels — Hand-Embroidered Indian Occasion Wear, London UK';
    $seoDescription = $metaDescription ?? 'Hand-embroidered Indian occasion suits, Anarkali sets, bandhani kurtas and festive wear — designed by Akansha and tailored in small batches in London, UK. Free UK delivery.';
    $seoKeywords = $metaKeywords ?? 'Indian occasion wear, hand embroidered suits, Anarkali suit, salwar kameez, bandhani kurta, sharara set, Indian wedding guest outfit, festive wear London, occasion wear UK, Indian clothing store London';
    $seoImage = $metaImage ?? asset('images/hero/hero-1.jpg');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111110">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ $seoTitle }}</title>

    {{-- Open Graph / social sharing --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="London Threads & Tassels">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    {{-- Structured data: helps Google understand this is a UK clothing retailer --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ClothingStore',
        'name' => 'London Threads & Tassels',
        'description' => 'Hand-embroidered Indian occasion suits and festive wear, designed and tailored in small batches in London.',
        'url' => url('/'),
        'image' => $seoImage,
        'priceRange' => '££',
        'areaServed' => 'GB',
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'London', 'addressCountry' => 'GB'],
        'sameAs' => [],
    ], JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'London Threads & Tassels',
        'url' => url('/'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url('/shop').'?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ], JSON_UNESCAPED_SLASHES) !!}
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="{{ $transparent ? 'has-transparent-hero' : '' }}">

    @php($promotion = \App\Models\Promotion::current())
    @php($navCategories = \App\Models\Category::active()->get())

    <div id="page-loader" aria-hidden="true">
        <div class="page-loader__top">
            <div class="page-loader__mark">
                <span class="page-loader__mark-eyebrow">LONDON</span>
                <span class="page-loader__mark-name">Threads &amp; Tassels</span>
            </div>
            <img src="{{ asset('images/london-threads-tassels-logo.jpg') }}" alt="" class="h-10 w-10 rounded-full object-cover opacity-80 md:h-12 md:w-12">
        </div>
        <div class="page-loader__center">
            <div class="page-loader__ring">
                <img src="{{ asset('images/london-threads-tassels-logo.jpg') }}" alt="London Threads & Tassels" class="page-loader__logo">
                <span class="page-loader__label">Loading</span>
            </div>
        </div>
        <div class="page-loader__bottom">
            <span class="page-loader__count" id="loader-count">0%</span>
            <span class="page-loader__track"><span class="page-loader__fill" id="loader-fill"></span></span>
        </div>
    </div>

    @if($promotion)
        <div id="offer-popup" class="offer-popup" role="dialog" aria-modal="true" aria-labelledby="offer-popup-title">
            <div class="offer-banner">
                <div class="offer-banner__media">
                    @if($promotion->photo)
                        <img src="{{ $promotion->photo }}" alt="{{ $promotion->title }}">
                    @else
                        <div class="offer-banner__media-fallback" aria-hidden="true"></div>
                    @endif
                    <div class="offer-banner__veil" aria-hidden="true"></div>
                    <div class="offer-banner__veil--mobile" aria-hidden="true"></div>
                </div>
                <button type="button" id="offer-popup-close" class="offer-banner__close" aria-label="Close offer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>
                </button>
                <div class="offer-banner__body">
                    <p class="offer-banner__eyebrow">{{ $promotion->title }}</p>
                    <h2 id="offer-popup-title" class="offer-banner__title">An offer, <em>just landed</em></h2>
                    <p class="offer-banner__message">{{ $promotion->message }}</p>
                    @if($promotion->cta_label)
                        <a href="{{ $promotion->cta_url ?: '#collection' }}" id="offer-popup-cta" class="offer-banner__cta">{{ $promotion->cta_label }}</a>
                    @endif
                    <span class="offer-banner__fine">No code needed &middot; Applied automatically at checkout</span>
                </div>
            </div>
        </div>
    @endif

    <div id="route-veil" aria-hidden="true"><span id="route-veil__mark">LONDON THREADS &amp; TASSELS</span></div>

    <div id="cursor-view" aria-hidden="true">View</div>

    <a href="https://wa.me/442012345678" target="_blank" rel="noopener" class="whatsapp-bubble" aria-label="Chat with us on WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2Zm0 18.2a8.1 8.1 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.2-.3.2-.5.1-.2-.1-1-.4-2-1.2-.7-.6-1.2-1.4-1.4-1.6-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 2 3 4.7 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
    </a>

    <div id="site-topbar" class="site-topbar">
        <div class="bg-ink px-4 py-2 text-center text-[9px] uppercase tracking-[.17em] text-white/80">
            Complimentary UK delivery on every order &middot; Handcrafted in small batches
        </div>

        <header id="site-header" class="site-header {{ $transparent ? 'site-header--transparent' : 'site-header--solid' }}">
            <div class="site-header__inner">
                <div class="flex items-center">
                    <button type="button" id="menu-toggle" class="site-header__burger lg:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="Open menu">
                        <span></span><span></span><span></span>
                    </button>
                    <nav class="nav-links dock-nav hidden items-center gap-7 lg:flex" data-dock>
                        <a class="nav-link" href="{{ route('store.index') }}">Home</a>
                        <div class="nav-item-dropdown">
                            <a class="nav-link" href="{{ route('shop') }}">Shop</a>
                            @if($navCategories->isNotEmpty())
                                <div class="nav-dropdown-panel">
                                    <a class="nav-dropdown-panel__link" href="{{ route('shop') }}">All suits</a>
                                    @foreach($navCategories as $navCategory)
                                        <a class="nav-dropdown-panel__link" href="{{ route('shop') }}?category={{ $navCategory->slug }}">{{ $navCategory->name }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <a class="nav-link" href="{{ route('store.index') }}#new-arrivals">New In</a>
                        <a class="nav-link" href="{{ route('store.index') }}#story">About Us</a>
                        <a class="nav-link" href="{{ route('help') }}">Contact Us</a>
                    </nav>
                </div>

                <a href="{{ route('store.index') }}" class="site-header__logo" aria-label="London Threads & Tassels — home">
                    <span class="site-header__eyebrow">LONDON</span>
                    <span class="site-header__name">Threads &amp; Tassels</span>
                    <span class="site-header__rule"></span>
                </a>

                <div class="flex items-center justify-end gap-4 md:gap-5">
                    @auth
                        @if(auth()->user()->is_admin)
                            <a class="nav-link hidden lg:inline" href="{{ route('admin.index') }}">Admin</a>
                        @endif
                    @endauth
                    <a class="header-icon-link" href="{{ auth()->check() ? route('account') : route('login') }}" aria-label="{{ auth()->check() ? 'My account' : 'Sign in' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="3.5"/><path d="M4.5 20c1.5-4 5-5.5 7.5-5.5s6 1.5 7.5 5.5" stroke-linecap="round"/></svg>
                    </a>
                    <a class="header-icon-link" href="{{ route('cart.index') }}" aria-label="View bag">
                        <span class="relative inline-flex">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 8h12l-1 12.5a1.5 1.5 0 0 1-1.5 1.5h-8a1.5 1.5 0 0 1-1.5-1.5L6 8Z" stroke-linejoin="round"/><path d="M9 8V6a3 3 0 0 1 6 0v2" stroke-linecap="round"/></svg>
                            @if(collect(session('cart', []))->sum('quantity') > 0)
                                <span class="header-icon-link__badge">{{ collect(session('cart', []))->sum('quantity') }}</span>
                            @endif
                        </span>
                    </a>
                </div>
            </div>
        </header>
    </div>

    <div id="mobile-menu" class="mobile-menu" aria-hidden="true">
        <nav class="mobile-menu__nav nav-links">
            <a class="mobile-menu__link" href="{{ route('store.index') }}">Home</a>
            @if($navCategories->isNotEmpty())
                <details class="mobile-menu__dropdown">
                    <summary class="mobile-menu__link mobile-menu__dropdown-summary">
                        <a href="{{ route('shop') }}" onclick="event.stopPropagation();">Shop</a>
                        <span class="mobile-menu__dropdown-caret" aria-hidden="true">&darr;</span>
                    </summary>
                    <div class="mobile-menu__dropdown-panel">
                        <a class="mobile-menu__dropdown-link" href="{{ route('shop') }}">All suits</a>
                        @foreach($navCategories as $navCategory)
                            <a class="mobile-menu__dropdown-link" href="{{ route('shop') }}?category={{ $navCategory->slug }}">{{ $navCategory->name }}</a>
                        @endforeach
                    </div>
                </details>
            @else
                <a class="mobile-menu__link" href="{{ route('shop') }}">Shop</a>
            @endif
            <a class="mobile-menu__link" href="{{ route('store.index') }}#new-arrivals">New In</a>
            <a class="mobile-menu__link" href="{{ route('store.index') }}#story">About Us</a>
            <a class="mobile-menu__link" href="{{ route('help') }}">Contact Us</a>
            <a class="mobile-menu__link" href="{{ auth()->check() ? route('account') : route('login') }}">{{ auth()->check() ? 'My account' : 'Sign in' }}</a>
            <a class="mobile-menu__link" href="{{ route('cart.index') }}">Bag ({{ collect(session('cart', []))->sum('quantity') }})</a>
            @auth
                @if(auth()->user()->is_admin)
                    <a class="mobile-menu__link" href="{{ route('admin.index') }}">Admin</a>
                @endif
            @endauth
            <div class="mobile-menu__social">
                <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1"/></svg></a>
                <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M16 3c.3 2 1.6 3.6 3.5 3.9v2.4c-1.3 0-2.5-.4-3.5-1.1v5.9a5.4 5.4 0 1 1-5.4-5.4c.3 0 .5 0 .8.1v2.5a2.9 2.9 0 1 0 2.1 2.8V3H16Z"/></svg></a>
                <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M21.6 7.2s-.2-1.4-.8-2c-.7-.8-1.6-.8-2-.9C16 4 12 4 12 4s-4 0-6.8.3c-.4.1-1.3.1-2 .9-.6.6-.8 2-.8 2S2.2 8.8 2.2 10.4v1.2C2.2 13.2 2.4 14.8 2.4 14.8s.2 1.4.8 2c.7.8 1.7.8 2.1.9C6.9 20 12 20 12 20s4 0 6.8-.3c.4-.1 1.3-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.2c0-1.6-.2-3.2-.2-3.2ZM10 14.5v-5l4.2 2.5-4.2 2.5Z"/></svg></a>
                <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><path d="M14 8.5h2V5.8c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H6.8v3h2.5V21h3v-6.7h2.4l.4-3h-2.8V10c0-.9.2-1.5 1.7-1.5Z" stroke-linejoin="round"/></svg></a>
                <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="Pinterest"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><circle cx="12" cy="12" r="9"/><path d="M9.5 17c1-3 1.3-4.7 1.3-4.7m0 0c-.3-.6-.5-1.9.4-2.8.9-.9 2.4-.5 2.7.6.3 1.1-.6 2.6-.9 3.4-.3.8.2 1.6 1.1 1.6 1.6 0 2.7-2 2.7-3.8 0-1.7-1.3-3.4-3.7-3.4-2.8 0-4.4 2-4.4 4 0 .8.3 1.6.6 2"/></svg></a>
                <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="X (Twitter)"><svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M13.9 10.6 21 3h-2l-6.2 6.6L7.8 3H3l7.4 10.2L3 21h2l6.6-7 5.4 7H22l-7.6-10.4Zm-2.3 2.5-.8-1L5 4.6h2.3l4.9 6.7.8 1 6.4 8.8h-2.3l-5.5-7Z"/></svg></a>
            </div>
        </nav>
    </div>

    @if(session('success'))
        <div class="bg-soft-linen px-5 py-3 text-center text-xs text-ink">{{ session('success') }}</div>
    @endif

    <main class="site-main {{ $transparent ? '' : 'site-main--padded' }}">{{ $slot }}</main>

    <footer data-motion class="site-footer">
        <div class="mx-auto grid max-w-[1440px] gap-10 px-5 md:grid-cols-3 md:px-10">
            <div>
                <p class="display text-xl">LONDON THREADS<br>&amp; TASSELS</p>
                <p class="mt-4 max-w-xs text-xs leading-6 text-graphite-warm">Celebrating the craft of occasion dressing, one considered suit at a time — designed by Akansha.</p>
                <div class="mt-6 flex gap-3">
                    <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M16 3c.3 2 1.6 3.6 3.5 3.9v2.4c-1.3 0-2.5-.4-3.5-1.1v5.9a5.4 5.4 0 1 1-5.4-5.4c.3 0 .5 0 .8.1v2.5a2.9 2.9 0 1 0 2.1 2.8V3H16Z"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M21.6 7.2s-.2-1.4-.8-2c-.7-.8-1.6-.8-2-.9C16 4 12 4 12 4s-4 0-6.8.3c-.4.1-1.3.1-2 .9-.6.6-.8 2-.8 2S2.2 8.8 2.2 10.4v1.2C2.2 13.2 2.4 14.8 2.4 14.8s.2 1.4.8 2c.7.8 1.7.8 2.1.9C6.9 20 12 20 12 20s4 0 6.8-.3c.4-.1 1.3-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.2c0-1.6-.2-3.2-.2-3.2ZM10 14.5v-5l4.2 2.5-4.2 2.5Z"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="Pinterest">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><circle cx="12" cy="12" r="9"/><path d="M9.5 17c1-3 1.3-4.7 1.3-4.7m0 0c-.3-.6-.5-1.9.4-2.8.9-.9 2.4-.5 2.7.6.3 1.1-.6 2.6-.9 3.4-.3.8.2 1.6 1.1 1.6 1.6 0 2.7-2 2.7-3.8 0-1.7-1.3-3.4-3.7-3.4-2.8 0-4.4 2-4.4 4 0 .8.3 1.6.6 2"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><path d="M14 8.5h2V5.8c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H6.8v3h2.5V21h3v-6.7h2.4l.4-3h-2.8V10c0-.9.2-1.5 1.7-1.5Z" stroke-linejoin="round"/></svg></a>
                    <a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="X (Twitter)"><svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M13.9 10.6 21 3h-2l-6.2 6.6L7.8 3H3l7.4 10.2L3 21h2l6.6-7 5.4 7H22l-7.6-10.4Zm-2.3 2.5-.8-1L5 4.6h2.3l4.9 6.7.8 1 6.4 8.8h-2.3l-5.5-7Z"/></svg></a>
                </div>
            </div>
            <div class="footer-col">
                <a class="footer-link" href="{{ route('store.index') }}">Home</a>
                <a class="footer-link" href="{{ route('shop') }}">Shop</a>
                <a class="footer-link" href="{{ route('store.index') }}#new-arrivals">New In</a>
                <a class="footer-link" href="{{ route('store.index') }}#story">About Us</a>
            </div>
            <div class="footer-col">
                <a class="footer-link" href="{{ route('help') }}">Contact Us</a>
                <a class="footer-link" href="{{ route('help') }}#shipping">Shipping &amp; returns</a>
                <a class="footer-link" href="{{ route('help') }}#sizing">Size guide</a>
            </div>
        </div>
        <p class="mx-auto mt-12 max-w-[1440px] border-t border-pale-stone px-5 pt-6 text-[10px] uppercase tracking-[.14em] text-ash-gray md:px-10">&copy; {{ now()->year }} London Threads &amp; Tassels. All rights reserved.</p>
    </footer>
</body>
</html>
