<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

// State untuk motion effects
const showContent = ref(false);
const floatingElements = ref([]);

// Generate floating elements for background animation
onMounted(() => {
    showContent.value = true;
    
    // Generate random floating elements
    for (let i = 0; i < 20; i++) {
        floatingElements.value.push({
            id: i,
            top: Math.random() * 100 + '%',
            left: Math.random() * 100 + '%',
            delay: Math.random() * 5 + 's',
            duration: (Math.random() * 10 + 10) + 's',
            size: Math.random() * 4 + 2 + 'rem',
            opacity: Math.random() * 0.1 + 0.05
        });
    }
});
</script>

<template>
    <Head title="404 - Page Not Found" />
    
    <!-- Background dengan gradient dan floating elements -->
    <div class="min-h-screen bg-gradient-to-br from-purple-100 via-pink-50 to-indigo-100 overflow-hidden relative">
        <!-- Floating background elements -->
        <div 
            v-for="element in floatingElements"
            :key="element.id"
            class="absolute rounded-full bg-white animate-pulse"
            :style="{
                top: element.top,
                left: element.left,
                width: element.size,
                height: element.size,
                opacity: element.opacity,
                animationDelay: element.delay,
                animationDuration: element.duration
            }"
        ></div>
        
        <!-- Main content container -->
        <div class="relative flex min-h-screen flex-col items-center justify-center px-6 text-center">
            <!-- 404 Error Content -->
            <div 
                class="max-w-md w-full transform transition-all duration-1000 ease-out"
                :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'"
            >
                <!-- 404 Number dengan motion effect -->
                <div class="mb-8">
                    <h1 class="text-8xl md:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600 animate-bounce">
                        404
                    </h1>
                </div>
                
                <!-- Geometric Icon dengan rotation animation -->
                <div class="flex justify-center mb-8">
                    <div class="relative animate-spin" style="animation-duration: 8s;">
                        <!-- Green Triangle -->
                        <div class="w-16 h-16 bg-green-400 rounded-xl transform rotate-45 absolute -top-2 -left-2 animate-pulse"></div>
                        <!-- Purple Triangle -->
                        <div class="w-16 h-16 bg-purple-400 rounded-xl transform rotate-45 animate-pulse" style="animation-delay: 0.5s;"></div>
                    </div>
                </div>
                
                <!-- Error Message -->
                <div class="mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                        Oops! Page Not Found
                    </h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        The page you're looking for doesn't exist. It might have been moved, deleted, or you entered the wrong URL.
                    </p>
                </div>
                
                <!-- Animated illustration -->
                <div class="mb-8 flex justify-center">
                    <div class="relative">
                        <!-- Search icon dengan motion -->
                        <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center animate-pulse">
                            <svg class="w-12 h-12 text-gray-400 animate-bounce" style="animation-delay: 1s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0120 12a8 8 0 11-16 0 8 8 0 018-8v.01z"></path>
                            </svg>
                        </div>
                        
                        <!-- Floating question marks -->
                        <div class="absolute -top-2 -right-2 text-2xl animate-bounce" style="animation-delay: 0.5s;">❓</div>
                        <div class="absolute -bottom-2 -left-2 text-xl animate-bounce" style="animation-delay: 1.5s;">❓</div>
                    </div>
                </div>
                
                <!-- Decorative elements -->
                <div class="flex justify-center space-x-4 mb-8">
                    <div class="w-3 h-3 bg-purple-400 rounded-full animate-pulse"></div>
                    <div class="w-3 h-3 bg-pink-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                    <div class="w-3 h-3 bg-indigo-400 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                </div>
                
                <!-- Helpful message -->
                <div class="text-sm text-gray-500">
                    <p class="mb-2">💡 Try checking the URL or contact the administrator</p>
                    <p class="opacity-75">Error Code: 404</p>
                </div>
            </div>
            
            <!-- Subtle footer -->
            <div 
                class="absolute bottom-8 text-center transform transition-all duration-1000 ease-out delay-500"
                :class="showContent ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            >
                <p class="text-sm text-gray-400">WhatsApp AI Chatbot System</p>
            </div>
        </div>
        
        <!-- Background gradient overlay untuk depth -->
        <div class="absolute inset-0 bg-gradient-to-t from-white/20 to-transparent pointer-events-none"></div>
    </div>
</template>
