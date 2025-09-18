<?php

use App\Http\Controllers\FAQController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ChatController;
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
Route::get('/faq/db', [FAQController::class, 'getFAQDataFromDB'])->name('api.faq.db');
Route::get('/faq/all', [FAQController::class, 'getAllFAQData'])->name('api.faq.all');
Route::post('/faq', [FAQController::class, 'store'])->name('api.faq.store')->withoutMiddleware(['web']);
Route::put('/faq/{id}', [FAQController::class, 'update'])->name('api.faq.update')->withoutMiddleware(['web']);
Route::delete('/faq/{id}', [FAQController::class, 'destroy'])->name('api.faq.destroy')->withoutMiddleware(['web']);

// Customer API Routes (without CSRF protection for n8n integration)
Route::post('/customers', [CustomerController::class, 'store'])->name('api.customers.store');
Route::get('/customers/db/{phone_number}', [CustomerController::class, 'getCustomerDataFromDB'])->name('api.customers.db');
Route::get('/customers/phone', [CustomerController::class, 'getCustomerByPhone'])->name('api.customers.phone');

// Message Logs API Routes (without CSRF protection for n8n integration)
Route::post('/message-logs', [CustomerController::class, 'storeMessageLog'])->name('api.message-logs.store');
Route::get('/message-logs', [CustomerController::class, 'getMessageLogs'])->name('api.message-logs.index');

// Chat API Routes (without CSRF protection)
Route::get('/chat/messages/{customerId}', [ChatController::class, 'getMessages'])->name('api.chat.messages');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('api.chat.send')->withoutMiddleware(['web']);
