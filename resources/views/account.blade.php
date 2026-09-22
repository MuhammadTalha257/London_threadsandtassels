<x-layouts.store title="My account — London Threads &amp; Tassels">
    @php
        $activeTab = request('tab', 'overview');
        $totalOrders = $orders->count();
        $totalSpent = $orders->where('status', '!=', 'cancelled')->sum('total');
        $lastOrder = $orders->first();
    @endphp

    <section class="mx-auto max-w-6xl px-5 py-14 md:px-10">
        <p class="mono-label text-signal-orange" data-motion>Welcome back</p>
        <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>{{ auth()->user()->name }}</h1>

        <div class="mt-10 grid gap-8 lg:grid-cols-[220px_1fr]">
            <aside class="dash-nav" data-motion>
                <button type="button" class="dash-nav__link {{ $activeTab === 'overview' ? 'is-active' : '' }}" data-dash-tab="overview">Overview</button>
                <button type="button" class="dash-nav__link {{ $activeTab === 'orders' ? 'is-active' : '' }}" data-dash-tab="orders">Order history</button>
                <button type="button" class="dash-nav__link {{ $activeTab === 'profile' ? 'is-active' : '' }}" data-dash-tab="profile">Profile &amp; address</button>
                <form method="POST" action="{{ route('logout') }}" class="mt-4 border-t border-ink/10 pt-4">
                    @csrf
                    <button class="dash-nav__link text-signal-orange">Sign out</button>
                </form>
            </aside>

            <div>
                {{-- Overview --}}
                <div data-dash-panel="overview" class="{{ $activeTab === 'overview' ? '' : 'hidden' }}">
                    <div class="motion-stagger grid gap-4 sm:grid-cols-3">
                        <div class="dash-stat" data-motion>
                            <p class="dash-stat__value">{{ $totalOrders }}</p>
                            <p class="dash-stat__label">Order{{ $totalOrders === 1 ? '' : 's' }} placed</p>
                        </div>
                        <div class="dash-stat" data-motion>
                            <p class="dash-stat__value">£{{ number_format($totalSpent, 2) }}</p>
                            <p class="dash-stat__label">Total spent</p>
                        </div>
                        <div class="dash-stat" data-motion>
                            <p class="dash-stat__value">{{ $lastOrder ? ucfirst($lastOrder->status) : '—' }}</p>
                            <p class="dash-stat__label">Latest order status</p>
                        </div>
                    </div>

                    <div class="mt-8 border border-ink/15 p-6" data-motion>
                        <p class="mono-label">Saved details</p>
                        <dl class="mt-4 grid gap-3 text-xs leading-6 text-ink/70 sm:grid-cols-3">
                            <div><dt class="text-ink/45">Email</dt><dd>{{ auth()->user()->email }}</dd></div>
                            <div><dt class="text-ink/45">Phone</dt><dd>{{ auth()->user()->phone ?: 'Not on file yet' }}</dd></div>
                            <div><dt class="text-ink/45">Delivery address</dt><dd class="whitespace-pre-line">{{ auth()->user()->address ?: 'Not on file yet' }}</dd></div>
                        </dl>
                        <button type="button" class="nav-link mt-5 underline" data-dash-tab="profile">Edit these details</button>
                    </div>

                    @if($lastOrder)
                        <div class="mt-8">
                            <p class="mono-label mb-4">Most recent order</p>
                            <a href="{{ route('orders.show', $lastOrder) }}" class="surface-card block border border-ink/15 p-5" data-motion>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="mono-label">{{ $lastOrder->order_number }}</p>
                                    <span class="status-pill status-pill--{{ $lastOrder->status }}">{{ ucfirst($lastOrder->status) }}</span>
                                </div>
                                <p class="mt-2 text-xs text-ink/55">{{ $lastOrder->created_at->format('d M Y') }} &middot; {{ $lastOrder->items->sum('quantity') }} item(s)</p>
                                <p class="mt-3 text-sm">£{{ number_format($lastOrder->total, 2) }}</p>
                            </a>
                        </div>
                    @else
                        <p class="mt-8 text-sm text-ink/60" data-motion>You haven&rsquo;t placed an order yet. <a class="underline" href="{{ route('store.index') }}#collection">Start browsing.</a></p>
                    @endif
                </div>

                {{-- Order history --}}
                <div data-dash-panel="orders" class="{{ $activeTab === 'orders' ? '' : 'hidden' }}">
                    <p class="mono-label mb-5">Order history</p>
                    @forelse($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="surface-card mb-4 block border border-ink/15 p-5" data-motion>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="mono-label">{{ $order->order_number }}</p>
                                <span class="status-pill status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <p class="mt-2 text-xs text-ink/55">{{ $order->created_at->format('d M Y') }} &middot; {{ $order->items->sum('quantity') }} item(s)</p>
                            <p class="mt-3 text-sm">£{{ number_format($order->total, 2) }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-ink/60" data-motion>You haven&rsquo;t placed an order yet. <a class="underline" href="{{ route('store.index') }}#collection">Start browsing.</a></p>
                    @endforelse
                </div>

                {{-- Profile & address --}}
                <div data-dash-panel="profile" class="{{ $activeTab === 'profile' ? '' : 'hidden' }}">
                    <p class="mono-label mb-5">Profile &amp; delivery address</p>
                    <form method="POST" action="{{ route('account.update') }}" class="max-w-lg space-y-6" data-motion>
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="mono-label mb-2 block text-ink/50" for="name">Full name</label>
                            <input id="name" name="name" type="text" class="field" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name') <p class="mt-1 text-xs text-signal-orange">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mono-label mb-2 block text-ink/50" for="email">Email</label>
                            <input id="email" type="email" class="field opacity-60" value="{{ auth()->user()->email }}" disabled>
                            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Email can&rsquo;t be changed here — contact us via the Help page.</p>
                        </div>
                        <div>
                            <label class="mono-label mb-2 block text-ink/50" for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" class="field" value="{{ old('phone', auth()->user()->phone) }}" placeholder="e.g. 07123 456789">
                            @error('phone') <p class="mt-1 text-xs text-signal-orange">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mono-label mb-2 block text-ink/50" for="address">Delivery address</label>
                            <textarea id="address" name="address" rows="3" class="field" placeholder="Used to pre-fill checkout next time">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address') <p class="mt-1 text-xs text-signal-orange">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="button-dark">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.store>
