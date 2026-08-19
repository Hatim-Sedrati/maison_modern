<?php

namespace App\Livewire\Layout;

use Livewire\Attributes\On;
use Livewire\Component;

class CartToast extends Component
{
    public bool $visible = false;

    #[On('cart-updated')]
    public function notify(): void
    {
        $this->visible = true;
    }

    public function dismiss(): void
    {
        $this->visible = false;
    }

    public function render()
    {
        return view('livewire.layout.cart-toast');
    }
}
