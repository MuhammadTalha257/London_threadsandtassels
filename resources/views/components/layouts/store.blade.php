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
    <meta name="theme-color" content="#5c0e1c">
    <link rel="icon" type="image/png" href="{{ asset('images/brand/logo-mark.png') }}">
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
        <span class="page-loader__curtain page-loader__curtain--left"></span>
        <span class="page-loader__curtain page-loader__curtain--right"></span>
        <div class="page-loader__stage">
            <div class="page-loader__mandala">
                <svg class="page-loader__ring page-loader__ring--outer" viewBox="0 0 400 400">
                    <circle cx="200" cy="200" r="190" fill="none" stroke="currentColor" stroke-width="1" stroke-dasharray="2 9" stroke-linecap="round"/>
                    @for($petal = 0; $petal < 24; $petal++)
                        <path d="M200 22 C206 34 206 44 200 54 C194 44 194 34 200 22Z" fill="currentColor" opacity=".55" transform="rotate({{ $petal * 15 }} 200 200)"/>
                    @endfor
                </svg>
                <svg class="page-loader__ring page-loader__ring--inner" viewBox="0 0 400 400">
                    <circle cx="200" cy="200" r="150" fill="none" stroke="currentColor" stroke-width=".8" opacity=".6"/>
                    @for($dot = 0; $dot < 48; $dot++)
                        <circle cx="200" cy="58" r="{{ $dot % 2 ? 1.6 : 3 }}" fill="currentColor" transform="rotate({{ $dot * 7.5 }} 200 200)"/>
                    @endfor
                </svg>
                <svg class="page-loader__progress" viewBox="0 0 400 400">
                    <circle cx="200" cy="200" r="170" fill="none" stroke="rgba(239,208,142,.18)" stroke-width="2"/>
                    <circle id="loader-arc" cx="200" cy="200" r="170" fill="none" stroke="url(#loader-gold)" stroke-width="2.5" stroke-linecap="round" pathLength="100" stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 200 200)"/>
                    <defs><linearGradient id="loader-gold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff3cf"/><stop offset=".5" stop-color="#efd08e"/><stop offset="1" stop-color="#c68f2f"/></linearGradient></defs>
                </svg>
                <div class="page-loader__logo-wrap">
                    <img src="{{ asset('images/brand/logo-light.png') }}" alt="" class="page-loader__logo page-loader__logo--ghost">
                    <span class="page-loader__logo-fill" style="--logo-mask: url('{{ asset('images/brand/logo-light.png') }}')"><img src="{{ asset('images/brand/logo-light.png') }}" alt="London Threads & Tassels" class="page-loader__logo"></span>
                </div>
            </div>
            <p class="page-loader__tagline">Hand-embroidered in London</p>
            <p class="page-loader__count"><span id="loader-count">0%</span></p>
            <span class="page-loader__fill" id="loader-fill" hidden></span>
        </div>
    </div>

    <div id="route-veil" aria-hidden="true"><span id="route-veil__mark">LONDON THREADS &amp; TASSELS</span></div>

    <div id="cursor-view" aria-hidden="true">View</div>

    <a href="https://wa.me/442012345678" target="_blank" rel="noopener" class="whatsapp-bubble" aria-label="Chat with us on WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2Zm0 18.2a8.1 8.1 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.2-.3.2-.5.1-.2-.1-1-.4-2-1.2-.7-.6-1.2-1.4-1.4-1.6-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 2 3 4.7 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
    </a>

    <div id="site-topbar" class="site-topbar">
        @if($promotion)
            @php($ribbonTag = $promotion->cta_url ? 'a' : 'div')
            <{{ $ribbonTag }} @if($promotion->cta_url) href="{{ $promotion->cta_url }}" @endif class="festive-ribbon" data-festive aria-label="{{ $promotion->title }}{{ $promotion->discount_label ? ': '.$promotion->discount_label : '' }}">
                <div class="festive-ribbon__inner">
                    @if($promotion->photo)
                        <img src="{{ $promotion->photo }}" alt="" class="festive-ribbon__thumb">
                    @endif
                    <span class="festive-ribbon__diya" aria-hidden="true">
                        <svg viewBox="0 0 32 32" fill="none"><path class="festive-ribbon__flame" d="M16 3c2.6 3.4 3.6 6 3 8.2-.5 1.9-1.8 3-3 3s-2.6-1.1-3-3c-.6-2.2.4-4.8 3-8.2Z" fill="#ffd27a"/><path d="M3 18h26c-.8 5.6-6.3 9-13 9S3.8 23.6 3 18Z" fill="currentColor"/><path d="M8 18c1.6 2 4.5 3 8 3s6.4-1 8-3" stroke="#3a0711" stroke-width="1.2" stroke-linecap="round"/></svg>
                    </span>
                    <p class="festive-ribbon__text">
                        <span class="festive-ribbon__event">{{ $promotion->title }}</span>
                        @if($promotion->discount_label)
                            <span class="festive-ribbon__deal">{{ $promotion->discount_label }}</span>
                        @endif
                        <span class="festive-ribbon__msg">{{ $promotion->message }}</span>
                    </p>
                    @if($promotion->ends_at)
                        <span class="festive-ribbon__timer" data-countdown="{{ $promotion->ends_at->toIso8601String() }}" aria-label="Offer ends {{ $promotion->ends_at->format('j F, H:i') }}">
                            <span class="festive-ribbon__unit"><b data-cd="d">00</b><i>days</i></span>
                            <span class="festive-ribbon__unit"><b data-cd="h">00</b><i>hrs</i></span>
                            <span class="festive-ribbon__unit"><b data-cd="m">00</b><i>min</i></span>
                            <span class="festive-ribbon__unit"><b data-cd="s">00</b><i>sec</i></span>
                        </span>
                    @endif
                    @if($promotion->cta_url)
                        <span class="festive-ribbon__cta">{{ $promotion->cta_label ?: 'Shop the offer' }}</span>
                    @endif
                </div>
                @foreach([[8, 30, 0], [22, 70, 1.2], [41, 20, 2.1], [63, 65, 0.6], [79, 35, 2.8], [92, 60, 1.7]] as [$sparkX, $sparkY, $sparkDelay])
                    <span class="festive-ribbon__spark" aria-hidden="true" style="left: {{ $sparkX }}%; top: {{ $sparkY }}%; animation-delay: {{ $sparkDelay }}s"></span>
                @endforeach
                <span class="festive-ribbon__toran" aria-hidden="true"></span>
            </{{ $ribbonTag }}>
        @else
            <div class="quiet-ribbon">Free UK delivery on every order &middot; Hand-embroidered in small batches</div>
        @endif

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
                    <img src="{{ asset('images/brand/logo-light.png') }}" alt="London Threads & Tassels" class="site-header__logo-light" width="1054" height="580">
                    <img src="{{ asset('images/brand/logo-maroon.png') }}" alt="" aria-hidden="true" class="site-header__logo-dark" width="1054" height="580">
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
                <img src="{{ asset('images/brand/logo-maroon.png') }}" alt="London Threads & Tassels" class="h-20 w-auto" loading="lazy">
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
