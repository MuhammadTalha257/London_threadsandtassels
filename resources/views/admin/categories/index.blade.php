<x-layouts.admin title="Categories">
    <div class="flex items-end justify-between" data-motion>
        <div>
            <p class="mono-label text-signal-orange">Collection</p>
            <h1 class="display mt-3 text-3xl md:text-5xl">CATEGORIES</h1>
            <p class="mt-3 max-w-lg text-xs leading-6 text-ink/55">Organise your suits into categories. Every active category appears as a dropdown link under "Shop" on the storefront, letting shoppers filter straight to it.</p>
        </div>
        <a class="button-dark" href="{{ route('admin.categories.create') }}">New category</a>
    </div>

    <div class="mt-9 space-y-4" data-motion>
        @forelse($categories as $category)
            <div class="flex flex-wrap items-center justify-between gap-4 border border-ink/15 p-5">
                <div>
                    <div class="flex items-center gap-3">
                        <p class="mono-label">{{ $category->name }}</p>
                        @if($category->is_active)
                            <span class="status-pill status-pill--paid">Live in navbar</span>
                        @else
                            <span class="status-pill">Hidden</span>
                        @endif
                    </div>
                    <p class="mt-2 text-[10px] uppercase tracking-wider text-ink/45">/shop?category={{ $category->slug }} &middot; {{ $category->products_count }} {{ Str::plural('suit', $category->products_count) }} &middot; sort {{ $category->sort_order }}</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <a class="nav-link underline" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Suits in it will simply become uncategorised.');">
                        @csrf @method('DELETE')
                        <button class="nav-link text-signal-orange">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink/60">No categories yet — create one to start organising your collection and showing it in the navbar.</p>
        @endforelse
    </div>
</x-layouts.admin>
