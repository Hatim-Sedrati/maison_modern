<?php

namespace App\Livewire\Layout;

use App\Services\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(Cart $cart): void
    {
        $this->count = $cart->quantity();
    }

    #[On('cart-updated')]
    public function refreshCount(Cart $cart): void
    {
        $this->count = $cart->quantity();
    }

    public function render()
    {
        return view('livewire.layout.cart-count');
    }
}
