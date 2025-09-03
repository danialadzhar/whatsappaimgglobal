<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FAQController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // FAQ Routes
    Route::get('/faq', [FAQController::class, 'index'])->name('faq');
    Route::get('/api/faq', [FAQController::class, 'getFAQData'])->name('api.faq');
    Route::get('/api/faq/db', [FAQController::class, 'getFAQDataFromDB'])->name('api.faq.db');
    Route::post('/api/faq', [FAQController::class, 'store'])->name('api.faq.store');
});

require __DIR__ . '/auth.php';
