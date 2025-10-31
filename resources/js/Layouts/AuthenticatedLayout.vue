<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const showingNavigationDropdown = ref(false);
const expandedMenus = ref(['E-Commerce']); // Track expanded menu items

// Chatbot status
const chatbotStatus = ref({
    active: false,
    lastUpdated: null
});

// Toggle submenu
const toggleSubmenu = (itemName) => {
    const index = expandedMenus.value.indexOf(itemName);
    if (index > -1) {
        expandedMenus.value.splice(index, 1);
    } else {
        expandedMenus.value.push(itemName);
    }
};

const isExpanded = (itemName) => {
    return expandedMenus.value.includes(itemName);
};

// Get chatbot status
const getChatbotStatus = async () => {
    try {
        const response = await axios.get('/api/settings/chatbot-status');
        if (response.data.success) {
            chatbotStatus.value.active = response.data.chatbot_active;
            chatbotStatus.value.lastUpdated = response.data.last_updated;
        }
    } catch (error) {
        console.error('Error getting chatbot status:', error);
    }
};

// Load chatbot status on mount
onMounted(() => {
    getChatbotStatus();
});

// Navigation items sesuai design Mizal Gadjet
const navigationItems = ref([
    {
        name: 'Dashboard',
        href: 'dashboard',
        icon: '📊',
        active: true
    },
    {
        name: 'FAQ',
        href: 'faq',
        icon: '❓',
        active: false
    },
    {
        name: 'Customers',
        href: 'customers',
        icon: '👥',
        active: false
    },
    {
        name: 'Chat',
        href: 'chat',
        icon: '💬',
        active: false
    },
    // {
    //     name: 'Settings',
    //     href: 'settings',
    //     icon: '⚙️',
    //     active: false
    // },
    {
        name: 'E-Commerce',
        href: 'products.index',
        icon: '🛒',
        active: false,
        children: [
            {
                name: 'Products',
                href: 'products.index',
                icon: '📦',
                active: false
            },
            {
                name: 'Orders',
                href: 'orders.index',
                icon: '📋',
                active: false
            },
            {
                name: 'Settings',
                href: 'settings',
                icon: '⚙️',
                active: false
            }
        ]
    },
    //   {
    //     name: 'Cards',
    //     href: '#',
    //     icon: '💳',
    //     active: false
    //   },
    //   {
    //     name: 'Insight',
    //     href: '#',
    //     icon: '📈',
    //     active: false
    //   },
    //   {
    //     name: 'Rewards',
    //     href: '#',
    //     icon: '🎁',
    //     active: false
    //   }
]);

const supportItems = ref([
    {
        name: 'Help',
        href: '#',
        icon: '❓'
    },
    {
        name: 'Feedback',
        href: '#',
        icon: '💬'
    }
]);
</script>

<template>
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0">
            <div class="flex flex-col flex-grow pt-5 bg-white border-r border-gray-200">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-6">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                            <!-- Icon gadjet (smartphone) -->
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="6" y="2" width="12" height="20" rx="3" fill="currentColor" stroke="none"/>
                                <rect x="9" y="18" width="6" height="2" rx="1" fill="#22d3ee" stroke="none"/>
                                <rect x="8" y="4" width="8" height="12" rx="1" fill="white" fill-opacity="0.5" stroke="none"/>
                                <circle cx="12" cy="19" r="1" fill="#fff" stroke="none"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Mizal Gadjet</span>
                        <!-- <button class="ml-auto p-1 hover:bg-gray-100 rounded">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8V4a1 1 0 011-1h4M4 8l4-4m0 0v4M4 8h4m6-4h4a1 1 0 011 1v4m0 0l-4-4m4 0v4m0 4l-4 4m4 0h-4m0 0v-4m0 4v4a1 1 0 01-1 1h-4">
                                </path>
                            </svg>
                        </button> -->
                    </div>
                </div>

                <!-- Chatbot Status Indicator -->
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">🤖 Chatbot AI</span>
                            <div :class="[
                                chatbotStatus.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            ]" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ chatbotStatus.active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                        <Link :href="route('settings')" class="text-xs text-blue-600 hover:text-blue-800">
                        Settings
                        </Link>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 flex-grow flex flex-col">
                    <div class="px-3">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">General</p>
                        <nav class="mt-2 space-y-1">
                            <template v-for="item in navigationItems">
                                <!-- Parent Menu Item with Children -->
                                <div v-if="item.children" :key="`parent-${item.name}`">
                                    <button @click="toggleSubmenu(item.name)" :class="[
                                        item.active
                                            ? 'bg-blue-50 border-r-2 border-blue-600 text-blue-700'
                                            : 'text-gray-700 hover:bg-gray-50',
                                        'group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-l-lg'
                                    ]">
                                        <div class="flex items-center">
                                            <span class="text-lg mr-3">{{ item.icon }}</span>
                                            {{ item.name }}
                                        </div>
                                        <svg :class="[
                                            'w-4 h-4 transition-transform duration-200',
                                            isExpanded(item.name) ? 'transform rotate-180' : ''
                                        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Submenu Items -->
                                    <div v-show="isExpanded(item.name)" class="ml-6 mt-1 space-y-1">
                                        <Link v-for="child in item.children" :key="child.name"
                                            :href="['dashboard', 'customers', 'faq', 'chat', 'settings', 'products.index', 'orders.index'].includes(child.href) ? route(child.href) : child.href"
                                            :class="[
                                                child.active
                                                    ? 'bg-blue-50 border-r-2 border-blue-600 text-blue-700'
                                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                'group flex items-center px-3 py-2 text-sm font-medium rounded-l-lg'
                                            ]">
                                        <span class="text-base mr-3">{{ child.icon }}</span>
                                        {{ child.name }}
                                        </Link>
                                    </div>
                                </div>

                                <!-- Regular Menu Item without Children -->
                                <Link v-else :key="`regular-${item.name}`"
                                    :href="['dashboard', 'customers', 'faq', 'chat', 'settings', 'products.index', 'orders.index'].includes(item.href) ? route(item.href) : item.href"
                                    :class="[
                                        item.active
                                            ? 'bg-blue-50 border-r-2 border-blue-600 text-blue-700'
                                            : 'text-gray-700 hover:bg-gray-50',
                                        'group flex items-center px-3 py-2 text-sm font-medium rounded-l-lg'
                                    ]">
                                <span class="text-lg mr-3">{{ item.icon }}</span>
                                {{ item.name }}
                                </Link>
                            </template>
                        </nav>
                    </div>

                    <!-- Logout Button -->
                    <div class="mt-auto px-3 pb-6">
                        <Link :href="route('logout')" method="post" as="button"
                            class="w-full group flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors duration-200">
                        <span class="text-lg mr-3">🚪</span>
                        Logout
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <!-- Top Navigation -->
            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white border-b border-gray-200 lg:hidden">
                <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                    class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex">
                        <!-- Mobile logo -->
                        <div class="flex items-center">
                            <span class="text-xl font-bold text-gray-900">Mizal Gadjet</span>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Profile dropdown -->
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{
                                            $page.props.auth.user.name.charAt(0) }}</span>
                                    </div>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- Page Heading -->
            <header class="bg-white border-b border-gray-200" v-if="$slots.header">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <div v-show="showingNavigationDropdown" class="fixed inset-0 flex z-40 lg:hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="showingNavigationDropdown = false"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="showingNavigationDropdown = false"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <span class="text-xl font-bold text-gray-900">Mizal Gadjet</span>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <template v-for="item in navigationItems">
                            <!-- Parent Menu Item with Children -->
                            <div v-if="item.children" :key="`mobile-parent-${item.name}`">
                                <button @click="toggleSubmenu(item.name)"
                                    class="w-full flex items-center justify-between px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="text-lg mr-3">{{ item.icon }}</span>
                                        {{ item.name }}
                                    </div>
                                    <svg :class="[
                                        'w-4 h-4 transition-transform duration-200',
                                        isExpanded(item.name) ? 'transform rotate-180' : ''
                                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Submenu Items -->
                                <div v-show="isExpanded(item.name)" class="ml-6 mt-1 space-y-1">
                                    <ResponsiveNavLink v-for="child in item.children" :key="child.name"
                                        :href="['dashboard', 'customers', 'faq', 'chat', 'settings', 'products.index', 'orders.index'].includes(child.href) ? route(child.href) : child.href"
                                        :active="child.active">
                                        <span class="text-base mr-3">{{ child.icon }}</span>
                                        {{ child.name }}
                                    </ResponsiveNavLink>
                                </div>
                            </div>

                            <!-- Regular Menu Item without Children -->
                            <ResponsiveNavLink v-else :key="`mobile-regular-${item.name}`"
                                :href="['dashboard', 'customers', 'faq', 'chat', 'settings', 'products.index', 'orders.index'].includes(item.href) ? route(item.href) : item.href"
                                :active="item.active">
                                <span class="text-lg mr-3">{{ item.icon }}</span>
                                {{ item.name }}
                            </ResponsiveNavLink>
                        </template>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <div class="flex-shrink-0 group block">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">{{ $page.props.auth.user.name.charAt(0)
                                    }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs font-medium text-gray-500">{{ $page.props.auth.user.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
