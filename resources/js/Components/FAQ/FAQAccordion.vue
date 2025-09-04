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

// Emit untuk delete function, edit function dan refresh
const emit = defineEmits(['delete-faq', 'edit-faq', 'refresh-data']);

// State untuk accordion
const openItems = ref(new Set());

// State untuk edit modal
const showEditModal = ref(false);
const editingFAQ = ref(null);
const editForm = ref({
    question: '',
    answer: '',
    branch: ''
});
const isSubmitting = ref(false);
const countdownTimer = ref(0);

// Function untuk format branch name
const formatBranchName = (branchCode) => {
    const branchMap = {
        'BERTAM': 'Bertam',
        'PADANG_SERAI': 'Padang Serai',
        'IPOH': 'Ipoh'
    };
    return branchMap[branchCode] || branchCode;
};

// Branch options untuk edit form
const branchOptions = ['BERTAM', 'PADANG_SERAI', 'IPOH'];

// Computed untuk form validation
const isEditFormValid = computed(() => {
    return editForm.value.question.trim() !== '' &&
        editForm.value.answer.trim() !== '' &&
        editForm.value.branch !== '';
});

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

// Open edit modal
const openEditModal = (faq) => {
    editingFAQ.value = faq;
    editForm.value = {
        question: faq.question,
        answer: faq.answer,
        branch: faq.branch
    };
    showEditModal.value = true;
};

// Close edit modal
const closeEditModal = () => {
    showEditModal.value = false;
    editingFAQ.value = null;
    editForm.value = {
        question: '',
        answer: '',
        branch: ''
    };
    isSubmitting.value = false;
    countdownTimer.value = 0;
};

// Submit edit form
const submitEditForm = async () => {
    if (!isEditFormValid.value || isSubmitting.value) return;

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
        // Call edit API
        const response = await axios.put(`/api/faq/${editingFAQ.value.id}`, {
            question: editForm.value.question.trim(),
            answer: editForm.value.answer.trim(),
            branch: editForm.value.branch
        });

        if (response.data.success) {
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
            closeEditModal();
            emit('refresh-data');

            // Show success message
            Swal.fire({
                title: 'Success!',
                text: 'FAQ successfully updated!',
                icon: 'success',
                confirmButtonColor: '#10b981',
                timer: 2000,
                timerProgressBar: true
            });
        }
    } catch (error) {
        console.error('Error updating FAQ:', error);

        Swal.fire({
            title: 'Error!',
            text: 'Failed to update FAQ: ' + (error.response?.data?.message || error.message),
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    } finally {
        isSubmitting.value = false;
        countdownTimer.value = 0;
        clearInterval(timer);
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
                text: 'FAQ successfully deleted!',
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
            text: 'Failed to delete FAQ: ' + (error.response?.data?.message || error.message),
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

                <!-- Action Buttons -->
                <div class="flex items-center border-l border-gray-200">
                    <!-- Edit Button -->
                    <button @click="openEditModal(faq)"
                        class="px-4 py-4 text-blue-500 hover:text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-colors duration-200"
                        title="Edit FAQ">
                        <i class="fas fa-edit text-lg"></i>
                    </button>

                    <!-- Delete Button -->
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

    <!-- Edit FAQ Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeEditModal"></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Edit FAQ</h3>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <form @submit.prevent="submitEditForm" class="space-y-4">
                        <!-- Question Input -->
                        <div>
                            <label for="edit-question" class="block text-sm font-medium text-gray-700 mb-1">
                                Question
                            </label>
                            <input id="edit-question" v-model="editForm.question" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter FAQ question..." />
                        </div>

                        <!-- Answer Input -->
                        <div>
                            <label for="edit-answer" class="block text-sm font-medium text-gray-700 mb-1">
                                Answer
                            </label>
                            <textarea id="edit-answer" v-model="editForm.answer" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter answer for this question..."></textarea>
                        </div>

                        <!-- Branch Select -->
                        <div>
                            <label for="edit-branch" class="block text-sm font-medium text-gray-700 mb-1">
                                Branch
                            </label>
                            <select id="edit-branch" v-model="editForm.branch" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select branch...</option>
                                <option v-for="branch in branchOptions" :key="branch" :value="branch">
                                    {{ formatBranchName(branch) }}
                                </option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button @click="closeEditModal" :disabled="isSubmitting"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancel
                    </button>
                    <button @click="submitEditForm" :disabled="!isEditFormValid || isSubmitting"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="isSubmitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span v-if="countdownTimer > 0">Updating... ({{ countdownTimer }}s)</span>
                            <span v-else>Updating...</span>
                        </span>
                        <span v-else>Update FAQ</span>
                    </button>
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
