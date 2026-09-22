<x-layouts.admin :title="$customer->name">
    <a class="nav-link text-signal-orange" href="{{ route('admin.customers') }}" data-motion>&larr; All customers</a>
    <h1 class="display mt-4 text-3xl md:text-5xl" data-motion>{{ $customer->name }}</h1>

    <div class="mt-9 grid gap-10 lg:grid-cols-[280px_1fr]">
        <aside class="h-fit border border-ink/15 p-6" data-motion>
            <p class="mono-label">Contact details</p>
            <dl class="mt-4 space-y-3 text-xs leading-6 text-ink/70">
                <div><dt class="text-ink/45">Email</dt><dd>{{ $customer->email }}</dd></div>
                <div><dt class="text-ink/45">Phone</dt><dd>{{ $customer->phone ?: '—' }}</dd></div>
                <div><dt class="text-ink/45">Address on file</dt><dd class="whitespace-pre-line">{{ $customer->address ?: '—' }}</dd></div>
                <div><dt class="text-ink/45">Customer since</dt><dd>{{ $customer->created_at->format('d M Y') }}</dd></div>
            </dl>
        </aside>

        <div>
            <p class="mono-label mb-5">Order history</p>
            @forelse($orders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="surface-card mb-4 block border border-ink/15 p-5" data-motion>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="mono-label">{{ $order->order_number }}</p>
                        <span class="status-pill status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    <p class="mt-2 text-xs text-ink/55">{{ $order->created_at->format('d M Y') }} &middot; {{ $order->items->sum('quantity') }} item(s)</p>
                    <p class="mt-3 text-sm">£{{ number_format($order->total, 2) }}</p>
                </a>
            @empty
                <p class="text-sm text-ink/60" data-motion>No orders placed yet.</p>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
