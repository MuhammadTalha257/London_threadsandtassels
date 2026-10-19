<x-layouts.admin title="Festival offers">
    <div class="flex items-end justify-between" data-motion>
        <div>
            <p class="mono-label text-signal-orange">Marketing</p>
            <h1 class="display mt-3 text-3xl md:text-5xl">FESTIVAL OFFERS</h1>
            <p class="mt-3 max-w-lg text-xs leading-6 text-ink/55">Run a festival or event discount — Diwali, Eid, Christmas, a launch. The live offer shows in the ribbon at the top of every page, with a countdown when it has an end date. Only one offer is live at a time.</p>
        </div>
        <a class="button-dark" href="{{ route('admin.promotions.create') }}">New festival offer</a>
    </div>

    <div class="mt-9 space-y-4" data-motion>
        @forelse($promotions as $promotion)
            <div class="flex flex-wrap items-center justify-between gap-4 border border-ink/15 p-5">
                <div class="flex items-center gap-4">
                    @if($promotion->photo)
                        <img src="{{ $promotion->photo }}" class="h-14 w-14 shrink-0 rounded object-cover" alt="">
                    @endif
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="mono-label">{{ $promotion->title }}</p>
                            @if($promotion->is_active)
                                <span class="status-pill status-pill--paid">Live</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm">@if($promotion->discount_label)<strong>{{ $promotion->discount_label }}</strong> — @endif{{ $promotion->message }}</p>
                        @if($promotion->ends_at)
                            <p class="mt-1 text-[10px] uppercase tracking-wider {{ $promotion->ends_at->isPast() ? 'text-signal-orange' : 'text-ink/45' }}">{{ $promotion->ends_at->isPast() ? 'Ended' : 'Ends' }} {{ $promotion->ends_at->format('j M Y, H:i') }}</p>
                        @endif
                        @if($promotion->cta_label)
                            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/45">Button: {{ $promotion->cta_label }} &rarr; {{ $promotion->cta_url ?: '#' }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    @unless($promotion->is_active)
                        <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="title" value="{{ $promotion->title }}">
                            <input type="hidden" name="message" value="{{ $promotion->message }}">
                            <input type="hidden" name="discount_label" value="{{ $promotion->discount_label }}">
                            <input type="hidden" name="ends_at" value="{{ $promotion->ends_at?->format('Y-m-d H:i:s') }}">
                            <input type="hidden" name="cta_label" value="{{ $promotion->cta_label }}">
                            <input type="hidden" name="cta_url" value="{{ $promotion->cta_url }}">
                            <input type="hidden" name="image_url" value="{{ $promotion->image_url }}">
                            <input type="hidden" name="is_active" value="1">
                            <button class="nav-link underline">Make live</button>
                        </form>
                    @endunless
                    <a class="nav-link underline" href="{{ route('admin.promotions.edit', $promotion) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" onsubmit="return confirm('Delete this offer?');">
                        @csrf @method('DELETE')
                        <button class="nav-link text-signal-orange">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink/60">No festival offers yet — create one and it appears in the header ribbon across the site.</p>
        @endforelse
    </div>
</x-layouts.admin>
