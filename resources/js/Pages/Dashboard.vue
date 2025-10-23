<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import PocketCard from '@/Components/Dashboard/PocketCard.vue';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import TransactionTable from '@/Components/Dashboard/TransactionTable.vue';
import { ref, computed } from 'vue';

// Props untuk menerima data dari controller
const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    conversation: {
        type: Object,
        required: false,
        default: () => ({ today: 0, weeklyTotal: 0, series: [], labels: [] })
    }
});

// Data untuk WhatsApp AI Chatbot statistics menggunakan real data dari database
const chatbotStats = computed(() => [
    {
        title: 'Total Customer',
        description: 'Customer engaged',
        count: props.stats.totalCustomers || 0,
        subtitle: 'Active customers this month',
        icon: '👥',
        iconBgClass: 'bg-blue-100',
        actionText: 'View All Customers',
        actionRoute: 'customers'
    },
    {
        title: 'Customer Messages',
        description: 'Total messages from customers',
        count: props.stats.totalMessages || 0,
        subtitle: 'Total messages',
        icon: '💬',
        iconBgClass: 'bg-green-100'
    },
    {
        title: 'Response Rate',
        description: 'AI response in 1 minute',
        count: parseFloat((props.stats.responseRate || 0).toFixed(2)),
        subtitle: 'Percentage of fast responses',
        icon: '⚡',
        iconBgClass: 'bg-green-100'
    }
]);

// Data untuk charts - Conversation Analytics (guna data sebenar)
const conversationData = computed(() => ({
    amount: props.conversation.today ?? 0,
    chartData: props.conversation.series ?? [],
    chartLabels: props.conversation.labels ?? []
}))

const responseTimeData = ref({
    amount: 2.3,
    percentage: -8.2,
    chartData: [3.2, 2.8, 2.5, 2.1, 2.4, 2.0, 1.8],
    chartLabels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
});

// TransactionTable component will handle its own data fetching from message_logs table

// Fungsi untuk handle action click
const handleActionClick = (actionRoute) => {
    if (actionRoute) {
        router.visit(route(actionRoute));
    }
};
</script>

<template>
    <div>

        <Head title="Dashboard" />

        <AuthenticatedLayout>
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            WhatsApp AI Chatbot
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Monitor conversation statistics and user engagement</p>
                    </div>
                    <!-- <div class="flex items-center gap-3">
                    <input type="search" placeholder="Search conversations..."
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div> -->
                </div>
            </template>

            <div class="py-8 bg-gray-50 min-h-screen">
                <div class="mx-auto max-w-7xl px-6">
                    <!-- Chatbot Statistics Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <PocketCard v-for="stat in chatbotStats" :key="stat.title" :title="stat.title"
                            :description="stat.description" :count="stat.count" :subtitle="stat.subtitle"
                            :icon="stat.icon" :icon-bg-class="stat.iconBgClass" :action-text="stat.actionText"
                            :action-route="stat.actionRoute" @action-click="handleActionClick" />
                    </div>

                    <!-- Analytics Cards Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <StatCard title="Daily Conversations" :amount="props.conversation.weeklyTotal ?? 0"
                            :chart-data="conversationData.chartData" :chart-labels="conversationData.chartLabels"
                            label-text="conversations" type="income" />
                        <!-- Avg Response Time - Not Ready State -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Avg Response Time (sec)</h3>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                                    <span class="text-xs text-orange-600 font-medium">Coming Soon</span>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="text-center py-8">
                                <!-- Icon -->
                                <div
                                    class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>

                                <!-- Message -->
                                <h4 class="text-lg font-medium text-gray-700 mb-2">Feature in Development</h4>
                                <p class="text-sm text-gray-500 leading-relaxed">
                                    Response time analytics will be available soon. We're working on integrating
                                    real-time
                                    performance metrics.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Conversations Table -->
                    <TransactionTable />
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>
