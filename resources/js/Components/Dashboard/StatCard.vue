<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <!-- Header dengan dropdown tahun -->
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-gray-900">{{ title }}</h3>
        </div>

        <!-- Amount dengan optional percentage -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <span v-if="showPercentage" class="flex items-center text-sm font-medium px-2 py-1 rounded-full" :class="percentageClass">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            :d="isPositive ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6'">
                        </path>
                    </svg>
                    {{ Math.abs(percentage) }}%
                </span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900">{{ formattedAmount }}</span>
                    <span class="text-sm text-gray-500">{{ labelText }}</span>
                </div>
            </div>
        </div>

        <!-- Simple Chart -->
        <div class="h-20 flex items-end gap-1">
            <div v-for="(value, index) in chartData" :key="index"
                class="flex-1 rounded-t-sm transition-all duration-300 hover:opacity-80 relative group"
                :class="chartColor" :style="{ height: `${(value / Math.max(...chartData)) * 100}%` }">
                <!-- Tooltip untuk menunjukkan jumlah harian -->
                <div
                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                    {{ value }} <span class="text-gray-300 text-xs">conversations</span>
                </div>
            </div>
        </div>

        <!-- Chart Labels -->
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span v-for="label in chartLabels" :key="label">{{ label }}</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    amount: {
        type: Number,
        required: true
    },
    percentage: {
        type: Number,
        required: false,
        default: null
    },
    chartData: {
        type: Array,
        required: true
    },
    chartLabels: {
        type: Array,
        required: true
    },
    type: {
        type: String,
        default: 'income' // 'income' atau 'spent'
    },
    labelText: {
        type: String,
        default: 'conversations'
    }
})

// Format amount untuk conversation count
const formattedAmount = computed(() => {
    return props.amount.toLocaleString('en-US')
})

// Tentukan warna berdasarkan jenis dan percentage
const isPositive = computed(() => (props.percentage ?? 0) >= 0)

const showPercentage = computed(() => props.percentage !== null && props.percentage !== undefined)

const percentageClass = computed(() => {
    if (props.type === 'spent') {
        return isPositive.value
            ? 'text-red-700 bg-red-50'
            : 'text-green-700 bg-green-50'
    } else {
        return isPositive.value
            ? 'text-green-700 bg-green-50'
            : 'text-red-700 bg-red-50'
    }
})

const chartColor = computed(() => {
    return props.type === 'spent' ? 'bg-red-500' : 'bg-green-500'
})
</script>
