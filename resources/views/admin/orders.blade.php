<x-layouts.admin title="Orders">
    <p class="mono-label text-signal-orange" data-motion>Payments &amp; fulfilment</p>
    <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>ORDERS</h1>

    <div class="mt-9 overflow-x-auto" data-motion>
        <table class="w-full text-left text-xs">
            <thead class="border-y border-ink/15 text-[10px] uppercase tracking-wider">
                <tr><th class="py-3">Order</th><th>Customer</th><th>Items</th><th>Status</th><th>Payment</th><th class="text-right">Total</th></tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr class="border-b border-ink/10">
                        <td class="py-4"><a class="underline" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                        <td>{{ $order->user->name }}<br><span class="text-ink/55">{{ $order->shipping_email }}</span></td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td><span class="status-pill status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->payment?->provider }} &middot; {{ $order->payment?->status }}</td>
                        <td class="text-right">£{{ number_format($order->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
</x-layouts.admin>
