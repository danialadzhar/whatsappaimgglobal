<script setup>
import { ref } from 'vue';
import EcommerceLayout from '@/Layouts/EcommerceLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    trackingUrl: {
        type: String,
        required: true
    }
});

// Format price
const formatPrice = (price) => {
    return `RM${price.toFixed(2)}`;
};

// Get delivery method display name
const getDeliveryMethodName = (method) => {
    const methods = {
        'cod': 'COD (Cash on Delivery)',
        'postage': 'Postage',
        'walkin': 'Walk In'
    };
    return methods[method] || method;
};

// Get payment method display name
const getPaymentMethodName = (method) => {
    const methods = {
        'full': 'Full Payment',
        'booking': 'Booking / Pre-Order',
        'walkin': 'Walk In'
    };
    return methods[method] || method;
};

// Get status display
const getStatusDisplay = (status) => {
    const statuses = {
        'pending': { text: 'Pending', color: 'yellow' },
        'processing': { text: 'Processing', color: 'blue' },
        'completed': { text: 'Completed', color: 'green' },
        'cancelled': { text: 'Cancelled', color: 'red' }
    };
    return statuses[status] || { text: status, color: 'gray' };
};

const statusDisplay = getStatusDisplay(props.order.status);
</script>

<template>

    <Head :title="`Order ${order.order_number}`" />

    <EcommerceLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Success Header -->
                <div class="text-center mb-8">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Success!</h1>
                    <p class="text-gray-600">Thank you for your purchase. Your order has been received.</p>
                </div>

                <!-- Order Summary Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">Order Details</h2>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium',
                                    statusDisplay.color === 'yellow' ? 'bg-yellow-100 text-yellow-800' :
                                        statusDisplay.color === 'blue' ? 'bg-blue-100 text-blue-800' :
                                            statusDisplay.color === 'green' ? 'bg-green-100 text-green-800' :
                                                statusDisplay.color === 'red' ? 'bg-red-100 text-red-800' :
                                                    'bg-gray-100 text-gray-800'
                                ]">
                                    {{ statusDisplay.text }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Order Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Order Number:</span>
                                        <span class="font-medium">{{ order.order_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Order Date:</span>
                                        <span class="font-medium">{{ order.created_at }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Amount:</span>
                                        <span class="font-bold text-lg">{{ formatPrice(order.total_amount) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="font-medium">{{ order.customer_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium">{{ order.customer_phone }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium">{{ order.customer_email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery & Payment Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Method</h3>
                                <p class="text-gray-600">{{ getDeliveryMethodName(order.delivery_method) }}</p>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                                <p class="text-gray-600">{{ getPaymentMethodName(order.payment_method) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Order Items</h2>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="item in order.order_items" :key="item.id"
                                class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                                <img :src="item.product_image" :alt="item.product_name"
                                    class="w-16 h-16 object-cover rounded-lg"
                                    @error="$event.target.src = 'https://placehold.co/64x64'">

                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ item.product_name }}</h3>
                                    <p class="text-sm text-gray-600">Quantity: {{ item.quantity }}</p>
                                    <p v-if="item.color" class="text-sm text-gray-600">Color: {{ item.color }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="font-medium text-gray-900">{{ formatPrice(item.product_price) }}</p>
                                    <p class="text-sm text-gray-600">each</p>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold text-gray-900">{{ formatPrice(item.subtotal) }}</p>
                                    <p class="text-sm text-gray-600">subtotal</p>
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="space-y-2">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span>{{ formatPrice(order.subtotal) }}</span>
                                </div>

                                <div v-if="order.delivery_discount > 0" class="flex justify-between text-green-600">
                                    <span>Delivery Discount</span>
                                    <span>-{{ formatPrice(order.delivery_discount) }}</span>
                                </div>

                                <div v-if="order.payment_discount > 0" class="flex justify-between text-green-600">
                                    <span>Payment Discount</span>
                                    <span>-{{ formatPrice(order.payment_discount) }}</span>
                                </div>

                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ formatPrice(order.total_amount)
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a :href="trackingUrl"
                        class="px-8 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors duration-200 text-center">
                        Track Order
                    </a>

                    <Link :href="route('ecommerce.index')"
                        class="px-8 py-3 border-2 border-gray-900 text-gray-900 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200 text-center">
                    Continue Shopping
                    </Link>
                </div>

                <!-- Additional Info -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        We will send a confirmation email to {{ order.customer_email }} in the near future.
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        If you have any questions, please contact us.
                    </p>
                    <p class="text-sm text-gray-500 mt-3">
                        Track your order anytime:
                        <Link :href="route('ecommerce.order.track.form')"
                            class="text-blue-600 hover:text-blue-700 font-medium underline">
                        Order Tracking Page
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </EcommerceLayout>
</template>
