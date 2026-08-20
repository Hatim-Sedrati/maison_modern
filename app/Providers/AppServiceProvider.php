<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\Cart;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer(['layouts.partials.header', 'layouts.partials.footer', 'layouts.partials.mobile-menu'], function ($view): void {
            try {
                $view->with('navCategories', Category::query()->active()->orderBy('name')->get());
            } catch (Throwable) {
                $view->with('navCategories', collect());
            }
        });
    }
}
