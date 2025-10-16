<template>

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
                            <button @click="showCreateModal = true" v-if="!showArchived"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Product
                            </button>
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
                                                <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                                <div class="text-sm text-gray-500">{{ product.description?.substring(0,
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
                                        <div class="text-sm text-gray-900">RM{{ product.price }}</div>
                                        <div v-if="product.original_price" class="text-xs text-gray-500 line-through">
                                            RM{{ product.original_price }}</div>
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
                                                <button type="button" @click="editProduct(product)"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
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

            <!-- Create/Edit Product Modal -->
            <Teleport to="body">
                <div v-if="showCreateModal || showEditModal" class="fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal">
                        </div>

                        <!-- Modal panel -->
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ showEditModal ? 'Edit Product' : 'Add New Product' }}
                                    </h3>
                                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <form @submit.prevent="submitProduct" class="space-y-4">
                                    <!-- Product Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                                        <input v-model="form.name" placeholder="Enter Your Product" type="text" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea v-model="form.description" placeholder="Enter Your Description"
                                            rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                        <select v-model="form.category_id" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Select Category</option>
                                            <option v-for="category in categories" :key="category.id"
                                                :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">
                                            or Add new Category <button type="button" @click="showCategoryModal = true"
                                                class="text-blue-600 underline hover:text-blue-800">here</button>
                                        </p>
                                    </div>

                                    <!-- Price and Stock -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Price
                                                (RM)</label>
                                            <input v-model.number="form.price" type="number" step="0.01" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                                            <input v-model.number="form.stock" type="number" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                        </div>
                                    </div>

                                    <!-- Image Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Product
                                            Image</label>

                                        <!-- Dropzone Container -->
                                        <div ref="dropzoneElement"
                                            class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 transition-colors">
                                            <div class="dz-message" data-dz-message>
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-600">
                                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                                </p>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                            </div>
                                        </div>

                                        <!-- Current Image (for edit mode) -->
                                        <div v-if="!imagePreview && form.image_url" class="mt-3">
                                            <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                            <img :src="form.image_url" alt="Current"
                                                class="w-32 h-32 object-cover rounded-lg border" />
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-center">
                                        <input v-model="form.is_active" type="checkbox" id="is_active"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                            Active (visible in store)
                                        </label>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex justify-end gap-3 pt-4">
                                        <button type="button" @click="closeModal"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                            {{ showEditModal ? 'Update' : 'Create' }} Product
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Category Modal -->
            <Teleport to="body">
                <div v-if="showCategoryModal" class="fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="category-modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            @click="closeCategoryModal">
                        </div>

                        <!-- Modal panel -->
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900" id="category-modal-title">
                                        Add New Category
                                    </h3>
                                    <button @click="closeCategoryModal" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <form @submit.prevent="submitCategory" class="space-y-4">
                                    <!-- Category Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Category
                                            Name</label>
                                        <input v-model="categoryForm.name"
                                            @input="categoryForm.name = categoryForm.name.toUpperCase()"
                                            placeholder="Enter Category Name" type="text" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase" />
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex justify-end gap-3 pt-4">
                                        <button type="button" @click="closeCategoryModal"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                            Add Category
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Dropzone } from 'dropzone';
import 'dropzone/dist/dropzone.css';

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
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showCategoryModal = ref(false);
const editingProduct = ref(null);
const imagePreview = ref(null);
const imageFile = ref(null);
const dropzoneElement = ref(null);
const dropzoneInstance = ref(null);
const categories = ref(props.categories || []);
const categoryForm = ref({ name: '' });

// Form data
const form = ref({
    name: '',
    description: '',
    category_id: '',
    price: 0,
    stock: 0,
    image: null,
    image_url: null,
    is_active: true
});

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
const initializeDropzone = () => {
    if (!dropzoneElement.value) return;

    // Destroy existing instance if any
    if (dropzoneInstance.value) {
        dropzoneInstance.value.destroy();
        dropzoneInstance.value = null;
    }

    // Initialize new Dropzone instance
    dropzoneInstance.value = new Dropzone(dropzoneElement.value, {
        url: '/fake-url', // We handle upload manually, so this is just a placeholder
        autoProcessQueue: false, // Don't auto-upload
        maxFiles: 1,
        maxFilesize: 10, // MB
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        dictDefaultMessage: '',
        thumbnailWidth: 200,
        thumbnailHeight: 200,
        init: function () {
            this.on('addedfile', function (file) {
                // Remove previous file if exists
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }

                // Store the file
                imageFile.value = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.value = e.target.result;
                };
                reader.readAsDataURL(file);
            });

            this.on('removedfile', function () {
                imageFile.value = null;
                imagePreview.value = null;
            });
        }
    });
};

const editProduct = (product) => {
    editingProduct.value = product;
    form.value = {
        ...product,
        image_url: product.image_url || product.image
    };
    imagePreview.value = null;
    imageFile.value = null;
    showEditModal.value = true;
};

const deleteProduct = (id) => {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }

    router.delete(route('products.destroy', id), {
        preserveScroll: true,
        onError: () => {
            alert('Failed to delete product. Please try again.');
        }
    });
};

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

const submitProduct = async () => {
    const formData = new FormData();

    // Add form fields
    formData.append('name', form.value.name);
    formData.append('description', form.value.description || '');
    formData.append('category_id', form.value.category_id);
    formData.append('price', form.value.price);
    formData.append('stock', form.value.stock);
    formData.append('is_active', form.value.is_active ? 1 : 0);

    // Add image if uploaded
    if (imageFile.value) {
        formData.append('image', imageFile.value);
    }

    try {
        if (showEditModal.value) {
            // Update product
            await axios.put(`/products/${editingProduct.value.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
        } else {
            // Create product
            await axios.post('/products', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
        }

        closeModal();
        // Refresh the page to show updated data
        window.location.reload();
    } catch (error) {
        console.error('Error saving product:', error);
        alert('Error saving product. Please try again.');
    }
};

const closeModal = () => {
    // Destroy Dropzone instance
    if (dropzoneInstance.value) {
        dropzoneInstance.value.destroy();
        dropzoneInstance.value = null;
    }

    showCreateModal.value = false;
    showEditModal.value = false;
    editingProduct.value = null;
    imagePreview.value = null;
    imageFile.value = null;
    form.value = {
        name: '',
        description: '',
        category_id: '',
        price: 0,
        stock: 0,
        image: null,
        image_url: null,
        is_active: true
    };
};

// Category methods
const fetchCategories = async () => {
    try {
        const response = await axios.get('/categories');
        categories.value = response.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const submitCategory = async () => {
    try {
        const response = await axios.post('/categories', {
            name: categoryForm.value.name
        });

        if (response.data.success) {
            // Add the new category to the list
            categories.value.push(response.data.category);

            // Select the new category in the form
            form.value.category_id = response.data.category.id;

            // Close the modal
            closeCategoryModal();
        }
    } catch (error) {
        console.error('Error creating category:', error);
        alert('Error creating category. Please try again.');
    }
};

const closeCategoryModal = () => {
    showCategoryModal.value = false;
    categoryForm.value = { name: '' };
};

// Watch for modal open to initialize Dropzone
watch([showCreateModal, showEditModal], async ([create, edit]) => {
    if (create || edit) {
        await nextTick();
        initializeDropzone();
    }
});
</script>

<style scoped>
/* Custom Dropzone Styling */
:deep(.dropzone) {
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #fafafa;
}

:deep(.dropzone .dz-message) {
    margin: 0;
}

:deep(.dropzone .dz-preview) {
    margin: 10px;
}

:deep(.dropzone .dz-preview .dz-image) {
    border-radius: 8px;
    overflow: hidden;
}

:deep(.dropzone .dz-preview .dz-image img) {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

:deep(.dropzone .dz-preview .dz-error-message) {
    top: 130px;
    left: -10px;
    width: 220px;
    background: #ef4444;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
}

:deep(.dropzone .dz-preview .dz-remove) {
    font-size: 12px;
    color: #ef4444;
    margin-top: 8px;
    text-decoration: none;
    font-weight: 500;
}

:deep(.dropzone .dz-preview .dz-remove:hover) {
    text-decoration: underline;
}

:deep(.dropzone.dz-drag-hover) {
    border-color: #3b82f6;
    background-color: #eff6ff;
}
</style>
