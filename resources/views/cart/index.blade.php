<x-layouts.store title="Your bag — London Threads & Tassels">
    <section class="mx-auto max-w-5xl px-5 py-14 md:px-10">
        <p class="mono-label text-signal-orange" data-motion>Your selection</p>
        <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>SHOPPING BAG</h1>

        @if($cart)
            <div class="mt-10 grid gap-10 lg:grid-cols-[1fr_320px]">
                <div class="divide-y divide-ink/15">
                    @foreach($cart as $key => $line)
                        <article class="flex gap-5 py-5" data-motion>
                            <img class="h-32 w-24 object-cover" src="{{ $line['image_url'] }}" alt="{{ $line['name'] }}">
                            <div class="flex flex-1 flex-col justify-between sm:flex-row sm:items-center">
                                <div>
                                    <p class="mono-label">{{ $line['name'] }}</p>
                                    <p class="mt-2 text-xs text-ink/60">Size {{ $line['size'] }}</p>
                                    <form class="mt-3 flex items-center gap-3" method="POST" action="{{ route('cart.update', $key) }}">
                                        @csrf @method('PATCH')
                                        <label class="mono-label text-[10px]">Qty</label>
                                        <input class="field w-16 py-1" type="number" name="quantity" min="1" max="10" value="{{ $line['quantity'] }}">
                                        <button class="nav-link underline">Update</button>
                                    </form>
                                    <form class="mt-2" method="POST" action="{{ route('cart.destroy', $key) }}">
                                        @csrf @method('DELETE')
                                        <button class="nav-link text-signal-orange underline">Remove</button>
                                    </form>
                                </div>
                                <p class="mt-4 text-sm sm:mt-0">£{{ number_format($line['line_total'], 2) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
                <aside class="h-fit bg-soft-linen p-6" data-motion>
                    <p class="mono-label">Order summary</p>
                    <div class="mt-6 flex justify-between text-sm"><span>Subtotal</span><span>£{{ number_format($total, 2) }}</span></div>
                    <div class="mt-3 flex justify-between text-sm"><span>UK delivery</span><span>Complimentary</span></div>
                    <div class="mt-5 flex justify-between border-t border-ink/20 pt-5 text-sm font-medium"><span>Total</span><span>£{{ number_format($total, 2) }}</span></div>
                    <a href="{{ route('checkout.create') }}" class="button-dark mt-7 w-full">Checkout securely</a>
                </aside>
            </div>
        @else
            <p class="mt-9 text-sm text-ink/65" data-motion>Your bag is currently empty. <a class="underline" href="{{ route('store.index') }}#collection">Explore the collection.</a></p>
        @endif
    </section>
</x-layouts.store>
