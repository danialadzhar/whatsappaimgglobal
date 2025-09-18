<template>
    <div class="flex flex-col h-full bg-white">
        <!-- Chat Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
            <div class="flex items-center">
                <!-- Avatar -->
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium text-sm mr-3"
                    :style="{ backgroundColor: getAvatarColor(conversation.name) }">
                    {{ conversation.avatar }}
                </div>

                <!-- User Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ conversation.name }}</h3>
                    <div class="flex items-center space-x-2">
                        <p class="text-sm text-gray-500">{{ conversation.phone_number }}</p>
                        <div class="w-2 h-2 rounded-full" :class="{
                            'bg-green-400': conversation.status === 'online',
                            'bg-gray-400': conversation.status === 'offline',
                            'bg-yellow-400': conversation.status === 'away'
                        }">
                        </div>
                        <span class="text-xs text-gray-500 capitalize">{{ conversation.status }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg>
                </button>
                <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto chat-scroll p-4 bg-gray-50" ref="messagesContainer">
            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center h-full">
                <div class="flex items-center space-x-2 text-gray-500">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>Loading messages...</span>
                </div>
            </div>

            <!-- Messages -->
            <div v-else class="space-y-4">
                <div v-if="messages.length === 0" class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-gray-500">No messages yet. Start the conversation!</p>
                </div>

                <!-- Message Items -->
                <div v-for="message in messages" :key="message.id" class="flex" :class="{
                    'justify-end': message.sender === 'ai',
                    'justify-start': message.sender === 'customer'
                }">

                    <!-- Customer Message -->
                    <div v-if="message.sender === 'customer'" class="flex items-start space-x-2 max-w-xs lg:max-w-md">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-medium text-xs"
                            :style="{ backgroundColor: getAvatarColor(message.sender_name) }">
                            {{ getInitials(message.sender_name) }}
                        </div>
                        <div>
                            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border">
                                <p class="text-sm text-gray-900">{{ message.text }}</p>
                            </div>
                            <div class="flex items-center mt-1 space-x-2">
                                <span class="text-xs text-gray-500">{{ message.time }}</span>
                                <span class="text-xs text-gray-400">via SMS</span>
                            </div>
                        </div>
                    </div>

                    <!-- AI Message -->
                    <div v-else class="flex items-start space-x-2 max-w-xs lg:max-w-md">
                        <div>
                            <div class="bg-blue-500 text-white rounded-lg px-4 py-2 shadow-sm">
                                <p class="text-sm">{{ message.text }}</p>
                            </div>
                            <div class="flex items-center justify-end mt-1 space-x-2">
                                <span class="text-xs text-gray-500">{{ message.time }}</span>
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div
                            class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium text-xs">
                            🤖
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div v-if="isTyping" class="flex justify-start">
                    <div class="flex items-start space-x-2 max-w-xs">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium text-xs">
                            🤖
                        </div>
                        <div class="bg-gray-200 rounded-lg px-4 py-2">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"
                                    style="animation-delay: 0.1s;"></div>
                                <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"
                                    style="animation-delay: 0.2s;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <MessageInput @send-message="handleSendMessage" :disabled="loading" />
    </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue';
import MessageInput from '@/Components/Chat/MessageInput.vue';

// Props
const props = defineProps({
    conversation: {
        type: Object,
        required: true
    },
    messages: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

// Emits
const emit = defineEmits(['send-message']);

// State
const messagesContainer = ref(null);
const isTyping = ref(false);

// Handle send message
const handleSendMessage = async (messageData) => {
    emit('send-message', messageData);

    // Show typing indicator hanya untuk customer messages
    if (messageData.sender === 'customer') {
        isTyping.value = true;
        await nextTick();
        scrollToBottom();

        // Hide typing indicator after delay
        setTimeout(() => {
            isTyping.value = false;
        }, 2000);
    } else {
        // Untuk AI messages, scroll sahaja tanpa typing indicator
        await nextTick();
        scrollToBottom();
    }
};

// Scroll to bottom
const scrollToBottom = async () => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

// Watch messages untuk auto scroll
watch(() => props.messages, () => {
    scrollToBottom();
}, { deep: true });

// Generate avatar color
const getAvatarColor = (name) => {
    const colors = [
        '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
        '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
    ];

    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    return colors[Math.abs(hash) % colors.length];
};

// Get initials from name
const getInitials = (name) => {
    const words = name.split(' ');
    let initials = '';

    for (let word of words) {
        initials += word.charAt(0).toUpperCase();
        if (initials.length >= 2) break;
    }

    return initials || name.charAt(0).toUpperCase();
};
</script>

<style scoped>
/* Custom scrollbar */
.chat-scroll {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.chat-scroll::-webkit-scrollbar {
    width: 6px;
}

.chat-scroll::-webkit-scrollbar-track {
    background: #f7fafc;
}

.chat-scroll::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}

.chat-scroll::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}

/* Message animations */
.message-enter-active,
.message-leave-active {
    transition: all 0.3s ease;
}

.message-enter-from,
.message-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
