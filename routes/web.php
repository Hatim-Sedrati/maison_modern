<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderConfirmationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/new-arrivals', fn () => redirect()->route('shop.index', ['sort' => 'newest']))->name('shop.new-arrivals');
Route::get('/shop/women', fn () => redirect()->route('shop.index', ['gender' => 'women']))->name('shop.women');
Route::get('/shop/men', fn () => redirect()->route('shop.index', ['gender' => 'men']))->name('shop.men');
Route::get('/shop/accessories', fn () => redirect()->route('shop.index', ['category' => 'accessories']))->name('shop.accessories');
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::view('/cart', 'cart.index')->name('cart.index');
Route::view('/checkout', 'checkout.index')->name('checkout.index');
Route::get('/order/{order}/confirmation', [OrderConfirmationController::class, 'show'])->name('order.confirmation');

Route::view('/contact', 'pages.contact')->name('pages.contact');
Route::view('/shipping', 'pages.shipping')->name('pages.shipping');
Route::view('/returns', 'pages.returns')->name('pages.returns');
Route::view('/faq', 'pages.faq')->name('pages.faq');
