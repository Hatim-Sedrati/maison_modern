<?php

namespace App\Http\Controllers;

use App\Enums\ProductGender;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $catalog = fn () => Product::query()
            ->active()
            ->with(['category', 'images', 'variants' => fn ($q) => $q->active()]);

        $newArrivals = $catalog()->latest()->limit(8)->get();

        $featured = $catalog()->featured()->latest()->limit(8)->get();

        $women = $catalog()->gender(ProductGender::Women)->latest()->limit(4)->get();

        $men = $catalog()->gender(ProductGender::Men)->latest()->limit(4)->get();

        $accessoriesCategory = Category::query()
            ->active()
            ->where('slug', 'accessories')
            ->first();

        $accessories = $accessoriesCategory
            ? $catalog()->where('category_id', $accessoriesCategory->id)->latest()->limit(4)->get()
            : collect();

        $heroProduct = Product::query()
            ->active()
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->whereHas('images')
            ->orderByDesc('is_featured')
            ->latest()
            ->first();

        return view('home.index', compact(
            'newArrivals',
            'featured',
            'women',
            'men',
            'accessories',
            'accessoriesCategory',
            'heroProduct',
        ));
    }
}
