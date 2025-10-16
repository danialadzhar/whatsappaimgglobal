<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => '8.2+',
    ]);
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Ecommerce Routes (Public - with rate limiting for security)
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/ecommerce', [EcommerceController::class, 'index'])->name('ecommerce.index');
    Route::get('/ecommerce/{id}', [EcommerceController::class, 'show'])->name('ecommerce.show');
    Route::get('/ecommerce/checkout/{id}', [EcommerceController::class, 'checkout'])->name('ecommerce.checkout');

    // Order tracking form + submit
    Route::get('/ecommerce/order/track', [OrderController::class, 'trackForm'])->name('ecommerce.order.track.form');
    Route::post('/ecommerce/order/track', [OrderController::class, 'trackSubmit'])
        ->middleware('throttle:5,1')
        ->name('ecommerce.order.track.submit');

    // Order detail via signed link only (protected from IDOR)
    Route::get('/ecommerce/order/{orderNumber}', [OrderController::class, 'show'])
        ->middleware('signed')
        ->name('ecommerce.order.show');
});

// Order Routes (Public)
Route::post('/ecommerce/order', [OrderController::class, 'store'])->name('ecommerce.order.store');
Route::get('/ecommerce/order/success/{id}', [OrderController::class, 'success'])->name('ecommerce.order.success');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // FAQ Routes
    Route::get('/faq', [FAQController::class, 'index'])->name('faq');

    // Customer Routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    // Chat Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');

    // Settings Routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

    // E-Commerce Settings Routes
    Route::get('/ecommerce/settings', [SettingsController::class, 'ecommerceSettings'])->name('ecommerce.settings.index');

    // Product Management Routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete'])->name('products.forceDelete');

    // Category Management Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Order Management Routes (Admin)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'detail'])->name('orders.detail');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// API Routes moved to routes/api.php for consistency

require __DIR__ . '/auth.php';
