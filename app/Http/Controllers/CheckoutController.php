<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\BillplzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $billplz;

    public function __construct(BillplzService $billplz)
    {
        $this->billplz = $billplz;
    }

    /**
     * Create Direct Payment with Billplz
     * API endpoint yang akan dipanggil dari frontend
     */
    public function createDirectPayment(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'delivery_method' => 'required|in:cod,postage,walkin',
            'payment_method' => 'required|in:full,booking,walkin',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string|max:50',
            'bank_code' => 'nullable|string|max:50', // Bank code untuk direct payment
        ]);

        try {
            DB::beginTransaction();

            // Get product dan validate stock
            $product = Product::findOrFail($validated['product_id']);

            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock,
                    'errors' => [
                        'quantity' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
                    ]
                ], 400);
            }

            // Calculate pricing
            $subtotal = $product->price * $validated['quantity'];

            // Calculate discounts based on delivery and payment methods
            $deliveryDiscount = $this->calculateDeliveryDiscount($validated['delivery_method'], $subtotal);
            $paymentDiscount = $this->calculatePaymentDiscount($validated['payment_method'], $subtotal);
            $totalAmount = $subtotal - $deliveryDiscount - $paymentDiscount;

            // Create order with pending status
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'delivery_method' => $validated['delivery_method'],
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'delivery_discount' => $deliveryDiscount,
                'payment_discount' => $paymentDiscount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'idempotency_key' => Str::uuid()->toString(),
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'quantity' => $validated['quantity'],
                'color' => $validated['color'],
                'subtotal' => $subtotal,
            ]);

            // Create Billplz bill dengan Direct Payment Gateway
            $bill = $this->billplz->createDirectPaymentBill($order, $validated['bank_code'] ?? null);

            // Update order dengan Billplz bill information
            $order->update([
                'billplz_bill_id' => $bill['bill_id'],
                'billplz_collection_id' => $bill['collection_id'],
                'payment_metadata' => $bill['full_response'],
            ]);

            // Deduct stock (akan restore kalau payment failed)
            $product->decrement('stock', $validated['quantity']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'bill_url' => $bill['bill_url'],
                    'bill_id' => $bill['bill_id'],
                    'total_amount' => (float) $order->total_amount,
                ],
                'message' => 'Payment bill created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan semasa membuat order. Sila cuba lagi.',
                'errors' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get available payment gateways untuk frontend
     */
    public function getPaymentGateways()
    {
        try {
            $gateways = $this->billplz->getPaymentGateways();

            return response()->json([
                'success' => true,
                'data' => $gateways,
                'message' => 'Payment gateways retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment gateways',
                'errors' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Calculate delivery discount
     */
    private function calculateDeliveryDiscount($method, $subtotal)
    {
        $discounts = [
            'cod' => 0,
            'postage' => 5, // 5%
            'walkin' => 10, // 10%
        ];

        $percentage = $discounts[$method] ?? 0;
        return $subtotal * ($percentage / 100);
    }

    /**
     * Calculate payment discount
     */
    private function calculatePaymentDiscount($method, $subtotal)
    {
        $discounts = [
            'full' => 5, // 5%
            'booking' => 0,
            'walkin' => 3, // 3%
        ];

        $percentage = $discounts[$method] ?? 0;
        return $subtotal * ($percentage / 100);
    }
}
