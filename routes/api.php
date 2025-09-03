<?php

use App\Http\Controllers\FAQController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// FAQ API Routes (without CSRF protection)
Route::get('/faq/db', [FAQController::class, 'getFAQDataFromDB']);
Route::post('/faq', [FAQController::class, 'store'])->withoutMiddleware(['web']);
