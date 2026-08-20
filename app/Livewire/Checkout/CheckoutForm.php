<?php

namespace App\Livewire\Checkout;

use App\Exceptions\CartException;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreOrderRequest;
use App\Services\Cart;
use App\Services\OrderService;
use App\Support\Money;
use App\Support\OrderConfirmation;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
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
        $key = 'checkout:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 8)) {
            $this->addError('cart', 'Please wait a moment before trying again.');

            return;
        }

        RateLimiter::hit($key, 60);

        $validated = $this->validate((new StoreOrderRequest)->rules());

        $validated['email'] = $validated['email'] ?: null;
        $validated['postal_code'] = $validated['postal_code'] ?: null;
        $validated['notes'] = $validated['notes'] ?: null;

        try {
            $order = $orderService->createFromCart($validated);

            $this->redirect(OrderConfirmation::url($order));
        } catch (InsufficientStockException|CartException $e) {
            $this->addError('cart', $e->getMessage());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('cart', 'We could not place your order. Please try again.');
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
