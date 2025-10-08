<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products to create orders for
        $products = Product::take(5)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        // Create sample orders based on available products
        $orders = [
            [
                'customer_name' => 'Ahmad Rahman',
                'customer_phone' => '012-3456789',
                'customer_email' => 'ahmad@example.com',
                'delivery_method' => 'postage',
                'payment_method' => 'full',
                'status' => 'completed',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 2, 'color' => 'black'],
                ]
            ],
            [
                'customer_name' => 'Siti Aminah',
                'customer_phone' => '013-9876543',
                'customer_email' => 'siti@example.com',
                'delivery_method' => 'walkin',
                'payment_method' => 'booking',
                'status' => 'processing',
                'items' => [
                    ['product_id' => $products[1]->id, 'quantity' => 1, 'color' => 'blue'],
                ]
            ],
            [
                'customer_name' => 'Muhammad Ali',
                'customer_phone' => '014-5555555',
                'customer_email' => 'ali@example.com',
                'delivery_method' => 'cod',
                'payment_method' => 'walkin',
                'status' => 'pending',
                'items' => [
                    ['product_id' => $products[2]->id, 'quantity' => 3, 'color' => 'red'],
                ]
            ],
            [
                'customer_name' => 'Fatimah Zahra',
                'customer_phone' => '015-7777777',
                'customer_email' => 'fatimah@example.com',
                'delivery_method' => 'postage',
                'payment_method' => 'full',
                'status' => 'completed',
                'items' => [
                    ['product_id' => $products[3]->id, 'quantity' => 1, 'color' => 'white'],
                ]
            ],
        ];

        // Add more orders using available products if we have more than 4
        if ($products->count() >= 4) {
            $orders[] = [
                'customer_name' => 'Hassan Abdullah',
                'customer_phone' => '016-9999999',
                'customer_email' => 'hassan@example.com',
                'delivery_method' => 'walkin',
                'payment_method' => 'full',
                'status' => 'cancelled',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 2, 'color' => 'green'],
                ]
            ];
        }

        foreach ($orders as $orderData) {
            // Calculate pricing
            $subtotal = 0;
            $orderItems = [];

            foreach ($orderData['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $itemSubtotal = $product->price * $itemData['quantity'];
                    $subtotal += $itemSubtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_price' => $product->price,
                        'quantity' => $itemData['quantity'],
                        'color' => $itemData['color'],
                        'subtotal' => $itemSubtotal,
                    ];
                }
            }

            // Calculate discounts
            $deliveryDiscount = $this->calculateDeliveryDiscount($orderData['delivery_method'], $subtotal);
            $paymentDiscount = $this->calculatePaymentDiscount($orderData['payment_method'], $subtotal);
            $totalAmount = $subtotal - $deliveryDiscount - $paymentDiscount;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $orderData['customer_name'],
                'customer_phone' => $orderData['customer_phone'],
                'customer_email' => $orderData['customer_email'],
                'delivery_method' => $orderData['delivery_method'],
                'payment_method' => $orderData['payment_method'],
                'subtotal' => $subtotal,
                'delivery_discount' => $deliveryDiscount,
                'payment_discount' => $paymentDiscount,
                'total_amount' => $totalAmount,
                'status' => $orderData['status'],
                'created_at' => now()->subDays(rand(1, 30)), // Random date within last 30 days
            ]);

            // Create order items
            foreach ($orderItems as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'product_name' => $itemData['product_name'],
                    'product_price' => $itemData['product_price'],
                    'quantity' => $itemData['quantity'],
                    'color' => $itemData['color'],
                    'subtotal' => $itemData['subtotal'],
                ]);
            }

            // Deduct stock for completed/processing orders
            if (in_array($orderData['status'], ['completed', 'processing'])) {
                foreach ($orderItems as $itemData) {
                    $product = Product::find($itemData['product_id']);
                    if ($product) {
                        $product->decrement('stock', $itemData['quantity']);
                    }
                }
            }
        }

        $this->command->info('Created ' . count($orders) . ' sample orders.');
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
