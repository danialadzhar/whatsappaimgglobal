<script setup>
import { ref, onMounted, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    settings: Object,
    chatbotActive: Boolean,
});

// AI Activation state
const chatbotActive = ref(props.chatbotActive || false);
const isLoading = ref(false);
const lastUpdated = ref(null);

// Countdown settings state (init dari props.settings jika ada)
const countdownEnabled = ref(props.settings?.countdown_enabled ?? true);
const countdownDays = ref(props.settings?.countdown_days ?? 3);
const countdownHours = ref(props.settings?.countdown_hours ?? 11);
const countdownMinutes = ref(props.settings?.countdown_minutes ?? 31);
const urgencyText = ref(props.settings?.urgency_text ?? '🔥 TAWARAN TERHAD! Promosi Ansuran Berakhir Dalam:');
const backgroundColor = ref(props.settings?.background_color ?? '#1f2937');

// Preview state
const showPreview = ref(false);

// Computed properties untuk text yang panjang
const chatbotStatusText = computed(() => {
    return chatbotActive.value
        ? 'The AI Chatbot is active and can respond to customer messages'
        : 'The AI Chatbot is not active - customers will not receive automatic replies';
});

const chatbotInfoText = computed(() => {
    return chatbotActive.value
        ? 'Customers will automatically receive replies from the AI chatbot via WhatsApp.'
        : 'Customers will not receive automatic replies. Activate the chatbot for 24/7 service.';
});

// Toggle chatbot status
const toggleChatbot = async () => {
    isLoading.value = true;
    try {
        const response = await axios.post('/api/settings/chatbot-toggle', {
            active: !chatbotActive.value
        });

        if (response.data.success) {
            chatbotActive.value = response.data.chatbot_active;
            lastUpdated.value = response.data.last_updated;

            // Show success message
            alert(response.data.message);
        }
    } catch (error) {
        console.error('Error toggling chatbot:', error);
        alert('Failed to update chatbot status');
    } finally {
        isLoading.value = false;
    }
};

// Get current chatbot status
const getChatbotStatus = async () => {
    try {
        const response = await axios.get('/api/settings/chatbot-status');
        if (response.data.success) {
            chatbotActive.value = response.data.chatbot_active;
            lastUpdated.value = response.data.last_updated;
        }
    } catch (error) {
        console.error('Error getting chatbot status:', error);
    }
};

const saveSettings = async () => {
    try {
        isLoading.value = true;
        const response = await axios.post('/api/settings/ecommerce', {
            countdown_enabled: countdownEnabled.value,
            countdown_days: countdownDays.value,
            countdown_hours: countdownHours.value,
            countdown_minutes: countdownMinutes.value,
            urgency_text: urgencyText.value,
            background_color: backgroundColor.value,
        });
        if (response.data.success) {
            alert(response.data.message);
        } else {
            alert('Gagal menyimpan settings');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        alert('Gagal menyimpan settings');
    } finally {
        isLoading.value = false;
    }
};

const resetToDefault = () => {
    countdownEnabled.value = true;
    countdownDays.value = 3;
    countdownHours.value = 11;
    countdownMinutes.value = 31;
    urgencyText.value = '🔥 TAWARAN TERHAD! Promosi Ansuran Berakhir Dalam:';
    backgroundColor.value = '#1f2937';
};

// Load chatbot status on mount
onMounted(() => {
    getChatbotStatus();
});
</script>

<template>
    <div>

        <Head title="Settings" />

        <AuthenticatedLayout>
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
                        <p class="mt-2 text-sm text-gray-600">Manage your WhatsApp AI chatbot and e-commerce settings
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Settings Form -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- AI Chatbot Settings Card -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-lg font-semibold text-gray-900">🤖 AI Chatbot Settings</h2>
                                    <p class="text-sm text-gray-500 mt-1">Control your WhatsApp AI chatbot activation
                                    </p>
                                </div>

                                <div class="p-6 space-y-6">
                                    <!-- Chatbot Status Toggle -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Chatbot Status</label>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ chatbotStatusText }}
                                            </p>
                                            <p v-if="lastUpdated" class="text-xs text-gray-400 mt-1">
                                                Last updated: {{ lastUpdated }}
                                            </p>
                                        </div>
                                        <button @click="toggleChatbot" :disabled="isLoading" :class="[
                                            chatbotActive ? 'bg-green-600' : 'bg-gray-200',
                                            isLoading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-opacity-80'
                                        ]"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            <span :class="chatbotActive ? 'translate-x-6' : 'translate-x-1'"
                                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" />
                                        </button>
                                    </div>

                                    <!-- Status Indicator -->
                                    <div class="flex items-center gap-2">
                                        <div :class="[
                                            chatbotActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                        ]" class="px-3 py-1 rounded-full text-xs font-medium">
                                            {{ chatbotActive ? '🟢 Active' : '🔴 Inactive' }}
                                        </div>
                                        <span v-if="isLoading" class="text-xs text-gray-500">Sedang mengemas
                                            kini...</span>
                                    </div>

                                    <div class="border-t border-gray-200"></div>

                                    <!-- Info Box -->
                                    <div :class="[
                                        chatbotActive ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200'
                                    ]" class="border rounded-lg p-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <span class="text-lg">{{ chatbotActive ? '✅' : '⚠️' }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium"
                                                    :class="chatbotActive ? 'text-green-800' : 'text-yellow-800'">
                                                    {{ chatbotActive ? 'Chatbot AI Active' : 'Chatbot AI Inactive' }}
                                                </h3>
                                                <p class="text-xs mt-1"
                                                    :class="chatbotActive ? 'text-green-700' : 'text-yellow-700'">
                                                    {{ chatbotInfoText }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- E-Commerce Settings Card -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-lg font-semibold text-gray-900">🛒 E-Commerce Settings</h2>
                                    <p class="text-sm text-gray-500 mt-1">Configure the countdown timer displayed on
                                        your store</p>
                                </div>

                                <div class="p-6 space-y-6">
                                    <!-- Enable/Disable Toggle -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Enable Countdown</label>
                                            <p class="text-xs text-gray-500 mt-1">Show countdown timer on storefront</p>
                                        </div>
                                        <button @click="countdownEnabled = !countdownEnabled"
                                            :class="countdownEnabled ? 'bg-blue-600' : 'bg-gray-200'"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                            <span :class="countdownEnabled ? 'translate-x-6' : 'translate-x-1'"
                                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" />
                                        </button>
                                    </div>

                                    <div class="border-t border-gray-200"></div>

                                    <!-- Countdown Duration -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Countdown
                                            Duration</label>
                                        <div class="grid grid-cols-3 gap-4">
                                            <!-- Days -->
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-2">Days</label>
                                                <input v-model.number="countdownDays" type="number" min="0" max="365"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    :disabled="!countdownEnabled" />
                                            </div>

                                            <!-- Hours -->
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-2">Hours</label>
                                                <input v-model.number="countdownHours" type="number" min="0" max="23"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    :disabled="!countdownEnabled" />
                                            </div>

                                            <!-- Minutes -->
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-2">Minutes</label>
                                                <input v-model.number="countdownMinutes" type="number" min="0" max="59"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    :disabled="!countdownEnabled" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200"></div>

                                    <!-- Urgency Text -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Urgency
                                            Message</label>
                                        <textarea v-model="urgencyText" rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                            placeholder="Enter urgency message..."
                                            :disabled="!countdownEnabled"></textarea>
                                        <p class="text-xs text-gray-500 mt-1">Tip: Use emojis to make it more engaging!
                                            🔥⚡✨
                                        </p>
                                    </div>

                                    <div class="border-t border-gray-200"></div>

                                    <!-- Background Color -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Background
                                            Color</label>
                                        <div class="flex items-center gap-3">
                                            <input v-model="backgroundColor" type="color"
                                                class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                                                :disabled="!countdownEnabled" />
                                            <input v-model="backgroundColor" type="text"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="#1f2937" :disabled="!countdownEnabled" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between">
                                <button @click="resetToDefault"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    Reset to Default
                                </button>
                                <div class="flex gap-3">
                                    <button @click="showPreview = !showPreview"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        {{ showPreview ? 'Hide Preview' : 'Show Preview' }}
                                    </button>
                                    <button @click="saveSettings"
                                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Panel -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">Preview</h3>
                                    <p class="text-xs text-gray-500 mt-1">Live preview of your countdown timer</p>
                                </div>

                                <div class="p-6">
                                    <div v-if="countdownEnabled" class="space-y-4">
                                        <!-- Preview Countdown -->
                                        <div :style="{ backgroundColor: backgroundColor }"
                                            class="rounded-lg p-4 text-white">
                                            <div class="space-y-3">
                                                <!-- Urgency Text -->
                                                <p class="text-xs font-medium text-center break-words">
                                                    {{ urgencyText }}
                                                </p>

                                                <!-- Countdown Display -->
                                                <div class="flex items-center justify-center gap-2">
                                                    <!-- Days -->
                                                    <div
                                                        class="flex flex-col items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                        <div class="text-lg font-bold">{{
                                                            String(countdownDays).padStart(2,
                                                                '0') }}</div>
                                                        <div class="text-[0.5rem] text-gray-300 uppercase">Hari</div>
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-400">:</span>

                                                    <!-- Hours -->
                                                    <div
                                                        class="flex flex-col items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                        <div class="text-lg font-bold">{{
                                                            String(countdownHours).padStart(2,
                                                                '0') }}</div>
                                                        <div class="text-[0.5rem] text-gray-300 uppercase">Jam</div>
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-400">:</span>

                                                    <!-- Minutes -->
                                                    <div
                                                        class="flex flex-col items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                        <div class="text-lg font-bold">{{
                                                            String(countdownMinutes).padStart(2, '0') }}</div>
                                                        <div class="text-[0.5rem] text-gray-300 uppercase">Min</div>
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-400">:</span>

                                                    <!-- Seconds -->
                                                    <div
                                                        class="flex flex-col items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                        <div class="text-lg font-bold">00</div>
                                                        <div class="text-[0.5rem] text-gray-300 uppercase">Saat</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Info -->
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <p class="text-xs text-blue-800">
                                                <span class="font-semibold">Note:</span> This is a static preview. The
                                                actual countdown will be dynamic on your storefront.
                                            </p>
                                        </div>
                                    </div>

                                    <div v-else class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Countdown is disabled</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>

<style scoped>
/* Custom styles for disabled state */
input:disabled,
textarea:disabled {
    background-color: #f9fafb;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Smooth transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
