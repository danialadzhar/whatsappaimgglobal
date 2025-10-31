<?php

use App\Http\Controllers\FAQController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
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
Route::post('/customers/update-name', [CustomerController::class, 'updateNameByPhone'])->name('api.customers.update-name');
Route::post('/customers/update-services-mode', [CustomerController::class, 'updateServicesModeByPhone'])->name('api.customers.update-services-mode');
Route::post('/customers/check-services-mode', [CustomerController::class, 'checkServicesModeIsNull'])->name('api.customers.check-services-mode');

// Message Logs API Routes (without CSRF protection for n8n integration)
Route::post('/message-logs', [CustomerController::class, 'storeMessageLog'])->name('api.message-logs.store');
Route::get('/message-logs', [CustomerController::class, 'getMessageLogs'])->name('api.message-logs.index');

// Chat API Routes (without CSRF protection)
Route::get('/chat/messages/{customerId}', [ChatController::class, 'getMessages'])->name('api.chat.messages');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('api.chat.send')->withoutMiddleware(['web']);

// Settings API Routes (without CSRF protection)
Route::post('/settings/chatbot-toggle', [SettingsController::class, 'toggleChatbot'])->name('api.settings.chatbot-toggle')->withoutMiddleware(['web']);
Route::get('/settings/chatbot-status', [SettingsController::class, 'getChatbotStatus'])->name('api.settings.chatbot-status');
Route::post('/settings/ecommerce', [SettingsController::class, 'saveEcommerceSettings'])->name('api.settings.ecommerce.save')->withoutMiddleware(['web']);
Route::get('/settings/ecommerce', [SettingsController::class, 'getEcommerceSettings'])->name('api.settings.ecommerce.get');

// Ecommerce API Routes (without CSRF protection)
Route::get('/ecommerce/products', [EcommerceController::class, 'apiIndex'])->name('api.ecommerce.products');
Route::get('/ecommerce/products/{id}', [EcommerceController::class, 'apiShow'])->name('api.ecommerce.products.show');

// Order API Routes (without CSRF protection)
Route::post('/orders', [OrderController::class, 'apiStore'])->name('api.orders.store');
Route::get('/orders/{order_number}', [OrderController::class, 'apiGetByOrderNumber'])->name('api.orders.get');
Route::get('/orders/track/{order_number}', [OrderController::class, 'apiTrackSubmit'])->name('api.orders.track')->middleware('throttle:5,1');

// Billplz Payment Routes (without CSRF protection)
Route::post('/checkout/billplz', [CheckoutController::class, 'createDirectPayment'])->name('api.checkout.billplz');
Route::get('/payment/gateways', [CheckoutController::class, 'getPaymentGateways'])->name('api.payment.gateways');

// Billplz Webhook (public endpoint untuk Billplz server callback)
Route::post('/webhook/billplz', [WebhookController::class, 'handleBillplzCallback'])->name('api.webhook.billplz');
