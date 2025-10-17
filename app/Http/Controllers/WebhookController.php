<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\BillplzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $billplz;

    public function __construct(BillplzService $billplz)
    {
        $this->billplz = $billplz;
    }

    /**
     * Handle Billplz webhook callback
     * Endpoint ini akan dipanggil oleh Billplz server
     */
    public function handleBillplzCallback(Request $request)
    {
        try {
            // Log incoming webhook untuk debugging
            Log::info('Billplz Webhook Received', $request->all());

            // Verify X-Signature jika ada
            $xSignature = $request->header('X-Signature');
            if ($xSignature && config('services.billplz.x_signature_key')) {
                if (!$this->billplz->verifyWebhookSignature($request->all(), $xSignature)) {
                    Log::warning('Billplz Webhook Invalid Signature', [
                        'signature' => $xSignature,
                        'data' => $request->all(),
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid signature'
                    ], 400);
                }
            }

            // Parse webhook data
            $webhookData = $this->billplz->parseWebhookData($request->all());

            // Find order by order_number (reference_2) or bill_id
            $order = Order::where('order_number', $webhookData['order_number'])
                ->orWhere('billplz_bill_id', $webhookData['bill_id'])
                ->first();

            if (!$order) {
                Log::error('Billplz Webhook: Order not found', [
                    'order_number' => $webhookData['order_number'],
                    'bill_id' => $webhookData['bill_id'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Idempotency check - jangan update kalau dah paid
            if ($order->payment_status === 'paid' && $webhookData['paid']) {
                Log::info('Billplz Webhook: Order already paid (idempotent)', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order already processed'
                ]);
            }

            // Update order status based on payment
            if ($webhookData['paid']) {
                // Payment successful
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing', // Update order status from pending to processing
                    'paid_at' => $webhookData['paid_at'] ?? now(),
                    'payment_metadata' => array_merge(
                        $order->payment_metadata ?? [],
                        [
                            'webhook_data' => $webhookData,
                            'paid_at' => $webhookData['paid_at'],
                            'transaction_id' => $webhookData['transaction_id'],
                        ]
                    ),
                ]);

                Log::info('Billplz Webhook: Payment successful', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'amount' => $webhookData['amount'],
                ]);

                // TODO: Dispatch events untuk send email, notifications, etc
                // event(new OrderPaid($order));

            } else {
                // Payment failed
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                    'payment_metadata' => array_merge(
                        $order->payment_metadata ?? [],
                        [
                            'webhook_data' => $webhookData,
                            'failed_at' => now(),
                        ]
                    ),
                ]);

                // Restore stock jika payment failed
                $orderItems = $order->orderItems;
                foreach ($orderItems as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }

                Log::warning('Billplz Webhook: Payment failed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'state' => $webhookData['state'],
                ]);

                // TODO: Dispatch events untuk send email notification
                // event(new OrderPaymentFailed($order));
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Billplz Webhook Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle redirect after payment (optional)
     * Customer akan di-redirect ke sini selepas payment
     */
    public function handleBillplzRedirect(Request $request)
    {
        try {
            // Parse redirect data (sama format dengan webhook)
            $webhookData = $this->billplz->parseWebhookData($request->all());

            // Debug logging untuk troubleshoot
            Log::info('Billplz Redirect Data', [
                'raw_request' => $request->all(),
                'parsed_data' => $webhookData,
                'order_number' => $webhookData['order_number'],
                'bill_id' => $webhookData['bill_id'],
            ]);

            // Find order
            $order = Order::where('order_number', $webhookData['order_number'])
                ->orWhere('billplz_bill_id', $webhookData['bill_id'])
                ->first();

            // Fallback: try to find by order_id from URL parameters
            if (!$order && $request->has('order_id')) {
                $order = Order::find($request->get('order_id'));
                Log::info('Found order by order_id fallback', [
                    'order_id' => $request->get('order_id'),
                    'order_found' => $order ? $order->order_number : 'not found'
                ]);
            }

            if (!$order) {
                // Redirect to frontend error page
                $frontendUrl = config('services.billplz.frontend_success_url');
                $errorUrl = str_replace('/shop/order/success', '/shop/order/error', $frontendUrl);
                return redirect($errorUrl . '?type=order_not_found');
            }

            // Update order if not yet updated (webhook might not arrive yet)
            if ($order->payment_status === 'pending' && $webhookData['paid']) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'paid_at' => $webhookData['paid_at'] ?? now(),
                    'payment_metadata' => array_merge(
                        $order->payment_metadata ?? [],
                        [
                            'redirect_data' => $webhookData,
                            'paid_at' => $webhookData['paid_at'],
                            'transaction_id' => $webhookData['transaction_id'],
                        ]
                    ),
                ]);

                Log::info('Billplz Redirect: Payment successful', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }

            // Redirect to Next.js frontend success or failed page
            $frontendUrl = config('services.billplz.frontend_success_url');

            if ($webhookData['paid']) {
                return redirect($frontendUrl . '/' . $order->order_number . '?payment=success');
            } else {
                return redirect($frontendUrl . '/' . $order->order_number . '?payment=failed');
            }
        } catch (\Exception $e) {
            Log::error('Billplz Redirect Error: ' . $e->getMessage());

            $frontendUrl = config('services.billplz.frontend_success_url', 'http://localhost:3000/shop/order/success');
            $errorUrl = str_replace('/shop/order/success', '/shop/order/error', $frontendUrl);
            return redirect($errorUrl . '?type=system_error');
        }
    }
}
