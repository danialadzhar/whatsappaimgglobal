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
        actionText: 'View All Customer',
        actionRoute: 'customers'
    },
    {
        title: 'Customer Messages',
        description: 'Total messages from customers',
        count: props.stats.totalMessages || 0,
        subtitle: 'Total messages',
        icon: '💬',
        iconBgClass: 'bg-green-100',
        actionText: 'View Messages'
    },
    {
        title: 'Response Rate',
        description: 'AI response dalam masa 1 minit',
        count: props.stats.responseRate || 0,
        subtitle: 'Percentage of fast responses',
        icon: '⚡',
        iconBgClass: 'bg-green-100',
        actionText: 'View Analytics'
    }
]);

// Data untuk charts - Conversation Analytics
const conversationData = ref({
    amount: 2847,
    percentage: 12.5,
    chartData: [45, 52, 48, 61, 55, 67, 72],
    chartLabels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
});

const responseTimeData = ref({
    amount: 2.3,
    percentage: -8.2,
    chartData: [3.2, 2.8, 2.5, 2.1, 2.4, 2.0, 1.8],
    chartLabels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
});

// Data untuk recent conversations
const recentConversations = ref([
    {
        id: 1,
        name: 'Ahmad Rahman',
        company: '+60123456789',
        amount: 15,
        date: '2024-01-27',
        type: 'Product Inquiry',
        status: 'Replied'
    },
    {
        id: 2,
        name: 'Siti Aminah',
        company: '+60198765432',
        amount: 8,
        date: '2024-01-27',
        type: 'Support',
        status: 'Pending'
    },
    {
        id: 3,
        name: 'Muhammad Ali',
        company: '+60187654321',
        amount: 23,
        date: '2024-01-26',
        type: 'Order Status',
        status: 'Replied'
    }
]);

// Fungsi untuk handle action click
const handleActionClick = (actionRoute) => {
    if (actionRoute) {
        router.visit(route(actionRoute));
    }
};
</script>

<template>

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
                <div class="flex items-center gap-3">
                    <input type="search" placeholder="Search conversations..."
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </template>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-7xl px-6">
                <!-- Chatbot Statistics Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <PocketCard v-for="stat in chatbotStats" :key="stat.title" :title="stat.title"
                        :description="stat.description" :count="stat.count" :subtitle="stat.subtitle" :icon="stat.icon"
                        :icon-bg-class="stat.iconBgClass" :action-text="stat.actionText"
                        :action-route="stat.actionRoute" @action-click="handleActionClick" />
                </div>

                <!-- Analytics Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <StatCard title="Daily Conversations" :amount="conversationData.amount"
                        :percentage="conversationData.percentage" :chart-data="conversationData.chartData"
                        :chart-labels="conversationData.chartLabels" type="income" />
                    <StatCard title="Avg Response Time (sec)" :amount="responseTimeData.amount"
                        :percentage="responseTimeData.percentage" :chart-data="responseTimeData.chartData"
                        :chart-labels="responseTimeData.chartLabels" type="spent" />
                </div>

                <!-- Recent Conversations Table -->
                <TransactionTable :transactions="recentConversations" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
