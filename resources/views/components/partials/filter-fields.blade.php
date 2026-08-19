@unless ($hideCategory)
    <fieldset>
        <legend class="mb-3 text-[11px] uppercase tracking-[0.16em]">Category</legend>
        <div class="space-y-1">
            <label class="flex min-h-11 items-center gap-2 text-sm">
                <input type="radio" name="category" value="" @checked(! $filters['category']) class="accent-charcoal">
                All
            </label>
            @foreach ($categories as $category)
                <label class="flex min-h-11 items-center gap-2 text-sm">
                    <input type="radio" name="category" value="{{ $category->slug }}" @checked($filters['category'] === $category->slug) class="accent-charcoal">
                    {{ $category->name }}
                </label>
            @endforeach
        </div>
    </fieldset>
@endunless

<fieldset>
    <legend class="mb-3 text-[11px] uppercase tracking-[0.16em]">Gender</legend>
    <div class="space-y-1">
        <label class="flex min-h-11 items-center gap-2 text-sm">
            <input type="radio" name="gender" value="" @checked(! $filters['gender']) class="accent-charcoal">
            All
        </label>
        @foreach (\App\Enums\ProductGender::cases() as $gender)
            <label class="flex min-h-11 items-center gap-2 text-sm">
                <input type="radio" name="gender" value="{{ $gender->value }}" @checked($filters['gender'] === $gender->value) class="accent-charcoal">
                {{ $gender->getLabel() }}
            </label>
        @endforeach
    </div>
</fieldset>

<div>
    <label class="flex min-h-11 items-center gap-2 text-sm">
        <input type="checkbox" name="in_stock" value="1" @checked($filters['in_stock']) class="accent-charcoal">
        In stock
    </label>
</div>

<div>
    <label for="shop-sort-{{ $sortId }}" class="mb-2 block text-[11px] uppercase tracking-[0.16em]">Sort</label>
    <select id="shop-sort-{{ $sortId }}" name="sort" class="input-field">
        <option value="featured" @selected($filters['sort'] === 'featured')>Featured</option>
        <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
        <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Price: low to high</option>
        <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Price: high to low</option>
    </select>
</div>
