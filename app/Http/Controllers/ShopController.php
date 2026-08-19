<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\ProductCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = ProductCatalog::paginate($request);
        $categories = Category::query()->active()->orderBy('name')->get();
        $filters = ProductCatalog::filtersFromRequest($request);

        return view('shop.index', compact('products', 'categories', 'filters'));
    }
}
