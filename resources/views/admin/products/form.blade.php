<x-layouts.admin :title="$product->exists ? 'Edit suit' : 'Add a suit'">
    <p class="mono-label text-signal-orange" data-motion>Catalogue editor</p>
    <h1 class="display mt-3 text-3xl" data-motion>{{ $product->exists ? 'EDIT SUIT' : 'ADD A SUIT' }}</h1>

    <form class="mt-9 grid max-w-2xl gap-5" method="POST" action="{{ $action }}" enctype="multipart/form-data" data-motion>
        @csrf
        @if($method !== 'POST') @method($method) @endif

        <input class="field" name="name" value="{{ old('name', $product->name) }}" placeholder="Suit name" required>

        <div>
            <label class="mono-label mb-2 block">Category</label>
            <select class="field" name="category_id">
                <option value="">— No category —</option>
                @foreach($categories as $option)
                    <option value="{{ $option->id }}" @selected((int) old('category_id', $product->category_id) === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Controls which navbar dropdown and shop filter this suit appears under.</p>
        </div>

        <textarea class="field" name="description" rows="4" placeholder="Description" required>{{ old('description', $product->description) }}</textarea>

        <div class="grid gap-5 sm:grid-cols-2">
            <input class="field" type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" placeholder="Price in GBP" required>
            <input class="field" type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" placeholder="Stock quantity" required>
        </div>
        <p class="-mt-3 text-[10px] uppercase tracking-wider text-ink/40">Stock is the number of this exact suit you have ready to ship. Shoppers can never order more than this.</p>

        <div class="border border-ink/15 p-5">
            <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="is_on_sale" value="1" @checked(old('is_on_sale', $product->is_on_sale)) id="sale-toggle"> Put this suit on sale</label>
            <div class="mt-3">
                <input class="field" type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" placeholder="Sale price in GBP (must be lower than the price above)">
                @error('sale_price')<p class="mt-1 text-xs text-signal-orange">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="mono-label mb-2 block">Main photo</label>
            @if($product->exists && $product->photo)
                <img src="{{ $product->photo }}" class="mb-3 h-40 w-32 rounded object-cover" alt="">
            @endif
            <input class="field" type="file" name="image" accept="image/*">
            <input class="field mt-3" name="image_url" value="{{ old('image_url', $product->image_url) }}" placeholder="…or paste an image URL">
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Uploading a photo takes priority over the URL.</p>
            @error('image')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
        </div>

        <div class="border-2 border-dashed border-signal-orange/50 bg-signal-orange/5 p-5">
            <label class="mono-label mb-2 block text-graphite-warm">Extra photos — select up to 5 at once</label>
            <p class="mb-3 text-[11px] leading-5 text-ink/60">Hold <strong>Cmd</strong> (Mac) or <strong>Ctrl</strong> (Windows) in the file picker to choose several images in one go. These cycle automatically when a shopper hovers this suit, and appear as thumbnails on the product page.</p>
            @if($product->exists && !empty($product->gallery))
                <div class="mb-3 flex flex-wrap gap-3">
                    @foreach($product->gallery as $path)
                        <label class="relative block">
                            <img src="{{ \Illuminate\Support\Str::startsWith($path, 'http') ? $path : \Illuminate\Support\Facades\Storage::disk('public')->url($path) }}" class="h-24 w-20 rounded object-cover" alt="">
                            <span class="mt-1 flex items-center gap-1 text-[10px]"><input type="checkbox" name="remove_gallery[]" value="{{ $path }}"> Remove</span>
                        </label>
                    @endforeach
                </div>
            @endif
            <input class="field" type="file" name="gallery[]" accept="image/*" multiple>
            @error('gallery')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mono-label mb-2 block">Primary colour</label>
            <div class="grid gap-5 sm:grid-cols-2">
                <input class="field" name="color" value="{{ old('color', $product->color) }}" placeholder="Colour name (e.g. Midnight Black)">
                <div class="flex items-center gap-3">
                    <input class="h-11 w-14 cursor-pointer border border-ink/25" type="color" name="color_hex" value="{{ old('color_hex', $product->color_hex ?: '#151009') }}">
                    <span class="text-xs text-ink/55">Primary swatch</span>
                </div>
            </div>
        </div>
        <div>
            @php($colorsValue = old('colors', collect($product->colors ?? [])->map(fn ($c) => ($c['name'] ?? '').'='.($c['hex'] ?? '#151009'))->implode(', ')))
            <label class="mono-label mb-2 block">Other colours this suit comes in (optional)</label>
            <input class="field" name="colors" value="{{ $colorsValue }}" placeholder="Ruby=#7a1f2b, Emerald=#0b6e4f, Midnight=#151009">
            <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/40">Format: Name=#hex, separated by commas. Shoppers pick from these swatches on the product page.</p>
            @error('colors')<p class="text-xs text-signal-orange">{{ $message }}</p>@enderror
        </div>
        <input class="field" name="sizes" value="{{ old('sizes', is_array($product->sizes) ? implode(', ', $product->sizes) : '') }}" placeholder="Sizes: XS, S, M, L, XL" required>

        <div class="flex flex-wrap gap-6 text-xs">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> Live in shop</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Feature in hero slider</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival', $product->is_new_arrival))> Mark as New Arrival</label>
        </div>

        <button class="button-dark mt-2 w-fit">Save suit</button>
    </form>
</x-layouts.admin>
