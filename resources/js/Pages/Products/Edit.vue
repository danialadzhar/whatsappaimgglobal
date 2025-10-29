<template>
    <div>

        <Head title="Edit Product" />
        <AuthenticatedLayout>
            <div class="min-h-screen bg-gray-50">
                <!-- Header Section -->
                <div class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">Edit Product</h1>
                                <p class="mt-1 text-sm text-gray-500">Update product information</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <Link :href="route('products.index')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Products
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <form @submit.prevent="submitProduct" class="space-y-6">
                            <!-- Product Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                                <input v-model="form.name" placeholder="Enter product name" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea v-model="form.description" placeholder="Enter product description" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <div class="flex gap-2">
                                    <select v-model="form.category_id" required
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Category</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <button type="button" @click="showCategoryModal = true"
                                        class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                        Add Category
                                    </button>
                                </div>
                            </div>

                            <!-- Normal Price and Sales Price -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Normal Price
                                        (RM)</label>
                                    <input v-model.number="form.normal_price" type="number" step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales Price (RM)</label>
                                    <input v-model.number="form.price" type="number" step="0.01" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                </div>
                            </div>

                            <!-- Color and Storage -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                    <select v-model="form.color"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Pilih Warna</option>
                                        <option value="Black">Black</option>
                                        <option value="White">White</option>
                                        <option value="Silver">Silver</option>
                                        <option value="Gold">Gold</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Red">Red</option>
                                        <option value="Purple">Purple</option>
                                        <option value="Green">Green</option>
                                        <option value="Pink">Pink</option>
                                        <option value="Gray">Gray</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Storage</label>
                                    <select v-model="form.storage"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Pilih Storage</option>
                                        <option value="64GB">64GB</option>
                                        <option value="128GB">128GB</option>
                                        <option value="256GB">256GB</option>
                                        <option value="512GB">512GB</option>
                                        <option value="1TB">1TB</option>
                                        <option value="2TB">2TB</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Stock Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                                <input v-model.number="form.stock" type="number" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>

                                <!-- Current Image -->
                                <div v-if="!imagePreview && form.image_url" class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                    <img :src="form.image_url" alt="Current"
                                        class="w-32 h-32 object-cover rounded-lg border" />
                                </div>

                                <!-- Dropzone Container -->
                                <div ref="dropzoneElement"
                                    class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 transition-colors">
                                    <div class="dz-message" data-dz-message>
                                        <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-4 text-lg text-gray-600">
                                            <span class="font-semibold">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-sm text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div v-if="imagePreview" class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">New Image Preview:</p>
                                    <img :src="imagePreview" alt="Preview"
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
                            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                                <Link :href="route('products.index')"
                                    class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                                </Link>
                                <button type="submit" :disabled="isSubmitting"
                                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span v-if="isSubmitting">Updating...</span>
                                    <span v-else>Update Product</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Category Modal -->
                <Teleport to="body">
                    <div v-if="showCategoryModal" class="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="category-modal-title" role="dialog" aria-modal="true">
                        <div
                            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
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
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Dropzone } from 'dropzone';
import 'dropzone/dist/dropzone.css';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    }
});

// State
const showCategoryModal = ref(false);
const imagePreview = ref(null);
const imageFile = ref(null);
const dropzoneElement = ref(null);
const dropzoneInstance = ref(null);
const categories = ref(props.categories || []);
const categoryForm = ref({ name: '' });
const isSubmitting = ref(false);

// Form data
const form = ref({
    name: props.product.name || '',
    description: props.product.description || '',
    category_id: props.product.category_id || '',
    normal_price: props.product.normal_price || 0,
    price: props.product.price || 0,
    color: props.product.color || '',
    storage: props.product.storage || '',
    stock: props.product.stock || 0,
    image_url: props.product.image_url || null,
    is_active: props.product.is_active !== undefined ? props.product.is_active : true
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

const submitProduct = async () => {
    isSubmitting.value = true;
    const basePayload = {
        name: form.value.name,
        description: form.value.description || '',
        category_id: form.value.category_id,
        normal_price: form.value.normal_price || 0,
        price: form.value.price,
        color: form.value.color || '',
        storage: form.value.storage || '',
        stock: form.value.stock,
        is_active: form.value.is_active ? 1 : 0,
    };

    try {
        if (imageFile.value) {
            const formData = new FormData();
            Object.entries(basePayload).forEach(([key, value]) => {
                formData.append(key, value ?? '');
            });
            formData.append('_method', 'PUT');
            formData.append('image', imageFile.value);

            await axios.post(`/products/${props.product.id}`, formData);
        } else {
            await axios.put(`/products/${props.product.id}`, basePayload);
        }

        // Redirect to products index after successful update
        router.visit(route('products.index'), {
            onSuccess: () => {
                // Show success message or notification here
            }
        });
    } catch (error) {
        console.error('Error updating product:', error);
        if (error.response?.data?.errors) {
            console.error('Validation errors:', error.response.data.errors);
            alert('Validation failed. Sila semak input anda.');
        } else {
            alert('Error updating product. Please try again.');
        }
    } finally {
        isSubmitting.value = false;
    }
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

// Lifecycle
onMounted(() => {
    fetchCategories();
    nextTick(() => {
        initializeDropzone();
    });
});
</script>

<style scoped>
/* Custom Dropzone Styling */
:deep(.dropzone) {
    min-height: 200px;
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
