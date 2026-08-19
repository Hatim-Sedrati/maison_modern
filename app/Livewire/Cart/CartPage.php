<?php

namespace App\Livewire\Cart;

use App\Exceptions\CartException;
use App\Exceptions\InsufficientStockException;
use App\Services\Cart;
use App\Support\Money;
use Livewire\Component;

class CartPage extends Component
{
    public ?string $error = null;

    public function updateQuantity(string $lineKey, int $quantity, Cart $cart): void
    {
        $this->error = null;

        try {
            $cart->update($lineKey, $quantity);
            $this->dispatch('cart-updated');
        } catch (InsufficientStockException|CartException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function remove(string $lineKey, Cart $cart): void
    {
        $this->error = null;
        $cart->remove($lineKey);
        $this->dispatch('cart-updated');
    }

    public function render(Cart $cart)
    {
        $items = $cart->items();
        $subtotal = $cart->subtotal();
        $deliveryFee = Money::of(config('shop.delivery_fee', '0.00'));
        $total = Money::add($subtotal, $deliveryFee);

        return view('livewire.cart.cart-page', compact('items', 'subtotal', 'deliveryFee', 'total'));
    }
}
