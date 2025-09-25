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
const branches = ref(['All', 'BERTAM', 'PADANG_SERAI', 'IPOH']);
const selectedBranch = ref('All');
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
const isPrinting = ref(false);
const printError = ref(null);

// Fetch FAQ data dari API
const fetchFAQData = async (page = 1, fetchAll = false) => {
    try {
        if (!fetchAll) {
            loading.value = true;
        }

        // Build query parameters
        const params = new URLSearchParams({
            page: page,
            per_page: fetchAll ? 1000 : 10
        });

        if (searchQuery.value) {
            params.append('search', searchQuery.value);
        }

        if (selectedBranch.value && selectedBranch.value !== 'All') {
            params.append('branch', selectedBranch.value);
        }

        console.log('Fetching FAQ data with params:', params.toString()); // Debug log
        const response = await fetch(`/api/faq/db?${params.toString()}`);
        const result = await response.json();
        console.log('FAQ data result:', result); // Debug log

        if (result.success) {
            if (fetchAll) {
                return result.data;
            }

            faqData.value = result.data;
            pagination.value = result.pagination;
            currentPage.value = result.pagination.current_page;
            // branches sudah di-hardcode, tidak perlu set dari API
        } else {
            error.value = 'Failed to load FAQ data';
        }
    } catch (err) {
        error.value = 'An error occurred while loading FAQ data';
        console.error('Error fetching FAQ data:', err);
    } finally {
        if (!fetchAll) {
            loading.value = false;
        }
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

// Handle delete FAQ
const handleDeleteFAQ = (faq) => {
    // Untuk sekarang, hanya console.log dulu
    console.log('Delete FAQ:', faq);
    alert(`Delete FAQ: ${faq.question}`);
};

// Handle search change
const handleSearchChange = (query) => {
    console.log('Search changed to:', query); // Debug log
    searchQuery.value = query;
    currentPage.value = 1; // Reset to first page
    fetchFAQData(1);
};

// Handle branch change
const handleBranchChange = (branch) => {
    selectedBranch.value = branch;
    currentPage.value = 1; // Reset to first page
    fetchFAQData(1);
};

// Handle print FAQ data
const handlePrintFAQ = async () => {
    try {
        isPrinting.value = true;
        printError.value = null;

        // Fetch all FAQ data without pagination limit
        const allFaqData = await fetchFAQData(1, true);

        if (!allFaqData || allFaqData.length === 0) {
            alert('Tiada FAQ untuk dicetak');
            return;
        }

        // Generate printable content
        const printWindow = window.open('', '_blank');

        if (!printWindow) {
            alert('Tidak dapat membuka tetingkap cetak. Sila semak tetapan pop-up browser.');
            return;
        }

        const printableContent = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>FAQ Print</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 40px;
            color: #1f2937;
        }
        h1 {
            text-align: center;
            color: #111827;
            margin-bottom: 30px;
        }
        .faq-item {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        .faq-item:last-child {
            border-bottom: none;
        }
        .question {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .answer {
            margin: 0;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <h1>FAQ List</h1>
    ${allFaqData.map((faq, index) =>
            `<div class="faq-item">
            <div class="question">Question ${index + 1}: ${faq.question}</div>
            <div class="answer">Answer: ${faq.answer}</div>
        </div>`
        ).join('')}
</body>
</html>
        `;

        printWindow.document.write(printableContent);
        printWindow.document.close();
        printWindow.focus();

        // Wait for content to load before printing
        printWindow.onload = () => {
            printWindow.print();
            printWindow.close();
        };

    } catch (err) {
        console.error('Error printing FAQ:', err);
        printError.value = 'Gagal mencetak FAQ. Sila cuba lagi nanti.';
    } finally {
        isPrinting.value = false;
    }
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
                <FAQSearch :search-query="searchQuery" :selected-branch="selectedBranch" :branches="branches"
                    @update:search-query="handleSearchChange" @update:selected-branch="handleBranchChange"
                    @add-faq="handleAddFAQ" @print-faq="handlePrintFAQ" />

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
                <FAQAccordion v-else-if="faqData.length > 0" :faq-items="faqData" @refresh-data="fetchFAQData" />

                <!-- Pagination -->
                <Pagination v-if="faqData.length > 0 && pagination.last_page > 1"
                    :current-page="pagination.current_page" :total-pages="pagination.last_page"
                    :total-items="pagination.total" :items-per-page="pagination.per_page"
                    @page-change="handlePageChange" />

                <!-- No Results -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No questions found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Try changing the search keyword or select another branch.
                        </p>
                    </div>
                </div>

                <!-- Help Section -->
                <FAQHelp @contact-customer-service="handleContactCustomerService" />
            </div>
        </div>

        <!-- Add FAQ Modal -->
        <Modal :show="showAddFAQModal" @close="closeAddFAQModal">
            <AddFAQForm :branches="branches" :is-submitting="isSubmitting" :countdown-timer="countdownTimer"
                @close="closeAddFAQModal" @submit="handleFAQSubmit" />
        </Modal>
    </AuthenticatedLayout>
</template>
