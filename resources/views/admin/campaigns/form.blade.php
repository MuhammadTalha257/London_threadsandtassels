<x-layouts.admin :title="$campaign->exists ? 'Edit spotlight' : 'New spotlight'">
    <p class="mono-label text-signal-orange" data-motion>Storytelling</p>
    <h1 class="display mt-3 text-3xl" data-motion>{{ $campaign->exists ? 'EDIT SPOTLIGHT' : 'NEW SPOTLIGHT' }}</h1>

    <form class="mt-9 grid max-w-xl gap-5" method="POST" action="{{ $action }}" enctype="multipart/form-data" data-motion>
        @csrf
        @if($method !== 'POST') @method($method) @endif

        <div>
            <label class="mono-label mb-2 block">Headline</label>
            <input class="field" name="title" value="{{ old('title', $campaign->title) }}" placeholder="e.g. THE FESTIVE FLAGSHIP" required maxlength="120">
        </div>
        <div>
            <label class="mono-label mb-2 block">Subtitle (optional)</label>
            <input class="field" name="subtitle" value="{{ old('subtitle', $campaign->subtitle) }}" placeholder="e.g. Our most intricate hand-embroidered piece yet" maxlength="160">
        </div>
        <div>
            <label class="mono-label mb-2 block">Short description (optional)</label>
            <textarea class="field" name="body" rows="3" placeholder="A line or two about the launch" maxlength="400">{{ old('body', $campaign->body) }}</textarea>
        </div>

        <div>
            <label class="mono-label mb-2 block">Model image (full head-to-toe works best)</label>
            @if($campaign->exists && $campaign->photo)
                <img src="{{ $campaign->photo }}" class="mb-3 h-56 w-40 rounded object-cover" alt="">
            @endif
            <input class="field" type="file" name="image" accept="image/*">
            <input class="field mt-3" name="image_url" value="{{ old('image_url', $campaign->image_url) }}" placeholder="…or paste an image URL">
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Uploading a photo takes priority over the URL.</p>
            @error('image')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mono-label mb-2 block">Link to a suit (optional)</label>
            <select class="field" name="product_id">
                <option value="">— No linked suit —</option>
                @foreach($products as $option)
                    <option value="{{ $option->id }}" @selected((int) old('product_id', $campaign->product_id) === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mono-label mb-2 block">Button label (optional)</label>
                <input class="field" name="cta_label" value="{{ old('cta_label', $campaign->cta_label) }}" placeholder="Shop the piece">
            </div>
            <div>
                <label class="mono-label mb-2 block">Button link (optional)</label>
                <input class="field" name="cta_url" value="{{ old('cta_url', $campaign->cta_url) }}" placeholder="/suits/slug or /#collection">
            </div>
        </div>

        <label class="flex items-center gap-2 text-xs">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $campaign->is_active))> Make this spotlight live now
        </label>
        <p class="text-[10px] uppercase tracking-wider text-ink/40">Making this live automatically switches off any other active spotlight.</p>

        <button class="button-dark mt-2 w-fit">Save spotlight</button>
    </form>
</x-layouts.admin>
