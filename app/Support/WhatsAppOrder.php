<?php

namespace App\Support;

use App\Models\Order;
use App\Models\OrderItem;

class WhatsAppOrder
{
    public static function isEnabled(): bool
    {
        return self::number() !== '';
    }

    public static function number(): string
    {
        return preg_replace('/\D+/', '', (string) config('shop.whatsapp_number')) ?? '';
    }

    public static function chatUrl(): string
    {
        return 'https://wa.me/'.self::number();
    }

    public static function url(Order $order): string
    {
        return self::chatUrl().'?text='.rawurlencode(self::message($order));
    }

    public static function message(Order $order): string
    {
        $lines = [
            'Maison Modern',
            'Order #'.$order->order_number,
            'Name: '.$order->customer_name,
            'Total: '.Money::format($order->total),
            'Payment: Cash on Delivery',
            '',
            'Items:',
        ];

        foreach ($order->items as $item) {
            $lines[] = '- '.self::itemLine($item);
        }

        return implode("\n", $lines);
    }

    protected static function itemLine(OrderItem $item): string
    {
        $variant = trim(implode(' / ', array_filter([$item->selected_size, $item->selected_color])));
        $name = $item->product_name.($variant !== '' ? ' ('.$variant.')' : '');

        return $name.' x'.$item->quantity;
    }
}
