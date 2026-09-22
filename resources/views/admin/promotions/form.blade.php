<x-layouts.admin :title="$promotion->exists ? 'Edit banner' : 'New banner'">
    <p class="mono-label text-signal-orange" data-motion>Marketing</p>
    <h1 class="display mt-3 text-3xl" data-motion>{{ $promotion->exists ? 'EDIT BANNER' : 'NEW BANNER' }}</h1>

    <form class="mt-9 grid max-w-xl gap-5" method="POST" action="{{ $action }}" enctype="multipart/form-data" data-motion>
        @csrf
        @if($method !== 'POST') @method($method) @endif

        <div>
            <label class="mono-label mb-2 block">Title</label>
            <input class="field" name="title" value="{{ old('title', $promotion->title) }}" placeholder="e.g. FESTIVE SALE" required maxlength="60">
        </div>
        <div>
            <label class="mono-label mb-2 block">Message</label>
            <input class="field" name="message" value="{{ old('message', $promotion->message) }}" placeholder="e.g. 50% off selected suits — this week only" required maxlength="160">
        </div>
        <div>
            <label class="mono-label mb-2 block">Banner image (optional)</label>
            @if($promotion->exists && $promotion->photo)
                <img src="{{ $promotion->photo }}" class="mb-3 h-32 w-24 rounded object-cover" alt="">
            @endif
            <input class="field" type="file" name="image" accept="image/*">
            <input class="field mt-3" name="image_url" value="{{ old('image_url', $promotion->image_url) }}" placeholder="…or paste an image URL">
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Shown inside the popup banner when the site loads. Uploading a photo takes priority over the URL.</p>
            @error('image')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mono-label mb-2 block">Button label (optional)</label>
                <input class="field" name="cta_label" value="{{ old('cta_label', $promotion->cta_label) }}" placeholder="Shop the sale">
            </div>
            <div>
                <label class="mono-label mb-2 block">Button link (optional)</label>
                <input class="field" name="cta_url" value="{{ old('cta_url', $promotion->cta_url) }}" placeholder="/#collection">
            </div>
        </div>
        <label class="flex items-center gap-2 text-xs">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promotion->is_active))> Make this banner live now
        </label>
        <p class="text-[10px] uppercase tracking-wider text-ink/40">Making this live automatically switches off any other active banner.</p>

        <button class="button-dark mt-2 w-fit">Save banner</button>
    </form>
</x-layouts.admin>
