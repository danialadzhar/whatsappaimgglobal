<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const isWishlisted = ref(false);
const isAdding = ref(false);

// Fungsi untuk toggle wishlist
const toggleWishlist = () => {
    isWishlisted.value = !isWishlisted.value;
};

// Fungsi untuk add to cart
const addToCart = () => {
    isAdding.value = true;
    // Simulate API call
    setTimeout(() => {
        isAdding.value = false;
        alert(`${props.product.name} ditambah ke cart!`);
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
    <div
        class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
        <!-- Wishlist Button -->
        <button @click="toggleWishlist"
            class="absolute top-4 right-4 z-10 p-2 bg-white rounded-full shadow-md hover:scale-110 transition-transform duration-200"
            :class="isWishlisted ? 'text-red-500' : 'text-gray-400 hover:text-red-500'">
            <svg class="w-5 h-5" :fill="isWishlisted ? 'currentColor' : 'none'" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>

        <!-- Product Image -->
        <Link :href="`/ecommerce/${product.id}`" class="block">
        <div class="aspect-square bg-gray-50 overflow-hidden p-6">
            <img :src="product.image" :alt="product.name"
                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                @error="$event.target.src = 'https://placehold.co/300x300'">
        </div>
        </Link>

        <!-- Product Info -->
        <div class="p-5">
            <Link :href="`/ecommerce/${product.id}`" class="block">
            <h3
                class="text-lg font-semibold text-gray-900 mb-1 line-clamp-1 group-hover:text-blue-600 transition-colors">
                {{ product.name }}
            </h3>
            <p class="text-sm text-gray-500 mb-3 line-clamp-1">
                {{ product.description }}
            </p>
            </Link>

            <!-- Rating -->
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center">
                    <!-- Full stars -->
                    <svg v-for="i in fullStars" :key="`full-${i}`" class="w-4 h-4 text-yellow-400 fill-current"
                        viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <!-- Half star -->
                    <svg v-if="hasHalfStar" class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
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
                        class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <span class="text-xs text-gray-500">({{ product.reviews }})</span>
            </div>

            <!-- Price and Button -->
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex flex-col">
                        <span v-if="product.original_price" class="text-sm text-gray-400 line-through">
                            {{ formatPrice(product.original_price) }}
                        </span>
                        <span class="text-2xl font-bold text-gray-900">
                            {{ formatPrice(product.price) }}
                        </span>
                    </div>
                </div>
                <button @click="addToCart" :disabled="isAdding"
                    class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ isAdding ? 'Adding...' : 'Add to Cart' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Line clamp utility untuk text truncation */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
