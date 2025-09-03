<script setup>
import { computed } from 'vue';

// Props untuk pagination
const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
        default: 1
    },
    totalPages: {
        type: Number,
        required: true,
        default: 1
    },
    totalItems: {
        type: Number,
        required: true,
        default: 0
    },
    itemsPerPage: {
        type: Number,
        required: true,
        default: 10
    },
    showInfo: {
        type: Boolean,
        default: true
    }
});

// Emits untuk pagination events
const emit = defineEmits(['page-change']);

// Computed untuk pagination info
const startItem = computed(() => {
    return (props.currentPage - 1) * props.itemsPerPage + 1;
});

const endItem = computed(() => {
    return Math.min(props.currentPage * props.itemsPerPage, props.totalItems);
});

// Computed untuk visible pages
const visiblePages = computed(() => {
    const pages = [];
    const maxVisible = 5;

    if (props.totalPages <= maxVisible) {
        // Show all pages if total is less than max visible
        for (let i = 1; i <= props.totalPages; i++) {
            pages.push(i);
        }
    } else {
        // Show pages around current page
        let start = Math.max(1, props.currentPage - 2);
        let end = Math.min(props.totalPages, start + maxVisible - 1);

        // Adjust start if we're near the end
        if (end - start < maxVisible - 1) {
            start = Math.max(1, end - maxVisible + 1);
        }

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
    }

    return pages;
});

// Methods
const goToPage = (page) => {
    if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
        emit('page-change', page);
    }
};

const goToPrevious = () => {
    if (props.currentPage > 1) {
        goToPage(props.currentPage - 1);
    }
};

const goToNext = () => {
    if (props.currentPage < props.totalPages) {
        goToPage(props.currentPage + 1);
    }
};
</script>

<template>
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <!-- Pagination Info -->
        <div v-if="showInfo" class="flex-1 flex justify-between sm:hidden">
            <div class="text-sm text-gray-700">
                Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} results
            </div>
        </div>

        <div v-if="showInfo" class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ startItem }}</span> to
                    <span class="font-medium">{{ endItem }}</span> of
                    <span class="font-medium">{{ totalItems }}</span> results
                </p>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div class="flex-1 flex justify-between sm:justify-end">
            <!-- Mobile Previous/Next -->
            <div class="flex sm:hidden">
                <button @click="goToPrevious" :disabled="currentPage === 1"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button @click="goToNext" :disabled="currentPage === totalPages"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>

            <!-- Desktop Pagination -->
            <div class="hidden sm:flex sm:items-center sm:space-x-2">
                <!-- Previous Button -->
                <button @click="goToPrevious" :disabled="currentPage === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Page Numbers -->
                <template v-for="page in visiblePages" :key="page">
                    <button v-if="page === currentPage"
                        class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                        {{ page }}
                    </button>
                    <button v-else @click="goToPage(page)"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ page }}
                    </button>
                </template>

                <!-- Next Button -->
                <button @click="goToNext" :disabled="currentPage === totalPages"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
