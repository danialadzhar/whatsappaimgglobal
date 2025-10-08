<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    order: {
        type: Object,
        required: true
    }
});

// Status update form
const statusForm = ref({
    status: props.order.status,
    notes: props.order.notes || ''
});

const isUpdating = ref(false);

// Status options
const statusOptions = [
    { value: 'pending', label: 'Pending', color: 'yellow' },
    { value: 'processing', label: 'Processing', color: 'blue' },
    { value: 'completed', label: 'Completed', color: 'green' },
    { value: 'cancelled', label: 'Cancelled', color: 'red' }
];

// Format price
const formatPrice = (price) => {
    return `RM${price.toFixed(2)}`;
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

// Update order status
const updateStatus = () => {
    isUpdating.value = true;

    router.patch(route('orders.updateStatus', props.order.id), {
        status: statusForm.value.status,
        notes: statusForm.value.notes
    }, {
        onFinish: () => {
            isUpdating.value = false;
        }
    });
};

const statusDisplay = getStatusDisplay(props.order.status);
</script>

<template>

    <Head :title="`Order ${order.order_number}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
                            <p class="text-gray-600 mt-2">Order #{{ order.order_number }}</p>
                        </div>
                        <Link :href="route('orders.index')"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        ← Back to Orders
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Order Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Order Status Card -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Status</h2>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
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

                                <div class="text-sm text-gray-600">
                                    Created: {{ order.created_at }}
                                </div>
                            </div>

                            <!-- Status Update Form -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>

                                <form @submit.prevent="updateStatus" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                        <select v-model="statusForm.status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option v-for="option in statusOptions" :key="option.value"
                                                :value="option.value">
                                                {{ option.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                        <textarea v-model="statusForm.notes" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Add notes about this order..."></textarea>
                                    </div>

                                    <button type="submit" :disabled="isUpdating"
                                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        {{ isUpdating ? 'Updating...' : 'Update Status' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Customer Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Contact Details</h3>
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

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Details</h3>
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
                                            <span class="text-gray-600">Last Updated:</span>
                                            <span class="font-medium">{{ order.updated_at }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery & Payment Information -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Delivery & Payment</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Delivery Method</h3>
                                    <p class="text-gray-600">{{ getDeliveryMethodName(order.delivery_method) }}</p>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Method</h3>
                                    <p class="text-gray-600">{{ getPaymentMethodName(order.payment_method) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Items</h2>

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

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-20">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 mb-6">
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
                            </div>

                            <!-- Total -->
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ formatPrice(order.total_amount)
                                    }}</span>
                                </div>
                                <div v-if="(order.delivery_discount + order.payment_discount) > 0"
                                    class="text-sm text-green-600 text-right">
                                    Customer saved {{ formatPrice(order.delivery_discount + order.payment_discount) }}!
                                </div>
                            </div>

                            <!-- Order Notes -->
                            <div v-if="order.notes" class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">Order Notes</h3>
                                <p class="text-sm text-gray-600">{{ order.notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
