<script setup>
import { ref, watch } from 'vue';

// Props untuk search dan filter
const props = defineProps({
    searchQuery: {
        type: String,
        required: true
    },
    selectedBranch: {
        type: String,
        required: true
    },
    branches: {
        type: Array,
        required: true,
        default: () => []
    }
});

// Emits untuk update parent component
const emit = defineEmits(['update:searchQuery', 'update:selectedBranch', 'add-faq', 'print-faq']);

// Local search query untuk debounce
const localSearchQuery = ref(props.searchQuery);
console.log('FAQSearch initialized with searchQuery:', props.searchQuery); // Debug log

// Watch untuk sync dengan parent searchQuery
watch(() => props.searchQuery, (newValue) => {
    console.log('Parent searchQuery changed to:', newValue); // Debug log
    localSearchQuery.value = newValue;
});

// Debounce timer
let debounceTimer = null;

// Search loading state
const isSearching = ref(false);

// Watch untuk debounce search
watch(localSearchQuery, (newValue) => {
    // Clear previous timer
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    // Show searching indicator
    isSearching.value = true;

    // Set new timer untuk debounce (500ms)
    debounceTimer = setTimeout(() => {
        console.log('Search triggered:', newValue); // Debug log
        emit('update:searchQuery', newValue);
        isSearching.value = false;
    }, 500);
});

// Update search query immediately (untuk UI feedback)
const updateSearchQuery = (value) => {
    localSearchQuery.value = value;
};

// Update selected branch
const updateSelectedBranch = (value) => {
    emit('update:selectedBranch', value);
};

// Handle add FAQ button click
const handleAddFAQ = () => {
    emit('add-faq');
};

// Handle print FAQ button click
const handlePrintFAQ = () => {
    emit('print-faq');
};

// Clear search
const clearSearch = () => {
    localSearchQuery.value = '';
};
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Search & Filter</h3>
                <div class="flex items-center space-x-3">
                    <!-- Print FAQ Button -->
                    <button @click="handlePrintFAQ"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print FAQ
                    </button>
                    <!-- Add FAQ Button -->
                    <button @click="handleAddFAQ"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add FAQ
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Search Box -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Find Question
                    </label>
                    <div class="relative">
                        <input id="search" v-model="localSearchQuery" type="text" placeholder="Type to find question..."
                            class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <!-- Search Icon -->
                            <svg v-if="!isSearching" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <!-- Loading Spinner -->
                            <svg v-else class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                        <!-- Clear Search Button -->
                        <div v-if="localSearchQuery" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button @click="clearSearch" type="button"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Branch Filter -->
                <div>
                    <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">
                        Branch
                    </label>
                    <select id="branch" :value="selectedBranch" @change="updateSelectedBranch($event.target.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="All">All Branches</option>
                        <option value="BERTAM">Bertam</option>
                        <option value="PADANG_SERAI">Padang Serai</option>
                        <option value="IPOH">Ipoh</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>
