<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_number',
    'customer_name',
    'phone',
    'email',
    'city',
    'address',
    'postal_code',
    'notes',
    'subtotal',
    'delivery_fee',
    'total',
    'status',
    'payment_method',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'MM-'.now()->format('Ymd').'-';

        $latest = static::query()
            ->where('order_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('order_number')
            ->value('order_number');

        $sequence = $latest ? ((int) substr($latest, -4)) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
