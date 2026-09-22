<x-layouts.store title="Order confirmed — London Threads & Tassels">
    <section class="mx-auto max-w-3xl px-5 py-16 md:px-10">
        <p class="mono-label text-signal-orange" data-motion>Order confirmed</p>
        <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>THANK YOU.</h1>
        <p class="mt-6 text-sm leading-7" data-motion>Your order <strong>{{ $order->order_number }}</strong> is confirmed. We&rsquo;ll send progress updates to {{ $order->shipping_email }}.</p>

        <div class="mt-10 border-y border-ink/15 py-5" data-motion>
            @foreach($order->items as $item)
                <div class="flex justify-between py-2 text-sm"><span>{{ $item->product_name }} &middot; {{ $item->size }} &times; {{ $item->quantity }}</span><span>£{{ number_format($item->line_total, 2) }}</span></div>
            @endforeach
            <div class="mt-4 flex justify-between border-t border-ink/15 pt-4 text-sm font-medium"><span>Total paid</span><span>£{{ number_format($order->total, 2) }}</span></div>
        </div>

        <div class="mt-6 text-xs leading-6 text-ink/60" data-motion>
            <p class="mono-label mb-1 text-ink">Delivering to</p>
            {{ $order->shipping_name }}<br>
            {{ $order->shipping_address }}
        </div>

        <div class="mt-8 flex flex-wrap gap-4" data-motion>
            <a href="{{ route('store.index') }}" class="button-dark">Continue shopping</a>
            <a href="{{ route('account') }}" class="button-light !border-ink !text-ink hover:!bg-ink hover:!text-white">View my orders</a>
        </div>
    </section>
</x-layouts.store>
