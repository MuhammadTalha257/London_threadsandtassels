<x-layouts.admin title="Campaign spotlight">
    <div class="flex flex-wrap items-end justify-between gap-4" data-motion>
        <div>
            <p class="mono-label text-signal-orange">Storytelling</p>
            <h1 class="display mt-3 text-3xl md:text-5xl">CAMPAIGN SPOTLIGHT</h1>
            <p class="mt-3 max-w-lg text-xs leading-6 text-ink/55">Feature a full-length model image on the homepage for a new or hero piece. Only one spotlight is live at a time — activating one switches off the others.</p>
        </div>
        <a class="button-dark" href="{{ route('admin.campaigns.create') }}">New spotlight</a>
    </div>

    <div class="mt-9 space-y-4" data-motion>
        @forelse($campaigns as $campaign)
            <div class="flex flex-wrap items-center justify-between gap-4 border border-ink/15 p-5">
                <div class="flex items-center gap-4">
                    @if($campaign->photo)
                        <img src="{{ $campaign->photo }}" class="h-20 w-16 rounded object-cover" alt="">
                    @endif
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="mono-label">{{ $campaign->title }}</p>
                            @if($campaign->is_active)
                                <span class="status-pill status-pill--paid">Live</span>
                            @endif
                        </div>
                        @if($campaign->subtitle)<p class="mt-1 text-sm">{{ $campaign->subtitle }}</p>@endif
                        @if($campaign->product)<p class="mt-1 text-[10px] uppercase tracking-wider text-ink/45">Linked suit: {{ $campaign->product->name }}</p>@endif
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    @unless($campaign->is_active)
                        <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="title" value="{{ $campaign->title }}">
                            <input type="hidden" name="subtitle" value="{{ $campaign->subtitle }}">
                            <input type="hidden" name="body" value="{{ $campaign->body }}">
                            <input type="hidden" name="image_url" value="{{ $campaign->image_url }}">
                            <input type="hidden" name="cta_label" value="{{ $campaign->cta_label }}">
                            <input type="hidden" name="cta_url" value="{{ $campaign->cta_url }}">
                            <input type="hidden" name="product_id" value="{{ $campaign->product_id }}">
                            <input type="hidden" name="is_active" value="1">
                            <button class="nav-link underline">Make live</button>
                        </form>
                    @endunless
                    <a class="nav-link underline" href="{{ route('admin.campaigns.edit', $campaign) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this spotlight?');">
                        @csrf @method('DELETE')
                        <button class="nav-link text-signal-orange">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink/60">No spotlights yet — create one to feature a model image on the homepage.</p>
        @endforelse
    </div>
</x-layouts.admin>
