@props(['title' => 'Admin'])
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — London Threads & Tassels</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.index') }}" class="flex items-center gap-3 px-6 py-7">
                <img src="{{ asset('images/brand/logo-maroon.png') }}" class="h-10 w-auto" alt="London Threads and Tassels">
                <span class="logo text-lg leading-tight text-ink">Admin</span>
            </a>
            <nav class="mt-4 flex flex-col gap-1 px-3 text-sm">
                <a href="{{ route('admin.index') }}" class="admin-nav-link {{ request()->routeIs('admin.index') ? 'is-active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories*') ? 'is-active' : '' }}">Categories</a>
                <a href="{{ route('admin.orders') }}" class="admin-nav-link {{ request()->routeIs('admin.orders*') ? 'is-active' : '' }}">Orders</a>
                <a href="{{ route('admin.customers') }}" class="admin-nav-link {{ request()->routeIs('admin.customers*') ? 'is-active' : '' }}">Customers</a>
                <a href="{{ route('admin.hero-slides.index') }}" class="admin-nav-link {{ request()->routeIs('admin.hero-slides*') ? 'is-active' : '' }}">Header images</a>
                <a href="{{ route('admin.promotions.index') }}" class="admin-nav-link {{ request()->routeIs('admin.promotions*') ? 'is-active' : '' }}">Festival offers</a>
                <a href="{{ route('admin.campaigns.index') }}" class="admin-nav-link {{ request()->routeIs('admin.campaigns*') ? 'is-active' : '' }}">Campaign spotlight</a>
            </nav>
            <div class="mt-auto px-3 pb-6">
                <a href="{{ route('store.index') }}" class="admin-nav-link">&larr; View store</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="admin-nav-link w-full text-left">Sign out</button>
                </form>
            </div>
        </aside>
        <div class="admin-content">
            <header class="flex items-center justify-between border-b border-ink/10 bg-white/80 px-6 py-4 backdrop-blur md:px-10">
                <p class="mono-label">{{ $title }}</p>
                <p class="mono-label text-ink/50">{{ auth()->user()->name }}</p>
            </header>
            @if(session('success'))
                <div class="bg-soft-linen px-6 py-3 text-center text-xs text-ink md:px-10">{{ session('success') }}</div>
            @endif
            <main class="px-6 py-10 md:px-10">{{ $slot }}</main>
        </div>
    </div>
</body>
</html>
