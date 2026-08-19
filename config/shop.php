<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cash on Delivery fee
    |--------------------------------------------------------------------------
    |
    | Flat delivery fee applied server-side when an order is created.
    | Stored as a decimal string (MAD). Never trust a browser-submitted fee.
    |
    */

    'delivery_fee' => env('SHOP_DELIVERY_FEE', '0.00'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp click-to-chat
    |--------------------------------------------------------------------------
    |
    | International number with country code, no plus sign required.
    | Example: 212612345678
    |
    */

    'whatsapp_number' => env('WHATSAPP_NUMBER'),

];
