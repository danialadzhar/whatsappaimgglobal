<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

// Check kalau user login atau tidak
const page = usePage();
// const isAuthenticated = computed(() => page.props.auth?.user != null);
const user = computed(() => page.props.auth?.user);

const showMobileMenu = ref(false);
const showUserDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation Bar -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-2 mr-8">
                        <Link :href="route('ecommerce.index')" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 hidden sm:inline">Shop | MG Global HQ</span>
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center gap-6">
                        <Link :href="route('ecommerce.index')"
                            class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        Products
                        </Link>
                        <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Categories
                        </a>
                        <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Deals
                        </a>
                    </div>

                    <!-- Spacer untuk push right side ke kanan -->
                    <div class="flex-1"></div>

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <!-- Cart Icon -->
                        <button class="relative p-2 text-gray-700 hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span
                                class="absolute -top-1 -right-1 bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                0
                            </span>
                        </button>

                        <!-- Mobile Menu Button -->
                        <button @click="showMobileMenu = !showMobileMenu"
                            class="md:hidden p-2 text-gray-700 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div v-show="showMobileMenu" class="md:hidden border-t border-gray-200 py-4">
                    <Link :href="route('ecommerce.index')"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Products
                    </Link>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Categories
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        Deals
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4">E-Shop</h3>
                        <p class="text-sm text-gray-600">Your trusted online store for quality products.</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Shop</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="#" class="hover:text-blue-600">Products</a></li>
                            <li><a href="#" class="hover:text-blue-600">Categories</a></li>
                            <li><a href="#" class="hover:text-blue-600">Deals</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Support</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="#" class="hover:text-blue-600">Contact Us</a></li>
                            <li><a href="#" class="hover:text-blue-600">FAQ</a></li>
                            <li><a href="#" class="hover:text-blue-600">Shipping</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Legal</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="#" class="hover:text-blue-600">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-blue-600">Terms of Service</a></li>
                            <li><a href="#" class="hover:text-blue-600">Return Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-600">
                    <p>&copy; {{ new Date().getFullYear() }} E-Shop. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Smooth transitions */
* {
    transition: background-color 0.2s ease, color 0.2s ease;
}
</style>
