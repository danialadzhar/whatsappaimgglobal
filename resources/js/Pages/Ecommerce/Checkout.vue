<script setup>
import { ref, computed } from 'vue';
import EcommerceLayout from '@/Layouts/EcommerceLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    quantity: {
        type: Number,
        default: 1
    },
    color: {
        type: String,
        default: null
    }
});

// Form data
const form = ref({
    name: '',
    phone: '',
    email: '',
    delivery_method: '',
    payment_method: ''
});

// Delivery methods with discounts
const deliveryMethods = [
    {
        id: 'cod',
        name: 'COD (Cash on Delivery)',
        discount: 0,
        description: 'Pay when product arrives'
    },
    {
        id: 'postage',
        name: 'Postage',
        discount: 5,
        description: 'Delivery via post'
    },
    {
        id: 'walkin',
        name: 'Walk In',
        discount: 10,
        description: 'Self pickup at store'
    }
];

// Payment methods with discounts
const paymentMethods = [
    {
        id: 'full',
        name: 'Full Payment',
        discount: 5,
        description: 'Pay in full now'
    },
    {
        id: 'booking',
        name: 'Booking / Pre-Order',
        discount: 0,
        description: 'Pay deposit first'
    },
    {
        id: 'walkin',
        name: 'Walk In',
        discount: 3,
        description: 'Pay at store'
    }
];

// Calculations
const subtotal = computed(() => props.product.price * props.quantity);

const deliveryDiscount = computed(() => {
    const method = deliveryMethods.find(m => m.id === form.value.delivery_method);
    return method ? (subtotal.value * method.discount / 100) : 0;
});

const paymentDiscount = computed(() => {
    const method = paymentMethods.find(m => m.id === form.value.payment_method);
    return method ? (subtotal.value * method.discount / 100) : 0;
});

const totalDiscount = computed(() => deliveryDiscount.value + paymentDiscount.value);

const finalPrice = computed(() => Math.max(0, subtotal.value - totalDiscount.value));

// Format price
const formatPrice = (price) => {
    return `RM${price.toFixed(2)}`;
};

// Submit checkout
const isSubmitting = ref(false);
const errors = ref({});

const submitCheckout = () => {
    // Reset errors
    errors.value = {};

    // Basic validation
    if (!form.value.name || !form.value.phone || !form.value.email ||
        !form.value.delivery_method || !form.value.payment_method) {
        errors.value.general = 'Sila lengkapkan semua maklumat yang diperlukan';
        return;
    }

    isSubmitting.value = true;

    // Submit form data
    router.post(route('ecommerce.order.store'), {
        customer_name: form.value.name,
        customer_phone: form.value.phone,
        customer_email: form.value.email,
        delivery_method: form.value.delivery_method,
        payment_method: form.value.payment_method,
        product_id: props.product.id,
        quantity: props.quantity,
        color: props.color,
    }, {
        onSuccess: () => {
            // Success handled by redirect in controller
        },
        onError: (page) => {
            errors.value = page.props.errors || {};
            isSubmitting.value = false;
        },
        onFinish: () => {
            // This will run after success or error
        }
    });
};
</script>

<template>

    <Head title="Checkout" />

    <EcommerceLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
                    <p class="text-gray-600">Complete your information to proceed with order</p>

                    <!-- Error Messages -->
                    <div v-if="errors.general" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-600">{{ errors.general }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Form Section -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Information -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Customer Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                    <input v-model="form.name" type="text" :class="[
                                        'w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-shadow',
                                        errors.customer_name ? 'border-red-300' : 'border-gray-300'
                                    ]" placeholder="Your full name">
                                    <p v-if="errors.customer_name" class="mt-1 text-sm text-red-600">{{
                                        errors.customer_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input v-model="form.phone" type="tel" :class="[
                                        'w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-shadow',
                                        errors.customer_phone ? 'border-red-300' : 'border-gray-300'
                                    ]" placeholder="012-3456789">
                                    <p v-if="errors.customer_phone" class="mt-1 text-sm text-red-600">{{
                                        errors.customer_phone }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input v-model="form.email" type="email" :class="[
                                        'w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-shadow',
                                        errors.customer_email ? 'border-red-300' : 'border-gray-300'
                                    ]" placeholder="email@example.com">
                                    <p v-if="errors.customer_email" class="mt-1 text-sm text-red-600">{{
                                        errors.customer_email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Method -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Delivery Method</h2>
                            <div class="space-y-3">
                                <label v-for="method in deliveryMethods" :key="method.id" :class="[
                                    'flex items-center justify-between p-4 border-2 rounded-xl cursor-pointer transition-all',
                                    form.delivery_method === method.id
                                        ? 'border-gray-900 bg-gray-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]">
                                    <div class="flex items-center">
                                        <input v-model="form.delivery_method" :value="method.id" type="radio"
                                            class="w-5 h-5 text-gray-900 focus:ring-gray-900">
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">{{ method.name }}</p>
                                            <p class="text-sm text-gray-600">{{ method.description }}</p>
                                        </div>
                                    </div>
                                    <div v-if="method.discount > 0"
                                        class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">
                                        Save {{ method.discount }}%
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>
                            <div class="space-y-3">
                                <label v-for="method in paymentMethods" :key="method.id" :class="[
                                    'flex items-center justify-between p-4 border-2 rounded-xl cursor-pointer transition-all',
                                    form.payment_method === method.id
                                        ? 'border-gray-900 bg-gray-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]">
                                    <div class="flex items-center">
                                        <input v-model="form.payment_method" :value="method.id" type="radio"
                                            class="w-5 h-5 text-gray-900 focus:ring-gray-900">
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">{{ method.name }}</p>
                                            <p class="text-sm text-gray-600">{{ method.description }}</p>
                                        </div>
                                    </div>
                                    <div v-if="method.discount > 0"
                                        class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">
                                        Save {{ method.discount }}%
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-20">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>

                            <!-- Product Info -->
                            <div class="flex gap-4 pb-6 border-b border-gray-200">
                                <img :src="product.image" :alt="product.name" class="w-20 h-20 object-cover rounded-lg"
                                    @error="$event.target.src = 'https://placehold.co/80x80'">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 mb-1">{{ product.name }}</h3>
                                    <p class="text-sm text-gray-600">Quantity: {{ quantity }}</p>
                                    <p v-if="color" class="text-sm text-gray-600">Color: {{ color }}</p>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="py-6 space-y-3 border-b border-gray-200">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span>{{ formatPrice(subtotal) }}</span>
                                </div>

                                <div v-if="deliveryDiscount > 0" class="flex justify-between text-green-600">
                                    <span>Delivery Discount</span>
                                    <span>-{{ formatPrice(deliveryDiscount) }}</span>
                                </div>

                                <div v-if="paymentDiscount > 0" class="flex justify-between text-green-600">
                                    <span>Payment Discount</span>
                                    <span>-{{ formatPrice(paymentDiscount) }}</span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="pt-6 pb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ formatPrice(finalPrice) }}</span>
                                </div>
                                <div v-if="totalDiscount > 0" class="text-sm text-green-600 text-right">
                                    You save {{ formatPrice(totalDiscount) }}!
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button @click="submitCheckout" :disabled="isSubmitting"
                                class="w-full py-4 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ isSubmitting ? 'Processing...' : 'Place Order' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EcommerceLayout>
</template>
