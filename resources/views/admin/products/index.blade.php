<x-layouts.admin title="Products">
    <div class="flex items-end justify-between" data-motion>
        <div>
            <p class="mono-label text-signal-orange">Catalogue</p>
            <h1 class="display mt-3 text-3xl md:text-5xl">SUITS</h1>
        </div>
        <a class="button-dark" href="{{ route('admin.products.create') }}">Add a suit</a>
    </div>

    <div class="mt-9 overflow-x-auto" data-motion>
        <table class="w-full text-left text-xs">
            <thead class="border-y border-ink/15 text-[10px] uppercase tracking-wider">
                <tr><th class="py-3">Suit</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="border-b border-ink/10">
                        <td class="flex items-center gap-3 py-4">
                            <img src="{{ $product->photo }}" class="h-12 w-10 rounded object-cover" alt="">
                            <div>
                                <p>{{ $product->name }}</p>
                                <p class="mt-1 flex items-center gap-2 text-[10px] text-ink/55">
                                    @if($product->color_hex)<span class="swatch-dot" style="background:{{ $product->color_hex }}"></span>@endif
                                    {{ $product->color }}
                                    @if($product->is_new_arrival)<span class="badge badge--new !static !ml-1">New</span>@endif
                                </p>
                            </div>
                        </td>
                        <td>£{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->is_active ? 'Live' : 'Hidden' }}</td>
                        <td class="text-right">
                            <a class="nav-link underline" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                            <form class="ml-3 inline" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Remove this suit from the catalogue?');">
                                @csrf @method('DELETE')
                                <button class="nav-link text-signal-orange">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $products->links() }}</div>
</x-layouts.admin>
