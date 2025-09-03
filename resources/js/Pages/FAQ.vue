<script setup>
import { ref, onMounted, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import FAQSearch from '@/Components/FAQ/FAQSearch.vue';
import FAQAccordion from '@/Components/FAQ/FAQAccordion.vue';
import FAQHelp from '@/Components/FAQ/FAQHelp.vue';
import Modal from '@/Components/Modal.vue';
import AddFAQForm from '@/Components/FAQ/AddFAQForm.vue';
import Pagination from '@/Components/Pagination.vue';

// State untuk FAQ data
const faqData = ref([]);
const categories = ref([]);
const selectedCategory = ref('All');
const searchQuery = ref('');
const loading = ref(true);
const error = ref(null);

// Pagination state
const currentPage = ref(1);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
    from: 0,
    to: 0
});

// Modal state
const showAddFAQModal = ref(false);
const isSubmitting = ref(false);
const countdownTimer = ref(0);

// Fetch FAQ data dari API
const fetchFAQData = async (page = 1) => {
    try {
        loading.value = true;

        // Build query parameters
        const params = new URLSearchParams({
            page: page,
            per_page: 10
        });

        if (searchQuery.value) {
            params.append('search', searchQuery.value);
        }

        if (selectedCategory.value && selectedCategory.value !== 'All') {
            params.append('category', selectedCategory.value);
        }

        console.log('Fetching FAQ data with params:', params.toString()); // Debug log
        const response = await fetch(`/api/faq/db?${params.toString()}`);
        const result = await response.json();
        console.log('FAQ data result:', result); // Debug log

        if (result.success) {
            faqData.value = result.data;
            pagination.value = result.pagination;
            currentPage.value = result.pagination.current_page;
            categories.value = ['All', ...result.categories];
        } else {
            error.value = 'Failed to load FAQ data';
        }
    } catch (err) {
        error.value = 'An error occurred while loading FAQ data';
        console.error('Error fetching FAQ data:', err);
    } finally {
        loading.value = false;
    }
};

// Handle customer service contact
const handleContactCustomerService = () => {
    // Implementation for contact customer service
    alert('Contact customer service feature will be implemented later');
};

// Handle add FAQ modal
const handleAddFAQ = () => {
    showAddFAQModal.value = true;
};

// Close add FAQ modal
const closeAddFAQModal = () => {
    showAddFAQModal.value = false;
    countdownTimer.value = 0;
};

// Handle FAQ form submission
const handleFAQSubmit = async (formData) => {
    isSubmitting.value = true;

    try {
        // Get CSRF token safely
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Call web API to save FAQ
        const response = await fetch('/api/faq', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            // Start countdown timer
            countdownTimer.value = 5;

            // Countdown every second
            const timer = setInterval(() => {
                countdownTimer.value--;
                if (countdownTimer.value <= 0) {
                    clearInterval(timer);
                }
            }, 1000);

            // Wait 5 seconds to show progress
            await new Promise(resolve => setTimeout(resolve, 5000));

            // Clear timer
            clearInterval(timer);

            // Close modal
            closeAddFAQModal();

            // Refresh FAQ data
            await fetchFAQData();
        } else {
            console.error('Failed to add FAQ:', result.message);
        }

    } catch (error) {
        console.error('Error adding FAQ:', error);
    } finally {
        isSubmitting.value = false;
    }
};

// Handle pagination change
const handlePageChange = (page) => {
    currentPage.value = page;
    fetchFAQData(page);
};

// Handle search change
const handleSearchChange = (query) => {
    console.log('Search changed to:', query); // Debug log
    searchQuery.value = query;
    currentPage.value = 1; // Reset to first page
    fetchFAQData(1);
};

// Handle category change
const handleCategoryChange = (category) => {
    selectedCategory.value = category;
    currentPage.value = 1; // Reset to first page
    fetchFAQData(1);
};

// Load data saat component mounted
onMounted(() => {
    console.log('Component mounted, fetching initial data'); // Debug log
    fetchFAQData();
});
</script>

<template>

    <Head title="FAQ - WhatsApp AI Chatbot" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Frequently Asked Questions (FAQ)
                </h2>
                <div class="text-sm text-gray-500">
                    {{ pagination.total }} questions found
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Search dan Filter Section -->
                <FAQSearch :search-query="searchQuery" :selected-category="selectedCategory" :categories="categories"
                    @update:search-query="handleSearchChange" @update:selected-category="handleCategoryChange"
                    @add-faq="handleAddFAQ" />

                <!-- Loading State -->
                <div v-if="loading" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-2 text-gray-600">Loading FAQ data...</p>
                    </div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800">{{ error }}</p>
                    </div>
                </div>

                <!-- FAQ Accordion -->
                <FAQAccordion v-else-if="faqData.length > 0" :faq-items="faqData" />

                <!-- Pagination -->
                <Pagination v-if="faqData.length > 0 && pagination.last_page > 1"
                    :current-page="pagination.current_page" :total-pages="pagination.last_page"
                    :total-items="pagination.total" :items-per-page="pagination.per_page"
                    @page-change="handlePageChange" />

                <!-- No Results -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No questions found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Try changing the search keyword or select another category.
                        </p>
                    </div>
                </div>

                <!-- Help Section -->
                <FAQHelp @contact-customer-service="handleContactCustomerService" />
            </div>
        </div>

        <!-- Add FAQ Modal -->
        <Modal :show="showAddFAQModal" @close="closeAddFAQModal">
            <AddFAQForm :categories="categories" :is-submitting="isSubmitting" :countdown-timer="countdownTimer"
                @close="closeAddFAQModal" @submit="handleFAQSubmit" />
        </Modal>
    </AuthenticatedLayout>
</template>
