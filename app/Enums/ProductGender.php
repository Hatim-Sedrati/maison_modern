<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductGender: string implements HasLabel
{
    case Men = 'men';
    case Women = 'women';
    case Unisex = 'unisex';

    public function getLabel(): string
    {
        return match ($this) {
            self::Men => 'Men',
            self::Women => 'Women',
            self::Unisex => 'Unisex',
        };
    }
}
