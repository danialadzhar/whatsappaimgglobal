<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import EcommerceLayout from '@/Layouts/EcommerceLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    products: Array,
    filters: Object,
});

// State untuk filters
const selectedCategory = ref('all');
const selectedPriceRange = ref('all');
const sortBy = ref('featured');
const searchQuery = ref('');

// Countdown Timer State
const days = ref(0);
const hours = ref(0);
const minutes = ref(0);
const seconds = ref(0);
let countdownInterval = null;

// Set target date (4 days from now based on the image)
const targetDate = new Date();
targetDate.setDate(targetDate.getDate() + 3);
targetDate.setHours(targetDate.getHours() + 11);
targetDate.setMinutes(targetDate.getMinutes() + 31);
targetDate.setSeconds(targetDate.getSeconds() + 8);

const updateCountdown = () => {
    const now = new Date().getTime();
    const distance = targetDate.getTime() - now;

    if (distance < 0) {
        days.value = 0;
        hours.value = 0;
        minutes.value = 0;
        seconds.value = 0;
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        return;
    }

    days.value = Math.floor(distance / (1000 * 60 * 60 * 24));
    hours.value = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    minutes.value = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    seconds.value = Math.floor((distance % (1000 * 60)) / 1000);
};

onMounted(() => {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});

// Categories untuk filter
const categories = [
    { value: 'all', label: 'All' },
    { value: 'Earbuds', label: 'Earbuds' },
    { value: 'Headphones', label: 'Headphones' },
    { value: 'Earphones', label: 'Earphones' },
];

// Price ranges
const priceRanges = [
    { value: 'all', label: 'All Prices' },
    { value: '0-100', label: 'Under RM100' },
    { value: '100-500', label: 'RM100 - RM500' },
    { value: '500-1000', label: 'RM500 - RM1000' },
    { value: '1000+', label: 'Above RM1000' },
];

// Sort options
const sortOptions = [
    { value: 'featured', label: 'Featured' },
    { value: 'price-low', label: 'Price: Low to High' },
    { value: 'price-high', label: 'Price: High to Low' },
    { value: 'rating', label: 'Top Rated' },
    { value: 'newest', label: 'Newest' },
];

// Filtered and sorted products
const filteredProducts = computed(() => {
    let result = [...props.products];

    // Filter by category
    if (selectedCategory.value !== 'all') {
        result = result.filter(p => p.category === selectedCategory.value);
    }

    // Filter by price range
    if (selectedPriceRange.value !== 'all') {
        const [min, max] = selectedPriceRange.value.split('-');
        if (max) {
            result = result.filter(p => p.price >= parseInt(min) && p.price <= parseInt(max));
        } else {
            result = result.filter(p => p.price >= parseInt(min));
        }
    }

    // Filter by search
    if (searchQuery.value) {
        result = result.filter(p =>
            p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            p.description.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    // Sort
    switch (sortBy.value) {
        case 'price-low':
            result.sort((a, b) => a.price - b.price);
            break;
        case 'price-high':
            result.sort((a, b) => b.price - a.price);
            break;
        case 'rating':
            result.sort((a, b) => b.rating - a.rating);
            break;
        case 'newest':
            result.sort((a, b) => b.id - a.id);
            break;
    }

    return result;
});
</script>

<template>

    <Head title="Shop" />

    <EcommerceLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Countdown Timer Banner -->
            <div
                class="countdown-banner bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white py-3 sm:py-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6">
                        <!-- Urgency Text -->
                        <div class="text-center sm:text-left">
                            <p class="text-xs sm:text-sm font-medium text-gray-300">
                                🔥 <span class="text-yellow-400 font-semibold">TAWARAN TERHAD!</span> Promosi Ansuran
                                Berakhir Dalam:
                            </p>
                        </div>

                        <!-- Countdown Display -->
                        <div class="flex items-center gap-2 sm:gap-3">
                            <!-- Days -->
                            <div class="countdown-box">
                                <div class="countdown-value">{{ String(days).padStart(2, '0') }}</div>
                                <div class="countdown-label">Hari</div>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-gray-400">:</span>

                            <!-- Hours -->
                            <div class="countdown-box">
                                <div class="countdown-value">{{ String(hours).padStart(2, '0') }}</div>
                                <div class="countdown-label">Jam</div>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-gray-400">:</span>

                            <!-- Minutes -->
                            <div class="countdown-box">
                                <div class="countdown-value">{{ String(minutes).padStart(2, '0') }}</div>
                                <div class="countdown-label">Min</div>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-gray-400">:</span>

                            <!-- Seconds -->
                            <div class="countdown-box">
                                <div class="countdown-value animate-pulse">{{ String(seconds).padStart(2, '0') }}</div>
                                <div class="countdown-label">Saat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Banner -->
            <div class="relative bg-gradient-to-r from-pink-50 to-blue-50 overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div>
                            <h4 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                                <!-- Grab Upto <span class="text-blue-600">50% Off</span> On<br>
                                Selected Headphone -->
                                <span class="text-red-600">Bayar Ansuran</span>, Dapat Phone Baru Serendah RM899
                            </h4>
                            <div class="flex flex-wrap gap-4 mt-6">
                                <button
                                    class="px-8 py-3 bg-gray-900 text-white rounded-full font-medium hover:bg-gray-800 transition-colors duration-200">
                                    Buy Now
                                </button>
                                <Link :href="route('ecommerce.order.track.form')"
                                    class="px-8 py-3 bg-white border-2 border-gray-900 text-gray-900 rounded-full font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Track Order
                                </Link>
                            </div>
                        </div>
                        <div class="hidden lg:flex justify-end">
                            <img src="/images/95-removebg-preview.png" alt="Headphone" class="w-80 h-80">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section bg-white border-b border-gray-200 shadow-sm backdrop-blur-sm bg-white/95">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Category Filter -->
                            <select v-model="selectedCategory"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                                    {{ cat.label }}
                                </option>
                            </select>

                            <!-- Price Filter -->
                            <select v-model="selectedPriceRange"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option v-for="price in priceRanges" :key="price.value" :value="price.value">
                                    {{ price.label }}
                                </option>
                            </select>

                            <!-- Search -->
                            <div class="relative flex-1 sm:flex-none">
                                <input type="text" v-model="searchQuery" placeholder="Search products..."
                                    class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent pl-10">
                                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 hidden sm:inline">Sort by:</span>
                            <select v-model="sortBy"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Headphones For You!</h2>

                <!-- No results message -->
                <div v-if="filteredProducts.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-4 text-lg text-gray-600">No products found</p>
                    <p class="mt-2 text-sm text-gray-500">Try adjusting your filters or search query</p>
                </div>

                <!-- Products Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <ProductCard v-for="product in filteredProducts" :key="product.id" :product="product" />
                </div>
            </div>
        </div>
    </EcommerceLayout>
</template>

<style scoped>
/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Countdown Timer Styles */
.countdown-banner {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 50;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.countdown-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 6px 10px;
    min-width: 50px;
    transition: all 0.3s ease;
}

.countdown-box:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.countdown-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #ffffff;
    font-variant-numeric: tabular-nums;
}

.countdown-label {
    font-size: 0.65rem;
    font-weight: 500;
    color: #d1d5db;
    margin-top: 2px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive countdown styles */
@media (min-width: 640px) {
    .countdown-box {
        padding: 8px 14px;
        min-width: 60px;
        border-radius: 10px;
    }

    .countdown-value {
        font-size: 1.75rem;
    }

    .countdown-label {
        font-size: 0.7rem;
        margin-top: 4px;
    }
}

@media (max-width: 639px) {
    .countdown-box {
        padding: 5px 8px;
        min-width: 45px;
    }

    .countdown-value {
        font-size: 1.25rem;
    }

    .countdown-label {
        font-size: 0.6rem;
    }
}

/* Sticky filter section improvements */
.filter-section {
    position: -webkit-sticky;
    position: sticky;
    top: 95px;
    /* Adjusted for countdown banner height (py-4 = 32px + content ~40px) */
    z-index: 40;
    transition: all 0.3s ease;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .filter-section {
        top: 100px;
        /* Adjusted for mobile countdown banner height (stacked layout is taller) */
    }
}

@media (min-width: 640px) and (max-width: 768px) {
    .filter-section {
        top: 68px;
        /* Adjusted for tablet size */
    }
}

/* Ensure proper stacking context */
.filter-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: inherit;
    z-index: -1;
}
</style>
