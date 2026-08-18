<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case CashOnDelivery = 'cash_on_delivery';

    public function getLabel(): string
    {
        return match ($this) {
            self::CashOnDelivery => 'Cash on Delivery',
        };
    }
}
