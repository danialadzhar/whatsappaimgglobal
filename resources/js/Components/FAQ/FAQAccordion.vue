<script setup>
import { ref } from 'vue';

// Props untuk menerima data FAQ
const props = defineProps({
    faqItems: {
        type: Array,
        required: true,
        default: () => []
    }
});

// State untuk accordion
const openItems = ref(new Set());

// Toggle accordion item
const toggleAccordion = (id) => {
    if (openItems.value.has(id)) {
        openItems.value.delete(id);
    } else {
        openItems.value.add(id);
    }
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="faq in faqItems" :key="faq.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Question Header -->
            <button @click="toggleAccordion(faq.id)"
                class="w-full px-6 py-4 text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-colors duration-200 hover:bg-gray-50"
                :class="{ 'bg-blue-50': openItems.has(faq.id) }">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ faq.question }}
                        </h3>
                        <div class="mt-1 flex items-center space-x-2">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ faq.category }}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </button>

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
</template>

<style scoped>
/* Smooth transition untuk accordion */
.transition-all {
    transition: all 0.3s ease-in-out;
}
</style>
