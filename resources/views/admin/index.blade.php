<x-layouts.admin title="Dashboard">
    <div class="flex items-end justify-between" data-motion>
        <div>
            <p class="mono-label text-signal-orange">Studio overview</p>
            <h1 class="display mt-3 text-3xl md:text-5xl">ADMIN DASHBOARD</h1>
        </div>
        <a class="button-dark" href="{{ route('admin.products.create') }}">Add a suit</a>
    </div>

    <div class="mt-10 grid gap-4 md:grid-cols-4">
        <div class="border border-ink/15 p-6" data-motion>
            <p class="mono-label">Collection</p>
            <p class="display mt-5 text-3xl">{{ $productCount }}</p>
            <p class="mt-2 text-xs text-ink/60">Total suits</p>
        </div>
        <div class="border border-ink/15 p-6" data-motion>
            <p class="mono-label">New arrivals</p>
            <p class="display mt-5 text-3xl">{{ $newArrivalCount }}</p>
            <p class="mt-2 text-xs text-ink/60">Live on homepage</p>
        </div>
        <div class="border border-ink/15 p-6" data-motion>
            <p class="mono-label">Customers</p>
            <p class="display mt-5 text-3xl">{{ $customerCount }}</p>
            <p class="mt-2 text-xs text-ink/60">Registered shoppers</p>
        </div>
        <div class="bg-ink p-6 text-white" data-motion>
            <p class="mono-label text-white/60">Payments received</p>
            <p class="display mt-5 text-3xl">£{{ number_format($paymentTotal, 2) }}</p>
            <p class="mt-2 text-xs text-white/60">Completed payments</p>
        </div>
    </div>

    @if($lowStockCount > 0)
        <div class="mt-6 border border-signal-orange/30 bg-signal-orange/5 px-6 py-4 text-xs text-signal-orange" data-motion>
            {{ $lowStockCount }} suit(s) have 3 or fewer in stock — <a class="underline" href="{{ route('admin.products.index') }}">review the collection</a>.
        </div>
    @endif

    <div class="mt-12" data-motion>
        <div class="flex justify-between">
            <h2 class="display text-xl">RECENT ORDERS</h2>
            <a class="nav-link" href="{{ route('admin.orders') }}">View all</a>
        </div>
        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-y border-ink/15 text-[10px] uppercase tracking-wider">
                    <tr><th class="py-3">Order</th><th>Customer</th><th>Status</th><th class="text-right">Total</th></tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr class="border-b border-ink/10">
                            <td class="py-4"><a class="underline" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->user->name }}</td>
                            <td><span class="status-pill status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="text-right">£{{ number_format($order->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
