@props([
    'products',
    'emptyTitle' => 'No products found',
    'emptyText' => 'Try another search or explore our collections.',
    'emptyHref' => null,
    'emptyAction' => 'Continue Shopping',
])

@if ($products->isEmpty())
    <x-empty-state
        :title="$emptyTitle"
        :text="$emptyText"
        :href="$emptyHref ?? route('shop.new-arrivals')"
        :action="$emptyAction"
    />
@else
    <div class="grid grid-cols-2 gap-x-3 gap-y-10 sm:gap-x-4 md:grid-cols-3 md:gap-x-6 md:gap-y-14 lg:grid-cols-4">
        @foreach ($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
@endif
