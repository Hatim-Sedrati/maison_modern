<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderConfirmationController extends Controller
{
    public function show(Order $order): View
    {
        $order->load('items');

        return view('order.confirmation', compact('order'));
    }
}
