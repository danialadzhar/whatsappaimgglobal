<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Recent Conversations</h3>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <button class="w-4 h-4 border border-gray-300 rounded flex items-center justify-center">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                Customer
                            </div>
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Messages
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Last Activity
                                <button class="text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </button>
                            </div>
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Category
                                <button class="text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="transaction in transactions" :key="transaction.id"
                        class="hover:bg-gray-50 transition-colors">
                        <!-- Customer Name -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <button class="w-4 h-4 border border-gray-300 rounded"></button>
                                <div>
                                    <div class="font-medium text-gray-900">{{ transaction.name }}</div>
                                    <div class="text-sm text-gray-500">{{ transaction.company }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Message Count -->
                        <td class="py-4 px-6">
                            <span class="font-medium text-gray-900">{{ transaction.amount }} messages</span>
                        </td>

                        <!-- Last Activity -->
                        <td class="py-4 px-6">
                            <span class="text-gray-900">{{ formatDate(transaction.date) }}</span>
                        </td>

                        <!-- Category -->
                        <td class="py-4 px-6">
                            <span class="text-gray-900">{{ transaction.type }}</span>
                        </td>

                        <!-- Status -->
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1 text-sm font-medium"
                                :class="getStatusClass(transaction.status)">
                                <div class="w-2 h-2 rounded-full" :class="getStatusDotClass(transaction.status)"></div>
                                {{ transaction.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    transactions: {
        type: Array,
        required: true
    }
})

// Format message count
const formatMessageCount = (count) => {
    return count + ' messages'
}

// Format tarikh
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Status styling untuk conversation
const getStatusClass = (status) => {
    switch (status.toLowerCase()) {
        case 'replied':
            return 'text-green-700'
        case 'pending':
            return 'text-orange-700'
        case 'failed':
            return 'text-red-700'
        default:
            return 'text-gray-700'
    }
}

const getStatusDotClass = (status) => {
    switch (status.toLowerCase()) {
        case 'replied':
            return 'bg-green-500'
        case 'pending':
            return 'bg-orange-500'
        case 'failed':
            return 'bg-red-500'
        default:
            return 'bg-gray-500'
    }
}
</script>
