<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'variants' => fn ($q) => $q->active(),
        ]);

        return view('product.show', compact('product'));
    }
}
