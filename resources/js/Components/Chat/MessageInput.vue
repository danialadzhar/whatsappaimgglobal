<template>
    <div class="border-t border-gray-200 bg-white p-4">
        <div class="flex items-center space-x-3">
            <!-- Message Input -->
            <input ref="messageInput" v-model="messageText" @keydown="handleKeydown" :disabled="disabled"
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                placeholder="Type a message..." type="text" />

            <!-- Send Button -->
            <button
                class="flex-shrink-0 p-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                @click="handleSendMessage" :disabled="!canSend || disabled">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

// Props
const props = defineProps({
    disabled: {
        type: Boolean,
        default: false
    }
});

// Emits
const emit = defineEmits(['send-message']);

// State
const messageText = ref('');
const messageInput = ref(null);

// Computed
const canSend = computed(() => {
    return messageText.value.trim().length > 0;
});

// Handle keydown events
const handleKeydown = (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();
        handleSendMessage();
    }
};

// Handle send message
const handleSendMessage = () => {
    if (!canSend.value || props.disabled) return;

    const message = messageText.value.trim();
    if (message) {
        // Hantar sebagai mesej sistem/AI sahaja seperti kehendak
        emit('send-message', {
            text: message,
            sender: 'ai',
            timestamp: new Date().toISOString()
        });

        // Reset form
        messageText.value = '';
    }
};

// Focus input on mount
onMounted(() => {
    messageInput.value?.focus();
});
</script>

<style scoped>
/* Button hover effects */
button:not(:disabled):hover {
    transform: translateY(-1px);
}

button:not(:disabled):active {
    transform: translateY(0);
}
</style>