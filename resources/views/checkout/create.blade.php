<x-layouts.store title="Checkout — London Threads & Tassels">
    <section class="mx-auto max-w-5xl px-5 py-14 md:px-10">
        <p class="mono-label text-signal-orange" data-motion>Secure checkout</p>
        <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>YOUR DETAILS</h1>
        @if($savedAddress)
            <p class="mt-3 text-xs text-ink/55" data-motion>Welcome back — we&rsquo;ve filled in your saved details below.</p>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="mt-10 grid gap-10 lg:grid-cols-[1fr_320px]" data-motion>
            @csrf
            <div class="space-y-5">
                <input class="field" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}" placeholder="Full name" required>
                <input class="field" name="shipping_email" value="{{ old('shipping_email', auth()->user()->email) }}" placeholder="Email address" required>
                <input class="field" name="shipping_phone" value="{{ old('shipping_phone', $savedPhone) }}" placeholder="Phone number">
                <textarea class="field" name="shipping_address" rows="4" placeholder="Delivery address" required>{{ old('shipping_address', $savedAddress) }}</textarea>
                @error('shipping_name')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
                @error('shipping_address')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
                <p class="text-[10px] uppercase tracking-wider text-ink/45">We&rsquo;ll save these details to your account for a faster checkout next time.</p>
            </div>
            <aside class="h-fit bg-soft-linen p-6">
                <p class="mono-label">Order summary</p>
                <div class="mt-4 divide-y divide-ink/10 text-xs">
                    @foreach($cart as $line)
                        <div class="flex justify-between py-2"><span>{{ $line['name'] }} &middot; {{ $line['size'] }} &times; {{ $line['quantity'] }}</span><span>£{{ number_format($line['line_total'], 2) }}</span></div>
                    @endforeach
                </div>
                <p class="mt-4 text-sm leading-6">Every payment is processed securely, and you&rsquo;ll receive an order confirmation by email the moment checkout is complete.</p>
                <div class="mt-6 flex justify-between border-t border-ink/20 pt-5 text-sm"><span>Total</span><strong>£{{ number_format($total, 2) }}</strong></div>
                <button class="button-dark mt-7 w-full">Pay £{{ number_format($total, 2) }}</button>
            </aside>
        </form>
    </section>
</x-layouts.store>
