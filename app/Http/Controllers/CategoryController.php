<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\ProductCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        $products = ProductCatalog::paginate($request, $category);
        $categories = Category::query()->active()->orderBy('name')->get();
        $filters = ProductCatalog::filtersFromRequest($request);

        return view('category.show', compact('category', 'products', 'categories', 'filters'));
    }
}
