<template>

    <Head title="Customers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customers
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Header Section -->
                        <div class="mb-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                        Customer List
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Manage your customer information and contact details
                                    </p>
                                </div>

                                <!-- Search and Add Button Row -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Search Input -->
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input v-model="searchQuery" type="text" placeholder="Search customers..."
                                            class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200" />
                                        <!-- Clear button -->
                                        <div v-if="searchQuery"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <button @click="clearSearch"
                                                class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Add New Customer Button -->
                                    <button @click="openAddModal"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add New Customer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Section -->
                        <div v-if="customers.length > 0" class="mt-8 pt-6 border-t border-gray-200 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="text-blue-600 text-2xl mr-3">👥</div>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">Total Customers</p>
                                            <p class="text-2xl font-bold text-blue-600">{{ filteredCustomers.length }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="text-green-600 text-2xl mr-3">📱</div>
                                        <div>
                                            <p class="text-sm font-medium text-green-900">Active Contacts</p>
                                            <p class="text-2xl font-bold text-green-600">{{ filteredCustomers.length }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-purple-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="text-purple-600 text-2xl mr-3">💬</div>
                                        <div>
                                            <p class="text-sm font-medium text-purple-900">WhatsApp Ready</p>
                                            <p class="text-2xl font-bold text-purple-600">{{ filteredCustomers.length }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Section -->
                        <div class="overflow-x-auto relative">
                            <!-- Loading Spinner Overlay -->
                            <div v-if="isSearching"
                                class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                                <div class="flex flex-col items-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2">
                                    </div>
                                    <p class="text-sm text-gray-600">Searching...</p>
                                </div>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200">
                                <!-- Table Header -->
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Phone Number
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>

                                <!-- Table Body -->
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="customer in filteredCustomers" :key="customer.id"
                                        class="hover:bg-gray-50 transition-colors duration-150">
                                        <!-- Name Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                                        <span class="text-white font-medium text-sm">
                                                            {{ customer.name.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ customer.name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Phone Number Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ customer.phone_number }}
                                            </div>
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-150">
                                                    View
                                                </button>
                                                <button
                                                    class="text-green-600 hover:text-green-900 transition-colors duration-150">
                                                    Edit
                                                </button>
                                                <button
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-150">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="customers.length === 0" class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">👥</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No customers found</h3>
                            <p class="text-gray-600">Start by adding your first customer to the system.</p>
                        </div>

                        <!-- No Search Results -->
                        <div v-else-if="searchQuery && filteredCustomers.length === 0" class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">🔍</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
                            <p class="text-gray-600 mb-4">No customers match your search for "{{ searchQuery }}"</p>
                            <button @click="clearSearch"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                Clear Search
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Add Customer Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAddModal"></div>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <!-- Modal header -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Add New Customer
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Enter customer information to add them to your list.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal body -->
                    <div class="bg-white px-4 pb-4 sm:p-6 sm:pt-0">
                        <form @submit.prevent="addCustomer" class="space-y-4">
                            <!-- Name Input -->
                            <div>
                                <label for="customer-name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Customer Name
                                </label>
                                <input id="customer-name" v-model="newCustomer.name" type="text" required
                                    placeholder="Enter customer name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200" />
                            </div>

                            <!-- Phone Number Input -->
                            <div>
                                <label for="customer-phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone Number
                                </label>
                                <input id="customer-phone" v-model="newCustomer.phone_number" type="tel" required
                                    placeholder="Enter phone number"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200" />
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="addCustomer" type="button" :disabled="isSubmitting"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                            :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }">
                            <span v-if="isSubmitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span v-if="countdownTimer > 0">Adding... ({{ countdownTimer }}s)</span>
                                <span v-else>Adding...</span>
                            </span>
                            <span v-else>Add Customer</span>
                        </button>
                        <button @click="closeAddModal" type="button" :disabled="isSubmitting"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                            :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

// Props dari controller
const props = defineProps({
    customers: {
        type: Array,
        required: true
    }
});

// Search functionality
const searchQuery = ref('');
const isSearching = ref(false);

// Modal functionality
const showAddModal = ref(false);
const newCustomer = ref({
    name: '',
    phone_number: ''
});

// Loading states
const isSubmitting = ref(false);
const countdownTimer = ref(0);

// Computed property untuk filtered customers
const filteredCustomers = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.customers;
    }

    const query = searchQuery.value.toLowerCase().trim();
    return props.customers.filter(customer =>
        customer.name.toLowerCase().includes(query) ||
        customer.phone_number.includes(query)
    );
});

// Watch search query untuk loading state
watch(searchQuery, (newQuery, oldQuery) => {
    if (newQuery !== oldQuery) {
        isSearching.value = true;

        // Simulate search delay untuk loading effect
        setTimeout(() => {
            isSearching.value = false;
        }, 300);
    }
});

// Clear search function
const clearSearch = () => {
    searchQuery.value = '';
    isSearching.value = false;
};

// Modal functions
const openAddModal = () => {
    showAddModal.value = true;
    newCustomer.value = {
        name: '',
        phone_number: ''
    };
};

const closeAddModal = () => {
    showAddModal.value = false;
    newCustomer.value = {
        name: '',
        phone_number: ''
    };
    // Reset loading states
    isSubmitting.value = false;
    countdownTimer.value = 0;
};

const addCustomer = async () => {
    // Validate form
    if (!newCustomer.value.name.trim() || !newCustomer.value.phone_number.trim()) {
        alert('Sila isi semua field yang diperlukan!');
        return;
    }

    // Set loading state
    isSubmitting.value = true;
    countdownTimer.value = 5;

    // Start countdown timer
    const timer = setInterval(() => {
        countdownTimer.value--;
        if (countdownTimer.value <= 0) {
            clearInterval(timer);
        }
    }, 1000);

    try {
        // Submit form data to API endpoint
        const response = await axios.post('/api/customers', newCustomer.value);

        if (response.data.success) {
            console.log('Customer berjaya ditambah!', response.data.customer);

            // Wait for countdown to finish before closing modal
            const waitForCountdown = new Promise((resolve) => {
                const checkCountdown = setInterval(() => {
                    if (countdownTimer.value <= 0) {
                        clearInterval(checkCountdown);
                        resolve();
                    }
                }, 100);
            });

            await waitForCountdown;

            closeAddModal();
            // Refresh the page to show new customer
            router.reload();
        }
    } catch (error) {
        console.error('Error adding customer:', error);
        alert('Terdapat ralat semasa menambah customer. Sila cuba lagi.');
    } finally {
        // Reset loading state
        isSubmitting.value = false;
        countdownTimer.value = 0;
        clearInterval(timer);
    }
};
</script>
