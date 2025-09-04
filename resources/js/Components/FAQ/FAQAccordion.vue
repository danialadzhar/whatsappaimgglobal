<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

// Props untuk menerima data FAQ
const props = defineProps({
    faqItems: {
        type: Array,
        required: true,
        default: () => []
    }
});

// Emit untuk delete function dan refresh
const emit = defineEmits(['delete-faq', 'refresh-data']);

// State untuk accordion
const openItems = ref(new Set());

// Function untuk format branch name
const formatBranchName = (branchCode) => {
    const branchMap = {
        'BERTAM': 'Bertam',
        'PADANG_SERAI': 'Padang Serai',
        'IPOH': 'Ipoh'
    };
    return branchMap[branchCode] || branchCode;
};

// State untuk delete confirmation - tidak diperlukan lagi dengan SweetAlert
// const showDeleteModal = ref(false);
// const faqToDelete = ref(null);
// const isDeleting = ref(false);

// Toggle accordion item
const toggleAccordion = (id) => {
    if (openItems.value.has(id)) {
        openItems.value.delete(id);
    } else {
        openItems.value.add(id);
    }
};

// Show delete confirmation dengan SweetAlert
const confirmDelete = (faq) => {
    Swal.fire({
        title: 'Confirm Delete FAQ',
        html: `
            <div class="text-left">
                <p class="text-gray-600 mb-3">Are you sure you want to delete this FAQ? This action cannot be undone.</p>
                <div class="bg-gray-50 p-3 rounded-md">
                    <p class="font-medium text-gray-900">${faq.question}</p>
                    <p class="text-sm text-gray-500 mt-1">Branch: ${formatBranchName(faq.branch)}</p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete FAQ',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return deleteFAQ(faq);
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
};

// Delete FAQ function dengan API call dan SweetAlert
const deleteFAQ = async (faq) => {
    try {
        // Call delete API
        const response = await axios.delete(`/api/faq/${faq.id}`);

        if (response.data.success) {
            // Emit refresh data untuk parent component
            emit('refresh-data');

            // Show success message dengan SweetAlert
            Swal.fire({
                title: 'Success!',
                text: 'FAQ successfully deleted',
                icon: 'success',
                confirmButtonColor: '#10b981',
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            throw new Error(response.data.message);
        }
    } catch (error) {
        console.error('Error deleting FAQ:', error);

        // Show error message dengan SweetAlert
        Swal.fire({
            title: 'Error!',
            text: 'Gagal menghapus FAQ: ' + (error.response?.data?.message || error.message),
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });

        throw error; // Re-throw untuk SweetAlert preConfirm handling
    }
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="faq in faqItems" :key="faq.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="flex items-stretch">
                <!-- Question Header -->
                <button @click="toggleAccordion(faq.id)"
                    class="flex-1 px-6 py-4 text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-colors duration-200 hover:bg-gray-50"
                    :class="{ 'bg-blue-50': openItems.has(faq.id) }">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ faq.question }}
                            </h3>
                            <div class="mt-1 flex items-center space-x-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ formatBranchName(faq.branch) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ new Date(faq.created_at).toLocaleDateString('ms-MY') }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': openItems.has(faq.id) }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </button>

                <!-- Delete Button -->
                <div class="flex items-center border-l border-gray-200">
                    <button @click="confirmDelete(faq)"
                        class="px-4 py-4 text-red-500 hover:text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-inset transition-colors duration-200"
                        title="Delete FAQ">
                        <i class="fas fa-trash-alt text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Answer Content -->
            <div v-show="openItems.has(faq.id)" class="px-6 pb-4 border-t border-gray-200">
                <div class="pt-4">
                    <p class="text-gray-700 leading-relaxed">
                        {{ faq.answer }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom confirmation modal tidak diperlukan lagi - menggunakan SweetAlert2 -->
</template>

<style scoped>
/* Smooth transition untuk accordion */
.transition-all {
    transition: all 0.3s ease-in-out;
}
</style>
