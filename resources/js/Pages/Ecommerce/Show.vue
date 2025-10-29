<script setup>
import { ref } from 'vue';
import EcommerceLayout from '@/Layouts/EcommerceLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const selectedColor = ref(props.product.colors?.[0] || 'black');
const quantity = ref(1);
const isAdding = ref(false);

// Fungsi untuk add to cart dan redirect ke checkout
const addToCart = () => {
    isAdding.value = true;
    setTimeout(() => {
        isAdding.value = false;
        // Redirect ke checkout page dengan product data
        router.visit(`/ecommerce/checkout/${props.product.id}`, {
            method: 'get',
            data: {
                quantity: quantity.value,
                color: selectedColor.value
            }
        });
    }, 500);
};

// Format harga
const formatPrice = (price) => {
    return `RM${price.toFixed(2)}`;
};

// Generate rating stars
const generateStars = (rating) => {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    return { fullStars, hasHalfStar };
};

const { fullStars, hasHalfStar } = generateStars(props.product.rating);
</script>

<template>
    <EcommerceLayout>

        <Head :title="product.name" />
        <div class="min-h-screen bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Breadcrumb -->
                <nav class="flex mb-8" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link :href="route('ecommerce.index')"
                                class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Products
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="ml-1 text-gray-500 md:ml-2">{{ product.name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Product Detail -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 lg:p-12">
                        <!-- Product Image -->
                        <div class="aspect-square bg-gray-50 rounded-xl overflow-hidden p-8">
                            <img :src="product.image" :alt="product.name" class="w-full h-full object-contain"
                                @error="$event.target.src = 'https://placehold.co/300x300'">
                        </div>

                        <!-- Product Info -->
                        <div class="flex flex-col justify-center">
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                                {{ product.name }}
                            </h1>

                            <!-- Rating -->
                            <div class="flex items-center gap-3 mb-6">
                                <div class="flex items-center">
                                    <!-- Full stars -->
                                    <svg v-for="i in fullStars" :key="`full-${i}`"
                                        class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <!-- Half star -->
                                    <svg v-if="hasHalfStar" class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20">
                                        <defs>
                                            <linearGradient id="half">
                                                <stop offset="50%" stop-color="currentColor" />
                                                <stop offset="50%" stop-color="#D1D5DB" />
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#half)"
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <!-- Empty stars -->
                                    <svg v-for="i in (5 - fullStars - (hasHalfStar ? 1 : 0))" :key="`empty-${i}`"
                                        class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">{{ product.rating }} ({{ product.reviews }}
                                    reviews)</span>
                            </div>

                            <!-- Price -->
                            <div class="mb-6">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-baseline gap-3">
                                        <span v-if="product.original_price" class="text-xl text-gray-400 line-through">
                                            {{ formatPrice(product.original_price) }}
                                        </span>
                                        <span v-if="product.original_price"
                                            class="px-3 py-1 bg-red-100 text-red-600 text-sm font-semibold rounded-full">
                                            Save {{ Math.round((1 - product.price / product.original_price) * 100) }}%
                                        </span>
                                    </div>
                                    <span class="text-4xl font-bold text-gray-900">
                                        {{ formatPrice(product.price) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ product.description }}
                            </p>

                            <!-- Color Selection -->
                            <div v-if="product.colors && product.colors.length > 0" class="mb-6">
                                <label class="block text-sm font-semibold text-gray-900 mb-3">Color</label>
                                <div class="flex gap-3">
                                    <button v-for="color in product.colors" :key="color" @click="selectedColor = color"
                                        :class="[
                                            'w-12 h-12 rounded-full border-2 transition-all duration-200',
                                            selectedColor === color ? 'border-blue-600 scale-110' : 'border-gray-300 hover:border-gray-400'
                                        ]" :style="{ backgroundColor: color }" :title="color" />
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="mb-8">
                                <label class="block text-sm font-semibold text-gray-900 mb-3">Quantity</label>
                                <div class="flex items-center gap-3">
                                    <button @click="quantity = Math.max(1, quantity - 1)"
                                        class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input v-model.number="quantity" type="number" min="1"
                                        class="w-20 h-10 text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <button @click="quantity++"
                                        class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-4">
                                <button @click="addToCart" :disabled="isAdding"
                                    class="flex-1 px-8 py-4 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ isAdding ? 'Adding to Cart...' : 'Add to Cart' }}
                                </button>
                                <button
                                    class="px-6 py-4 border-2 border-gray-900 text-gray-900 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EcommerceLayout>
</template>
