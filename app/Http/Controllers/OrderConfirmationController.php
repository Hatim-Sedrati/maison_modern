<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\WhatsAppOrder;
use Illuminate\View\View;

class OrderConfirmationController extends Controller
{
    public function show(Order $order): View
    {
        $order->load('items');

        $whatsappUrl = WhatsAppOrder::isEnabled() ? WhatsAppOrder::url($order) : null;

        return view('order.confirmation', compact('order', 'whatsappUrl'));
    }
}
