<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Array,
    filters: Object,
    pagination: Object
});

// Filter states
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

// Status options
const statusOptions = [
    { value: '', label: 'All Status' },
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
];

// Apply filters
const applyFilters = () => {
    router.get(route('orders.index'), {
        search: searchQuery.value,
        status: statusFilter.value,
        date_from: dateFrom.value,
        date_to: dateTo.value
    }, {
        preserveState: true,
        replace: true
    });
};

// Clear filters
const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
};

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

// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-MY', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>

    <Head title="Orders Management" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
                    <p class="text-gray-600 mt-2">Manage and track customer orders</p>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input v-model="searchQuery" type="text" placeholder="Order number, customer name, email..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select v-model="statusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input v-model="dateFrom" type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input v-model="dateTo" type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex gap-3 mt-4">
                        <button @click="applyFilters"
                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Apply Filters
                        </button>
                        <button @click="clearFilters"
                            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Orders</h2>
                            <span class="text-sm text-gray-600">
                                {{ pagination.total }} orders found
                            </span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Customer
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Items
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ order.order_number }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ order.customer_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ order.customer_email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{
                                            formatPrice(order.total_amount) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            getStatusDisplay(order.status).color === 'yellow' ? 'bg-yellow-100 text-yellow-800' :
                                                getStatusDisplay(order.status).color === 'blue' ? 'bg-blue-100 text-blue-800' :
                                                    getStatusDisplay(order.status).color === 'green' ? 'bg-green-100 text-green-800' :
                                                        getStatusDisplay(order.status).color === 'red' ? 'bg-red-100 text-red-800' :
                                                            'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ getStatusDisplay(order.status).text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ order.items_count }} item{{ order.items_count !== 1 ? 's' : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(order.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link :href="route('orders.detail', order.id)"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="orders.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search criteria.</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.last_page > 1" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} to
                        {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of
                        {{ pagination.total }} results
                    </div>

                    <div class="flex gap-2">
                        <Link v-if="pagination.current_page > 1"
                            :href="route('orders.index', { ...filters, page: pagination.current_page - 1 })"
                            class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Previous
                        </Link>

                        <Link v-if="pagination.current_page < pagination.last_page"
                            :href="route('orders.index', { ...filters, page: pagination.current_page + 1 })"
                            class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Next
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
