<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

// Props dari controller
// Props from controller
const props = defineProps({
    chatbotActive: {
        type: Boolean,
        default: true
    }
});

// State management
// Pengurusan state
const chatbotEnabled = ref(props.chatbotActive);
const isLoading = ref(false);

// Toggle chatbot status
// Toggle status chatbot
const toggleChatbot = async () => {
    if (isLoading.value) return;

    isLoading.value = true;

    try {
        const response = await axios.post('/api/settings/chatbot-toggle', {
            active: !chatbotEnabled.value
        });

        if (response.data.success) {
            chatbotEnabled.value = !chatbotEnabled.value;

            // Show success message
            // Papar mesej berjaya
            if (window.Swal) {
                window.Swal.fire({
                    title: 'Berjaya!',
                    text: response.data.message,
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        }
    } catch (error) {
        console.error('Error toggling chatbot:', error);

        // Show error message
        // Papar mesej error
        if (window.Swal) {
            window.Swal.fire({
                title: 'Error!',
                text: 'Gagal mengemas kini status chatbot',
                icon: 'error',
                confirmButtonColor: '#dc2626'
            });
        }
    } finally {
        isLoading.value = false;
    }
};

// Load current status on mount
// Muat status semasa apabila component dimuat
onMounted(async () => {
    try {
        const response = await axios.get('/api/settings/chatbot-status');
        if (response.data.success) {
            chatbotEnabled.value = response.data.chatbot_active;
        }
    } catch (error) {
        console.error('Error loading chatbot status:', error);
    }
});
</script>

<template>

    <Head title="Settings" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Settings</h1>
                    <p class="text-gray-600">Manage your application settings</p>
                </div>

                <!-- Settings Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                    <div class="p-8">
                        <!-- Chatbot AI Toggle Section -->
                        <div class="flex items-center justify-between py-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    Chatbot AI
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ chatbotEnabled ? 'AI chatbot is currently active and responding to messages' :
                                        'AI chatbot is currently disabled and not responding' }}
                                </p>
                            </div>

                            <!-- Toggle Switch -->
                            <div class="ml-6">
                                <button @click="toggleChatbot" :disabled="isLoading"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="{
                                        'bg-blue-600': chatbotEnabled,
                                        'bg-gray-200': !chatbotEnabled,
                                        'opacity-50 cursor-not-allowed': isLoading
                                    }">
                                    <span class="sr-only">Toggle chatbot</span>
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="{
                                            'translate-x-5': chatbotEnabled,
                                            'translate-x-0': !chatbotEnabled
                                        }">
                                        <!-- Loading spinner when toggling -->
                                        <svg v-if="isLoading"
                                            class="absolute inset-0 h-5 w-5 animate-spin text-gray-400" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Status Indicator -->
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-3 h-3 rounded-full mr-3" :class="{
                                    'bg-green-400 animate-pulse': chatbotEnabled,
                                    'bg-red-400': !chatbotEnabled
                                }"></div>
                                <span class="text-sm font-medium" :class="{
                                    'text-green-700': chatbotEnabled,
                                    'text-red-700': !chatbotEnabled
                                }">
                                    {{ chatbotEnabled ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="text-sm text-gray-500 ml-2">
                                    {{ chatbotEnabled ? '• Ready to respond' : '• Not responding to messages' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">About Chatbot AI</h4>
                            <p class="mt-1 text-sm text-blue-700">
                                When enabled, the AI chatbot will automatically respond to customer messages.
                                When disabled, messages will be received but no automatic responses will be sent.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Additional styles if needed */
/* Gaya tambahan jika diperlukan */
</style>
