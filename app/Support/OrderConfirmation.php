<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\URL;

class OrderConfirmation
{
    public static function url(Order $order): string
    {
        return URL::signedRoute('order.confirmation', $order);
    }
}
