<x-layouts.admin :title="'Order '.$order->order_number">
    <a class="nav-link text-signal-orange" href="{{ route('admin.orders') }}" data-motion>&larr; All orders</a>
    <div class="mt-4 flex flex-wrap items-center justify-between gap-4" data-motion>
        <h1 class="display text-2xl md:text-4xl">{{ $order->order_number }}</h1>
        <span class="status-pill status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
    </div>

    <div class="mt-9 grid gap-10 lg:grid-cols-[1fr_320px]">
        <div>
            <div class="divide-y divide-ink/10 border-y border-ink/15" data-motion>
                @foreach($order->items as $item)
                    <div class="flex justify-between py-4 text-sm">
                        <span>{{ $item->product_name }} &middot; Size {{ $item->size }} &times; {{ $item->quantity }}</span>
                        <span>£{{ number_format($item->line_total, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-between text-sm font-medium" data-motion>
                <span>Total</span><span>£{{ number_format($order->total, 2) }}</span>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2" data-motion>
                <div>
                    <p class="mono-label">Customer</p>
                    <p class="mt-2 text-sm">{{ $order->shipping_name }}</p>
                    <p class="text-xs text-ink/60">{{ $order->shipping_email }}</p>
                    <p class="text-xs text-ink/60">{{ $order->shipping_phone ?: 'No phone on file' }}</p>
                </div>
                <div>
                    <p class="mono-label">Delivery address</p>
                    <p class="mt-2 whitespace-pre-line text-sm">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </div>

        <aside class="h-fit space-y-6">
            <div class="border border-ink/15 p-6" data-motion>
                <p class="mono-label">Update status</p>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-4 space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="field">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button class="button-dark w-full">Save status</button>
                </form>
            </div>
            <div class="border border-ink/15 p-6 text-xs leading-6 text-ink/60" data-motion>
                <p class="mono-label text-ink">Payment</p>
                <p class="mt-2">{{ $order->payment?->provider ?? 'Not recorded' }} &middot; {{ $order->payment?->status ?? '—' }}</p>
                <p>Reference: {{ $order->payment?->provider_reference ?? '—' }}</p>
            </div>
        </aside>
    </div>
</x-layouts.admin>
