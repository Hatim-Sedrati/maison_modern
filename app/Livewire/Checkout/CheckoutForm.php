<?php

namespace App\Livewire\Checkout;

use App\Exceptions\CartException;
use App\Exceptions\InsufficientStockException;
use App\Services\Cart;
use App\Services\OrderService;
use App\Support\Money;
use Livewire\Component;

class CheckoutForm extends Component
{
    public string $customer_name = '';

    public string $phone = '';

    public string $city = '';

    public string $address = '';

    public string $email = '';

    public string $postal_code = '';

    public string $notes = '';

    public function mount(): void
    {
        $this->customer_name = old('customer_name', '');
        $this->phone = old('phone', '');
        $this->city = old('city', '');
        $this->address = old('address', '');
        $this->email = old('email', '');
        $this->postal_code = old('postal_code', '');
        $this->notes = old('notes', '');
    }

    public function placeOrder(OrderService $orderService)
    {
        $validated = $this->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['email'] = $validated['email'] ?: null;
        $validated['postal_code'] = $validated['postal_code'] ?: null;
        $validated['notes'] = $validated['notes'] ?: null;

        try {
            $order = $orderService->createFromCart($validated);

            $this->redirect(route('order.confirmation', $order));
        } catch (InsufficientStockException|CartException $e) {
            $this->addError('cart', $e->getMessage());
        }
    }

    public function render(Cart $cart)
    {
        $items = $cart->items();
        $subtotal = $cart->subtotal();
        $deliveryFee = Money::of(config('shop.delivery_fee', '0.00'));
        $total = Money::add($subtotal, $deliveryFee);

        return view('livewire.checkout.checkout-form', compact('items', 'subtotal', 'deliveryFee', 'total'));
    }
}
