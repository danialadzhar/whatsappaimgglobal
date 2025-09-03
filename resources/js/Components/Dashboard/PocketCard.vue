<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <!-- Header dengan icon -->
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl" :class="iconBgClass">
                {{ icon }}
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-lg">{{ title }}</h3>
                <p class="text-sm text-gray-500">{{ description }}</p>
            </div>
        </div>

        <!-- Statistik Number -->
        <div class="mb-4">
            <p class="text-3xl font-bold text-gray-900">{{ formattedCount }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ subtitle }}</p>
        </div>

        <!-- Action Button -->
        <button
            class="flex items-center justify-between w-full p-3 hover:bg-gray-50 rounded-lg transition-colors group">
            <span class="text-sm text-gray-700">{{ actionText }}</span>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    count: {
        type: Number,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        required: true
    },
    iconBgClass: {
        type: String,
        default: 'bg-blue-100'
    },
    actionText: {
        type: String,
        default: 'View Details'
    }
})

// Format count dengan koma
const formattedCount = computed(() => {
    return props.count.toLocaleString('en-US')
})
</script>
