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

            // Find order
            $order = Order::where('order_number', $webhookData['order_number'])
                ->orWhere('billplz_bill_id', $webhookData['bill_id'])
                ->first();

            if (!$order) {
                return redirect()->route('ecommerce.index')
                    ->with('error', 'Order tidak dijumpai');
            }

            // Update order if not yet updated (webhook might not arrive yet)
            if ($order->payment_status === 'pending' && $webhookData['paid']) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'paid_at' => $webhookData['paid_at'] ?? now(),
                ]);
            }

            // Redirect to success or failed page
            if ($webhookData['paid']) {
                return redirect()->route('ecommerce.order.success', $order->id)
                    ->with('success', 'Pembayaran berjaya!');
            } else {
                return redirect()->route('ecommerce.checkout', $order->orderItems->first()->product_id)
                    ->with('error', 'Pembayaran gagal. Sila cuba lagi.');
            }
        } catch (\Exception $e) {
            Log::error('Billplz Redirect Error: ' . $e->getMessage());

            return redirect()->route('ecommerce.index')
                ->with('error', 'Terjadi kesalahan. Sila cuba lagi.');
        }
    }
}
