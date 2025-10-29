<template>
    <div>

        <Head title="Products" />
        <!-- TODO: Implement the product management system -->
        <AuthenticatedLayout>
            <div class="min-h-screen bg-gray-50">
                <!-- Header Section -->
                <div class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">Product Management</h1>
                                <p class="mt-1 text-sm text-gray-500">Manage your product catalog</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <!-- Toggle Archived Products -->
                                <button @click="toggleArchived" :class="[
                                    'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150',
                                    showArchived
                                        ? 'bg-gray-600 hover:bg-gray-700 text-white'
                                        : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                                ]">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 8l6 6 6-6" />
                                    </svg>
                                    {{ showArchived ? 'Show Active' : 'Show Archived' }}
                                </button>

                                <!-- Add Product Button -->
                                <Link :href="route('products.create')" v-if="!showArchived"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Product
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Search and Filter Bar -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Search Input -->
                            <div class="flex-1">
                                <div class="relative">
                                    <input v-model="searchQuery" type="text" placeholder="Search products..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <select v-model="selectedCategory"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>

                            <!-- Sort -->
                            <select v-model="sortBy"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="name">Name</option>
                                <option value="price">Price</option>
                                <option value="date">Date Added</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Products Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="product in filteredProducts" :key="product.id"
                                        class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-12 w-12 flex-shrink-0">
                                                    <img :src="product.image_url || 'https://via.placeholder.com/48x48?text=No+Image'"
                                                        :alt="product.name" class="h-12 w-12 rounded-lg object-cover">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ product.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{
                                                        product.description?.substring(0,
                                                        30) }}...</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ product.category?.name || 'No Category' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div v-if="product.original_price"
                                                class="text-xs text-gray-500 line-through">
                                                RM{{ product.original_price }}</div>
                                            <div class="text-sm text-gray-900">RM{{ product.price }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="product.stock > 10 ? 'text-green-600' : 'text-red-600'"
                                                class="text-sm font-medium">
                                                {{ product.stock || 0 }} units
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ product.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <!-- Active Products Actions -->
                                                <template v-if="!showArchived">
                                                    <Link :href="route('products.edit', product.id)"
                                                        class="text-blue-600 hover:text-blue-900 transition-colors"
                                                        title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    </Link>
                                                    <button type="button" @click="archiveProduct(product.id)"
                                                        class="text-orange-600 hover:text-orange-900 transition-colors"
                                                        title="Archive" @click.stop>
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 8l6 6 6-6" />
                                                        </svg>
                                                    </button>
                                                </template>

                                                <!-- Archived Products Actions -->
                                                <template v-else>
                                                    <button type="button" @click="restoreProduct(product.id)"
                                                        class="text-green-600 hover:text-green-900 transition-colors"
                                                        title="Restore" @click.stop>
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" @click="forceDeleteProduct(product.id)"
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Permanently Delete" @click.stop>
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="filteredProducts.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding a new product.</p>
                        </div>
                    </div>
                </div>


            </div>
        </AuthenticatedLayout>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({
            data: [],
            links: {},
            meta: {}
        })
    },
    categories: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            category: '',
            sort_by: 'name',
            show_archived: false
        })
    },
    showArchived: {
        type: Boolean,
        default: false
    }
});

// State
const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const sortBy = ref(props.filters?.sort_by || 'name');
const showArchived = ref(props.showArchived || false);

// Computed
const productList = computed(() => {
    if (Array.isArray(props.products)) {
        return props.products;
    }

    if (Array.isArray(props.products?.data)) {
        return props.products.data;
    }

    return [];
});

const filteredProducts = computed(() => {
    let filtered = productList.value.slice();

    // Search
    if (searchQuery.value) {
        filtered = filtered.filter(p =>
            p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            p.description?.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    // Category
    if (selectedCategory.value) {
        filtered = filtered.filter(p => p.category_id == selectedCategory.value);
    }

    // Sort
    filtered = filtered.slice().sort((a, b) => {
        if (sortBy.value === 'name') {
            return a.name.localeCompare(b.name);
        } else if (sortBy.value === 'price') {
            return Number(a.price) - Number(b.price);
        } else if (sortBy.value === 'date') {
            return new Date(b.created_at) - new Date(a.created_at);
        }
        return 0;
    });

    return filtered;
});

// Methods

const toggleArchived = () => {
    showArchived.value = !showArchived.value;
    router.get(route('products.index'), {
        ...props.filters,
        show_archived: showArchived.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const archiveProduct = (id) => {
    if (!confirm('Are you sure you want to archive this product?')) {
        return;
    }

    router.delete(route('products.destroy', id), {
        preserveScroll: true,
        onError: () => {
            alert('Failed to archive product. Please try again.');
        }
    });
};

const restoreProduct = (id) => {
    if (!confirm('Are you sure you want to restore this product?')) {
        return;
    }

    router.post(route('products.restore', id), {}, {
        preserveScroll: true,
        onError: () => {
            alert('Failed to restore product. Please try again.');
        }
    });
};

const forceDeleteProduct = (id) => {
    if (!confirm('Are you sure you want to permanently delete this product? This action cannot be undone!')) {
        return;
    }

    router.delete(route('products.forceDelete', id), {
        preserveScroll: true,
        onError: () => {
            alert('Failed to permanently delete product. Please try again.');
        }
    });
};

</script>
