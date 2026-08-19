@props(['products', 'emptyTitle' => 'No products found', 'emptyText' => 'Try another category or search.'])

@if ($products->isEmpty())
    <x-empty-state :title="$emptyTitle" :text="$emptyText" :href="route('shop.index')" action="Shop now" />
@else
    <div class="grid grid-cols-2 gap-x-3 gap-y-10 md:grid-cols-3 md:gap-x-6 lg:grid-cols-4 lg:gap-y-14">
        @foreach ($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
@endif
