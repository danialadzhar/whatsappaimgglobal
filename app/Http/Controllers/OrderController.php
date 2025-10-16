<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * API endpoint untuk membuat order baru
     */
    public function apiStore(Request $request)
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

            // Create order
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

            // Deduct stock
            $product->decrement('stock', $validated['quantity']);

            DB::commit();

            // Prepare order data for response
            $orderData = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'delivery_method' => $order->delivery_method,
                'payment_method' => $order->payment_method,
                'subtotal' => (float) $order->subtotal,
                'delivery_discount' => (float) $order->delivery_discount,
                'payment_discount' => (float) $order->payment_discount,
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $orderData,
                ],
                'message' => 'Order berjaya dibuat!'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan semasa membuat order. Sila cuba lagi.',
                'errors' => [
                    'error' => 'Terjadi kesalahan semasa membuat order. Sila cuba lagi.'
                ]
            ], 500);
        }
    }

    /**
     * Store new order (customer checkout)
     */
    public function store(Request $request)
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
        ]);

        try {
            DB::beginTransaction();

            // Get product dan validate stock
            $product = Product::findOrFail($validated['product_id']);

            if ($product->stock < $validated['quantity']) {
                return back()->withErrors([
                    'quantity' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
                ]);
            }

            // Calculate pricing
            $subtotal = $product->price * $validated['quantity'];

            // Calculate discounts based on delivery and payment methods
            $deliveryDiscount = $this->calculateDeliveryDiscount($validated['delivery_method'], $subtotal);
            $paymentDiscount = $this->calculatePaymentDiscount($validated['payment_method'], $subtotal);
            $totalAmount = $subtotal - $deliveryDiscount - $paymentDiscount;

            // Create order
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

            // Deduct stock
            $product->decrement('stock', $validated['quantity']);

            DB::commit();

            // Redirect to success page
            return redirect()->route('ecommerce.order.success', $order->id)
                ->with('success', 'Order berjaya dibuat!');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Terjadi kesalahan semasa membuat order. Sila cuba lagi.'
            ]);
        }
    }

    /**
     * Show order success page
     */
    public function success($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);

        $orderData = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'delivery_method' => $order->delivery_method,
            'payment_method' => $order->payment_method,
            'subtotal' => (float) $order->subtotal,
            'delivery_discount' => (float) $order->delivery_discount,
            'payment_discount' => (float) $order->payment_discount,
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'order_items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'product_price' => (float) $item->product_price,
                    'quantity' => $item->quantity,
                    'color' => $item->color,
                    'subtotal' => (float) $item->subtotal,
                    'product_image' => $item->product->image_url ?? 'https://placehold.co/300x300',
                ];
            }),
        ];

        // Generate signed tracking URL
        $trackingUrl = URL::temporarySignedRoute(
            'ecommerce.order.show',
            now()->addMinutes(30),
            ['orderNumber' => $order->order_number]
        );

        return Inertia::render('Ecommerce/OrderSuccess', [
            'order' => $orderData,
            'trackingUrl' => $trackingUrl,
        ]);
    }

    /**
     * Show order tracking form
     */
    public function trackForm()
    {
        return Inertia::render('Ecommerce/OrderTrackingForm');
    }

    /**
     * API endpoint untuk submit order tracking form
     */
    public function apiTrackSubmit(Request $request, $order_number)
    {
        $data = $request->validate([
            'customer_email' => 'required|email',
        ]);

        try {
            // Find order by both order number and email for security
            $order = Order::with('orderItems.product')->where('order_number', $order_number)
                ->where('customer_email', $data['customer_email'])
                ->firstOrFail();

            // Generate temporary signed URL (valid for 30 minutes)
            $trackingUrl = URL::temporarySignedRoute(
                'ecommerce.order.show',
                now()->addMinutes(30),
                ['orderNumber' => $order->order_number]
            );

            // Prepare order data for response
            $orderData = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'delivery_method' => $order->delivery_method,
                'payment_method' => $order->payment_method,
                'subtotal' => (float) $order->subtotal,
                'delivery_discount' => (float) $order->delivery_discount,
                'payment_discount' => (float) $order->payment_discount,
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'updated_at' => $order->updated_at->format('d/m/Y H:i'),
                'order_items' => $order->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'product_price' => (float) $item->product_price,
                        'quantity' => $item->quantity,
                        'color' => $item->color,
                        'subtotal' => (float) $item->subtotal,
                        'product_image' => $item->product->image_url ?? 'https://placehold.co/300x300',
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $orderData,
                    'tracking_url' => $trackingUrl,
                ],
                'message' => 'Order ditemui!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak dijumpai. Sila pastikan nombor order dan email adalah betul.',
                'errors' => [
                    'order_number' => 'Order tidak dijumpai dengan maklumat yang diberikan'
                ]
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan semasa mencari order. Sila cuba lagi.',
                'errors' => [
                    'error' => 'Terjadi kesalahan semasa mencari order. Sila cuba lagi.'
                ]
            ], 500);
        }
    }

    /**
     * Submit order tracking form and redirect to signed URL
     */
    public function trackSubmit(Request $request)
    {
        $data = $request->validate([
            'order_number' => 'required|string',
            'customer_email' => 'required|email',
        ]);

        // Find order by both order number and email for security
        $order = Order::where('order_number', $data['order_number'])
            ->where('customer_email', $data['customer_email'])
            ->firstOrFail();

        // Generate temporary signed URL (valid for 30 minutes)
        $url = URL::temporarySignedRoute(
            'ecommerce.order.show',
            now()->addMinutes(30),
            ['orderNumber' => $order->order_number]
        );

        return redirect($url);
    }

    /**
     * Show order tracking page (requires signed URL)
     */
    public function show($orderNumber)
    {
        $order = Order::with('orderItems.product')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $orderData = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'delivery_method' => $order->delivery_method,
            'payment_method' => $order->payment_method,
            'subtotal' => (float) $order->subtotal,
            'delivery_discount' => (float) $order->delivery_discount,
            'payment_discount' => (float) $order->payment_discount,
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'updated_at' => $order->updated_at->format('d/m/Y H:i'),
            'order_items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'product_price' => (float) $item->product_price,
                    'quantity' => $item->quantity,
                    'color' => $item->color,
                    'subtotal' => (float) $item->subtotal,
                    'product_image' => $item->product->image_url ?? 'https://placehold.co/300x300',
                ];
            }),
        ];

        return Inertia::render('Ecommerce/OrderTracking', [
            'order' => $orderData,
        ]);
    }

    /**
     * Admin: List all orders
     */
    public function index(Request $request)
    {
        $query = Order::with('orderItems');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        $ordersData = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'items_count' => $order->orderItems->count(),
            ];
        });

        return Inertia::render('Orders/Index', [
            'orders' => $ordersData,
            'filters' => $request->only(['status', 'search', 'date_from', 'date_to']),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Admin: Show order detail
     */
    public function detail($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);

        $orderData = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,
            'delivery_method' => $order->delivery_method,
            'payment_method' => $order->payment_method,
            'subtotal' => (float) $order->subtotal,
            'delivery_discount' => (float) $order->delivery_discount,
            'payment_discount' => (float) $order->payment_discount,
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'notes' => $order->notes,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'updated_at' => $order->updated_at->format('d/m/Y H:i'),
            'order_items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'product_price' => (float) $item->product_price,
                    'quantity' => $item->quantity,
                    'color' => $item->color,
                    'subtotal' => (float) $item->subtotal,
                    'product_image' => $item->product->image_url ?? 'https://placehold.co/300x300',
                ];
            }),
        ];

        return Inertia::render('Orders/Detail', [
            'order' => $orderData,
        ]);
    }

    /**
     * Admin: Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $order->notes,
        ]);

        return back()->with('success', 'Status order berjaya dikemaskini.');
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
