<x-layouts.admin title="Header images">
    <div class="flex flex-wrap items-end justify-between gap-5" data-motion>
        <div>
            <p class="mono-label text-signal-orange">Homepage</p>
            <h1 class="display mt-3 text-3xl md:text-5xl">HEADER IMAGES</h1>
            <p class="mt-3 max-w-lg text-xs leading-6 text-ink/55">Your offer banners: each photo plays as a full-screen slide at the top of the homepage, in position order, with its own offer (e.g. 30% off), headline and button. When visitors scroll, the slide opens like palace doors into a gallery of the collection. Linking a suit adds its price to the slide — remove it any time.</p>
        </div>
        <a class="button-dark" href="{{ route('admin.hero-slides.create') }}">Add offer slide</a>
    </div>

    <div class="mt-9 grid gap-4 sm:grid-cols-2 xl:grid-cols-3" data-motion>
        @forelse($slides as $slide)
            <div class="surface-card overflow-hidden">
                <div class="relative aspect-[4/5] bg-soft-linen">
                    @if($slide->photo)
                        <img src="{{ $slide->photo }}" alt="{{ $slide->headline }}" class="h-full w-full object-cover">
                    @endif
                    <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium">Position {{ $slide->sort_order }}</span>
                    @if($slide->is_active)
                        <span class="status-pill status-pill--paid absolute right-3 top-3">Live</span>
                    @else
                        <span class="status-pill status-pill--pending absolute right-3 top-3">Hidden</span>
                    @endif
                </div>
                <div class="space-y-3 p-5">
                    @if($slide->discount_label)
                        <span class="inline-block rounded-full bg-signal-orange px-3 py-1 text-[11px] font-semibold text-white">{{ $slide->discount_label }}</span>
                    @endif
                    <p class="display text-xl leading-tight">{{ $slide->headline ?: 'No headline' }}</p>
                    @if($slide->subhead)
                        <p class="text-xs leading-5 text-ink/60">{{ $slide->subhead }}</p>
                    @endif

                    @if($slide->product)
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-pale-stone px-3 py-2 text-xs">
                            <span class="truncate">Linked suit: <strong>{{ $slide->product->name }}</strong></span>
                            <form method="POST" action="{{ route('admin.hero-slides.detach-product', $slide) }}">
                                @csrf @method('PATCH')
                                <button class="shrink-0 text-signal-orange underline">Remove suit</button>
                            </form>
                        </div>
                    @else
                        <p class="text-[11px] text-ink/45">No suit linked — photo only.</p>
                    @endif

                    <div class="flex items-center gap-4 pt-1 text-xs">
                        <a class="nav-link underline" href="{{ route('admin.hero-slides.edit', $slide) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}" onsubmit="return confirm('Delete this header image?');">
                            @csrf @method('DELETE')
                            <button class="nav-link text-signal-orange">Delete image</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="shop-empty sm:col-span-2 xl:col-span-3">
                <p class="text-sm text-ink/70">No header images yet, so the homepage is using the built-in campaign photos.</p>
                <a class="button-dark mt-5" href="{{ route('admin.hero-slides.create') }}">Add your first header image</a>
            </div>
        @endforelse
    </div>
</x-layouts.admin>
