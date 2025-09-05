<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

// Navigation items sesuai design WallPay
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
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">WhatsApp AI</span>
                        <!-- <button class="ml-auto p-1 hover:bg-gray-100 rounded">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8V4a1 1 0 011-1h4M4 8l4-4m0 0v4M4 8h4m6-4h4a1 1 0 011 1v4m0 0l-4-4m4 0v4m0 4l-4 4m4 0h-4m0 0v-4m0 4v4a1 1 0 01-1 1h-4">
                                </path>
                            </svg>
                        </button> -->
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 flex-grow flex flex-col">
                    <div class="px-3">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">General</p>
                        <nav class="mt-2 space-y-1">
                            <Link v-for="item in navigationItems" :key="item.name"
                                :href="item.href === 'dashboard' || item.href === 'customers' ? route(item.href) : item.href"
                                :class="[
                                    item.active
                                        ? 'bg-blue-50 border-r-2 border-blue-600 text-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50',
                                    'group flex items-center px-3 py-2 text-sm font-medium rounded-l-lg'
                                ]">
                            <span class="text-lg mr-3">{{ item.icon }}</span>
                            {{ item.name }}
                            </Link>
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
                            <span class="text-xl font-bold text-gray-900">WallPay</span>
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
                        <span class="text-xl font-bold text-gray-900">WallPay</span>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <ResponsiveNavLink v-for="item in navigationItems" :key="item.name"
                            :href="item.href === 'dashboard' || item.href === 'customers' ? route(item.href) : item.href"
                            :active="item.active">
                            <span class="text-lg mr-3">{{ item.icon }}</span>
                            {{ item.name }}
                        </ResponsiveNavLink>
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
