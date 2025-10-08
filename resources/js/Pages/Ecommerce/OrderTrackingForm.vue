<script setup>
import { ref } from 'vue';
import EcommerceLayout from '@/Layouts/EcommerceLayout.vue';
import { Head, router } from '@inertiajs/vue3';

// Form data
const form = ref({
    order_number: '',
    customer_email: ''
});

const isSubmitting = ref(false);
const errors = ref({});
const defaultTrackingError = 'Order tidak ditemui. Sila semak order number dan email anda.';

// Submit tracking form
const submitTracking = () => {
    if (!form.value.order_number || !form.value.customer_email) {
        errors.value = {};
        if (!form.value.order_number) {
            errors.value.order_number = 'Order number diperlukan';
        }
        if (!form.value.customer_email) {
            errors.value.customer_email = 'Email diperlukan';
        }
        return;
    }

    isSubmitting.value = true;
    errors.value = {};

    router.post(route('ecommerce.order.track.submit'), {
        order_number: form.value.order_number,
        customer_email: form.value.customer_email
    }, {
        preserveScroll: true,
        onError: (formErrors) => {
            errors.value = formErrors;
            isSubmitting.value = false;
        },
        onSuccess: () => {
            // Will redirect to signed URL
            isSubmitting.value = false;
        }
    });
};
</script>

<template>
    <EcommerceLayout>

        <Head title="Track Your Order" />

        <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Order</h1>
                    <p class="text-gray-600">
                        Enter order number and email to view your order status
                    </p>
                </div>

                <!-- Tracking Form Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <form @submit.prevent="submitTracking" class="space-y-6">
                        <!-- Order Number Input -->
                        <div>
                            <label for="order_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                Order Number
                            </label>
                            <input type="text" id="order_number" v-model="form.order_number"
                                placeholder="e.g., ORD-20251008-001"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                :class="{ 'border-red-500': errors.order_number }" />
                            <p v-if="errors.order_number" class="mt-2 text-sm text-red-600">
                                {{ errors.order_number }}
                            </p>
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" id="customer_email" v-model="form.customer_email"
                                placeholder="your.email@example.com"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                :class="{ 'border-red-500': errors.customer_email }" />
                            <p v-if="errors.customer_email" class="mt-2 text-sm text-red-600">
                                {{ errors.customer_email }}
                            </p>
                        </div>

                        <!-- General Error -->
                        <div v-if="errors.message || errors.error"
                            class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-red-800">
                                        {{ errors.message || errors.error || defaultTrackingError }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" :disabled="isSubmitting"
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition-all disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center">
                            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span v-if="!isSubmitting">Track Order</span>
                            <span v-else>Searching...</span>
                        </button>
                    </form>

                    <!-- Help Text -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600 text-center">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Order number can be found in the email confirmation or receipt of your order
                        </p>
                    </div>
                </div>

                <!-- Back to Shop -->
                <div class="text-center mt-8">
                    <a :href="route('ecommerce.index')"
                        class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Return to Shop
                    </a>
                </div>
            </div>
        </div>
    </EcommerceLayout>
</template>
