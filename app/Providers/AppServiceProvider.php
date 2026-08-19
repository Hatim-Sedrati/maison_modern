<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\Cart;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(Cart::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.partials.header', 'layouts.partials.footer', 'layouts.partials.mobile-menu'], function ($view): void {
            $view->with('navCategories', Category::query()->active()->orderBy('name')->get());
        });
    }
}
