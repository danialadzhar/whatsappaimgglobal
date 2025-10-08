<script setup>
import { ref } from 'vue';
import EcommerceLayout from '@/Layouts/EcommerceLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    order: {
        type: Object,
        required: true
    }
});

// Search form
const searchForm = ref({
    order_number: ''
});

const isSearching = ref(false);

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
        'pending': { text: 'Pending', color: 'yellow', description: 'Order sedang diproses' },
        'processing': { text: 'Processing', color: 'blue', description: 'Order sedang disediakan' },
        'completed': { text: 'Completed', color: 'green', description: 'Order telah selesai' },
        'cancelled': { text: 'Cancelled', color: 'red', description: 'Order telah dibatalkan' }
    };
    return statuses[status] || { text: status, color: 'gray', description: 'Status tidak diketahui' };
};

// Search for order
const searchOrder = () => {
    if (!searchForm.value.order_number.trim()) {
        return;
    }

    isSearching.value = true;
    router.visit(route('ecommerce.order.show', searchForm.value.order_number), {
        onFinish: () => {
            isSearching.value = false;
        }
    });
};

const statusDisplay = getStatusDisplay(props.order.status);
</script>

<template>

    <Head :title="`Track Order ${order.order_number}`" />

    <EcommerceLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Order</h1>
                    <p class="text-gray-600">Monitor status order anda di sini</p>
                </div>

                <!-- Search Form -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Search Order</h2>
                    <div class="flex gap-4">
                        <input v-model="searchForm.order_number" type="text"
                            placeholder="Enter order number (e.g., ORD-20251007-001)"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <button @click="searchOrder" :disabled="isSearching"
                            class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isSearching ? 'Searching...' : 'Search' }}
                        </button>
                    </div>
                </div>

                <!-- Order Status Timeline -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Order Status</h2>
                    </div>

                    <div class="p-6">
                        <!-- Current Status -->
                        <div class="text-center mb-8">
                            <div :class="[
                                'mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4',
                                statusDisplay.color === 'yellow' ? 'bg-yellow-100' :
                                    statusDisplay.color === 'blue' ? 'bg-blue-100' :
                                        statusDisplay.color === 'green' ? 'bg-green-100' :
                                            statusDisplay.color === 'red' ? 'bg-red-100' :
                                                'bg-gray-100'
                            ]">
                                <svg v-if="statusDisplay.color === 'green'" class="h-8 w-8 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <svg v-else-if="statusDisplay.color === 'red'" class="h-8 w-8 text-red-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <svg v-else class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 :class="[
                                'text-2xl font-bold mb-2',
                                statusDisplay.color === 'yellow' ? 'text-yellow-800' :
                                    statusDisplay.color === 'blue' ? 'text-blue-800' :
                                        statusDisplay.color === 'green' ? 'text-green-800' :
                                            statusDisplay.color === 'red' ? 'text-red-800' :
                                                'text-gray-800'
                            ]">
                                {{ statusDisplay.text }}
                            </h3>
                            <p class="text-gray-600">{{ statusDisplay.description }}</p>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Order Created</p>
                                    <p class="text-sm text-gray-600">{{ order.created_at }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div :class="[
                                        'w-4 h-4 rounded-full',
                                        ['processing', 'completed'].includes(order.status) ? 'bg-blue-500' : 'bg-gray-300'
                                    ]"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Processing</p>
                                    <p class="text-sm text-gray-600">
                                        {{ order.status === 'processing' ? 'Currently processing' :
                                            order.status === 'completed' ? 'Completed' : 'Pending' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div :class="[
                                        'w-4 h-4 rounded-full',
                                        order.status === 'completed' ? 'bg-green-500' : 'bg-gray-300'
                                    ]"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Completed</p>
                                    <p class="text-sm text-gray-600">
                                        {{ order.status === 'completed' ? 'Order completed' : 'Pending completion' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Order Details</h2>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link :href="route('ecommerce.index')"
                        class="px-8 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors duration-200 text-center">
                    Continue Shopping
                    </Link>
                </div>
            </div>
        </div>
    </EcommerceLayout>
</template>
