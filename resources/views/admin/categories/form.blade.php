<x-layouts.admin :title="$category->exists ? 'Edit category' : 'New category'">
    <p class="mono-label text-signal-orange" data-motion>Collection</p>
    <h1 class="display mt-3 text-3xl" data-motion>{{ $category->exists ? 'EDIT CATEGORY' : 'NEW CATEGORY' }}</h1>

    <form class="mt-9 grid max-w-xl gap-5" method="POST" action="{{ $action }}" data-motion>
        @csrf
        @if($method !== 'POST') @method($method) @endif

        <div>
            <label class="mono-label mb-2 block">Name</label>
            <input class="field" name="name" value="{{ old('name', $category->name) }}" placeholder="e.g. Bridal Sets" required maxlength="60">
            @error('name')<p class="mt-1 text-xs text-signal-orange">{{ $message }}</p>@enderror
            @if($category->exists)
                <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">/shop?category={{ $category->slug }}</p>
            @endif
        </div>
        <div>
            <label class="mono-label mb-2 block">Sort order</label>
            <input class="field" type="number" name="sort_order" min="0" max="999" value="{{ old('sort_order', $category->sort_order) }}" placeholder="0">
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Lower numbers appear first in the navbar dropdown.</p>
        </div>
        <label class="flex items-center gap-2 text-xs">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))> Show in the navbar &amp; shop filters
        </label>

        <button class="button-dark mt-2 w-fit">Save category</button>
    </form>
</x-layouts.admin>
