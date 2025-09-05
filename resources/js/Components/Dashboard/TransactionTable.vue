<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Recent Conversations</h3>
                <div class="flex items-center gap-3">
                    <!-- Auto refresh indicator -->
                    <div v-if="isRefreshing" class="flex items-center gap-2 text-sm text-blue-600">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Updating...</span>
                    </div>

                    <!-- Last update time -->
                    <div v-else-if="lastUpdateTime" class="text-xs text-gray-500">
                        Last updated: {{ formatTime(lastUpdateTime) }}
                    </div>

                    <!-- Manual refresh button -->
                    <button @click="manualRefresh"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Refresh conversations">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="px-6 py-8 text-center">
            <div class="flex items-center justify-center">
                <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="ml-2 text-gray-600">Loading conversations...</span>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="messageLogs.length === 0" class="px-6 py-8 text-center">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations yet</h3>
                <p class="mt-1 text-sm text-gray-500">Start a conversation to see it here.</p>
            </div>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Customer Name
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Last Customer Message
                        </th>
                        <th class="text-left py-3 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Created At
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="(log, index) in messageLogs" :key="log.id" class="hover:bg-gray-50 transition-colors"
                        :class="{
                            'bg-green-50 border-l-4 border-green-400': isNewData(index),
                            'animate-pulse': isNewData(index)
                        }">
                        <!-- Customer Name -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="font-medium text-gray-900">{{ log.customer_name }}</div>
                                <span v-if="isNewData(index)"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    New
                                </span>
                            </div>
                        </td>

                        <!-- Last Customer Message -->
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-900 max-w-xs truncate" :title="log.last_customer_message">
                                {{ log.last_customer_message }}
                            </div>
                        </td>

                        <!-- Created At -->
                        <td class="py-4 px-6">
                            <span class="text-gray-900">{{ log.created_at }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// State management
const messageLogs = ref([])
const loading = ref(true)
const error = ref(null)
const isRefreshing = ref(false)
const lastUpdateTime = ref(null)
const newDataCount = ref(0)

// Auto refresh settings
const refreshInterval = ref(null)
const refreshIntervalMs = 5000 // Refresh setiap 5 saat

// Fetch message logs data dari API
const fetchMessageLogs = async (showLoading = true) => {
    try {
        if (showLoading) {
            loading.value = true
        } else {
            isRefreshing.value = true
        }
        error.value = null

        const response = await axios.get('/api/message-logs')

        if (response.data.success) {
            const newData = response.data.data
            const previousCount = messageLogs.value.length

            // Check if ada data baru
            if (lastUpdateTime.value && newData.length > previousCount) {
                newDataCount.value = newData.length - previousCount
                // Show notification untuk data baru
                showNewDataNotification(newDataCount.value)

                // Auto clear new data indicator after 10 seconds
                setTimeout(() => {
                    newDataCount.value = 0
                }, 10000)
            }

            messageLogs.value = newData
            lastUpdateTime.value = new Date()
        } else {
            error.value = response.data.message || 'Failed to fetch message logs'
        }
    } catch (err) {
        console.error('Error fetching message logs:', err)
        error.value = 'Failed to load conversations'
        messageLogs.value = []
    } finally {
        loading.value = false
        isRefreshing.value = false
    }
}

// Show notification untuk data baru
const showNewDataNotification = (count) => {
    // Create notification element
    const notification = document.createElement('div')
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300'
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${count} new conversation${count > 1 ? 's' : ''} received!</span>
        </div>
    `

    // Add to page
    document.body.appendChild(notification)

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)'
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification)
            }
        }, 300)
    }, 3000)
}

// Start auto refresh
const startAutoRefresh = () => {
    // Clear existing interval jika ada
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value)
    }

    // Set new interval untuk auto refresh
    refreshInterval.value = setInterval(() => {
        fetchMessageLogs(false) // false = jangan show loading spinner
    }, refreshIntervalMs)
}

// Stop auto refresh
const stopAutoRefresh = () => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value)
        refreshInterval.value = null
    }
}

// Load data when component mounts
onMounted(() => {
    fetchMessageLogs()
    startAutoRefresh()
})

// Cleanup when component unmounts
onUnmounted(() => {
    stopAutoRefresh()
})

// Refresh data function (can be called from parent)
const refreshData = () => {
    fetchMessageLogs()
}

// Manual refresh function
const manualRefresh = () => {
    fetchMessageLogs()
}

// Format time untuk display
const formatTime = (date) => {
    if (!date) return ''
    const now = new Date()
    const diff = now - date
    const seconds = Math.floor(diff / 1000)
    const minutes = Math.floor(seconds / 60)

    if (seconds < 60) {
        return `${seconds}s ago`
    } else if (minutes < 60) {
        return `${minutes}m ago`
    } else {
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        })
    }
}

// Check if data is new (first few items after refresh)
const isNewData = (index) => {
    return index < newDataCount.value
}

// Expose functions to parent
defineExpose({
    refreshData,
    manualRefresh,
    startAutoRefresh,
    stopAutoRefresh
})
</script>
