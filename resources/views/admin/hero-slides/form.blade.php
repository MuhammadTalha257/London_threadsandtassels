<x-layouts.admin :title="$slide->exists ? 'Edit header image' : 'New header image'">
    <p class="mono-label text-signal-orange" data-motion>Homepage</p>
    <h1 class="display mt-3 text-3xl" data-motion>{{ $slide->exists ? 'EDIT HEADER IMAGE' : 'NEW HEADER IMAGE' }}</h1>

    <form class="mt-9 grid max-w-xl gap-5" method="POST" action="{{ $action }}" enctype="multipart/form-data" data-motion>
        @csrf
        @if($method !== 'POST') @method($method) @endif

        <div>
            <label class="mono-label mb-2 block">Photo (large and sharp — at least 1600px wide; keep the face in the upper half)</label>
            @if($slide->exists && $slide->photo)
                <img src="{{ $slide->photo }}" class="mb-3 h-56 w-44 rounded object-cover" alt="">
            @endif
            <input class="field" type="file" name="image" accept="image/*">
            <input class="field mt-3" name="image_url" value="{{ old('image_url', $slide->image_url) }}" placeholder="…or paste an image URL">
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Uploading a photo replaces the current one and takes priority over the URL.</p>
            @error('image')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
            @error('image_url')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mono-label mb-2 block">Offer (optional)</label>
                <input class="field" name="discount_label" value="{{ old('discount_label', $slide->discount_label) }}" placeholder="e.g. 30% off" maxlength="40">
                <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Shown big in a gold seal on the slide.</p>
            </div>
            <div>
                <label class="mono-label mb-2 block">Small line above headline (optional)</label>
                <input class="field" name="kicker" value="{{ old('kicker', $slide->kicker) }}" placeholder="e.g. Diwali sale · this week only" maxlength="60">
            </div>
        </div>

        <div>
            <label class="mono-label mb-2 block">Headline (optional)</label>
            <input class="field" name="headline" value="{{ old('headline', $slide->headline) }}" placeholder="e.g. Dressed for Diwali" maxlength="80">
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Shown large on this slide. Keep it short — five or six words.</p>
        </div>
        <div>
            <label class="mono-label mb-2 block">Short line (optional)</label>
            <input class="field" name="subhead" value="{{ old('subhead', $slide->subhead) }}" placeholder="e.g. Hand-embroidered in small batches in London" maxlength="180">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mono-label mb-2 block">Button text (optional)</label>
                <input class="field" name="cta_label" value="{{ old('cta_label', $slide->cta_label) }}" placeholder="e.g. Shop the sale" maxlength="40">
            </div>
            <div>
                <label class="mono-label mb-2 block">Button link (optional)</label>
                <input class="field" name="cta_url" value="{{ old('cta_url', $slide->cta_url) }}" placeholder="e.g. /shop?category=bridal" maxlength="255">
            </div>
        </div>
        <p class="-mt-2 text-[10px] uppercase tracking-wider text-ink/40">Left empty, the button goes to the linked suit, or to the shop.</p>

        <div>
            <label class="mono-label mb-2 block">Link a suit (optional)</label>
            <select class="field" name="product_id">
                <option value="">— No linked suit (photo only) —</option>
                @foreach($products as $option)
                    <option value="{{ $option->id }}" @selected((int) old('product_id', $slide->product_id) === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">A linked suit shows its price on the slide. Choose “No linked suit” to remove it.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mono-label mb-2 block">Position</label>
                <input class="field" type="number" min="0" max="999" name="sort_order" value="{{ old('sort_order', $slide->sort_order) }}">
                <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Lowest number plays first.</p>
            </div>
            <label class="flex items-center gap-2 self-center text-xs">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slide->is_active))> Show on the homepage
            </label>
        </div>

        <button class="button-dark mt-2 w-fit">Save header image</button>
    </form>
</x-layouts.admin>
