<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EcommerceController;
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

// Ecommerce Routes (Public - boleh access tanpa login)
Route::get('/ecommerce', [EcommerceController::class, 'index'])->name('ecommerce.index');
Route::get('/ecommerce/{id}', [EcommerceController::class, 'show'])->name('ecommerce.show');

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
});

// API Routes moved to routes/api.php for consistency

require __DIR__ . '/auth.php';
