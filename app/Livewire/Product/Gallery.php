<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Gallery extends Component
{
    public Product $product;

    public int $activeIndex = 0;

    public function mount(Product $product): void
    {
        $this->product = $product->load(['images' => fn ($q) => $q->orderBy('sort_order')]);

        $primaryIndex = $this->product->images->search(
            fn ($image): bool => (bool) $image->is_primary,
        );

        $this->activeIndex = $primaryIndex === false ? 0 : (int) $primaryIndex;
    }

    public function selectImage(int $index): void
    {
        if ($index >= 0 && $index < $this->product->images->count()) {
            $this->activeIndex = $index;
        }
    }

    public function next(): void
    {
        $count = $this->product->images->count();

        if ($count > 0) {
            $this->activeIndex = ($this->activeIndex + 1) % $count;
        }
    }

    public function previous(): void
    {
        $count = $this->product->images->count();

        if ($count > 0) {
            $this->activeIndex = ($this->activeIndex - 1 + $count) % $count;
        }
    }

    public function render()
    {
        return view('livewire.product.gallery');
    }
}
