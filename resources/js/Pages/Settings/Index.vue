<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
});

// Countdown settings state
const countdownEnabled = ref(true);
const countdownDays = ref(3);
const countdownHours = ref(11);
const countdownMinutes = ref(31);
const urgencyText = ref('🔥 TAWARAN TERHAD! Promosi Ansuran Berakhir Dalam:');
const backgroundColor = ref('#1f2937');

// Preview state
const showPreview = ref(false);

const saveSettings = () => {
    // Logic akan ditambah kemudian
    console.log('Saving settings...');
};

const resetToDefault = () => {
    countdownEnabled.value = true;
    countdownDays.value = 3;
    countdownHours.value = 11;
    countdownMinutes.value = 31;
    urgencyText.value = '🔥 TAWARAN TERHAD! Promosi Ansuran Berakhir Dalam:';
    backgroundColor.value = '#1f2937';
};
</script>

<template>

    <Head title="E-Commerce Settings" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">E-Commerce Settings</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage your e-commerce store settings and countdown timer
                        configuration</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Settings Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Countdown Timer Settings Card -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Countdown Timer</h2>
                                <p class="text-sm text-gray-500 mt-1">Configure the countdown timer displayed on your
                                    store</p>
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Message</label>
                                    <textarea v-model="urgencyText" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Enter urgency message..." :disabled="!countdownEnabled"></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Tip: Use emojis to make it more engaging! 🔥⚡✨
                                    </p>
                                </div>

                                <div class="border-t border-gray-200"></div>

                                <!-- Background Color -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
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
                                                    <div class="text-lg font-bold">{{ String(countdownDays).padStart(2,
                                                        '0') }}</div>
                                                    <div class="text-[0.5rem] text-gray-300 uppercase">Hari</div>
                                                </div>
                                                <span class="text-sm font-bold text-gray-400">:</span>

                                                <!-- Hours -->
                                                <div
                                                    class="flex flex-col items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                    <div class="text-lg font-bold">{{ String(countdownHours).padStart(2,
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
