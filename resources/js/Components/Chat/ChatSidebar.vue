<template>
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Messages</h2>

            <!-- Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input v-model="localSearchQuery" type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search conversations...">
            </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto chat-scroll">
            <div v-if="filteredConversations.length === 0" class="p-4 text-center text-gray-500">
                <div class="mb-2">
                    <svg class="w-8 h-8 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <p class="text-sm">No conversations found</p>
            </div>

            <div v-else>
                <div v-for="conversation in filteredConversations" :key="conversation.id"
                    class="flex items-center p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-150 border-l-4"
                    :class="{
                        'bg-blue-50 border-l-blue-500': selectedConversation && selectedConversation.id === conversation.id,
                        'border-l-transparent': !selectedConversation || selectedConversation.id !== conversation.id
                    }" @click="selectConversation(conversation)">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-medium text-sm"
                            :style="{ backgroundColor: getAvatarColor(conversation.name) }">
                            {{ conversation.avatar }}
                        </div>
                    </div>

                    <!-- Conversation Info -->
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ conversation.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ conversation.last_message_time }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between mt-1">
                            <p class="text-sm text-gray-600 truncate">
                                {{ conversation.last_message || 'No messages yet' }}
                            </p>

                            <!-- Unread Count -->
                            <span v-if="conversation.unread_count > 0"
                                class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ conversation.unread_count }}
                            </span>
                        </div>

                        <!-- Phone Number -->
                        <p class="text-xs text-gray-400 mt-1">
                            {{ conversation.phone_number }}
                        </p>

                        <!-- Typing Indicator -->
                        <div v-if="conversation.is_typing" class="flex items-center mt-1">
                            <div class="flex space-x-1">
                                <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"></div>
                                <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"
                                    style="animation-delay: 0.1s;"></div>
                                <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"
                                    style="animation-delay: 0.2s;"></div>
                            </div>
                            <span class="text-xs text-blue-500 ml-2">typing...</span>
                        </div>
                    </div>

                    <!-- Status Indicator -->
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-3 h-3 rounded-full" :class="{
                            'bg-green-400': conversation.status === 'online',
                            'bg-gray-400': conversation.status === 'offline',
                            'bg-yellow-400': conversation.status === 'away'
                        }">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Stats -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>{{ filteredConversations.length }} conversations</span>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <span>{{ onlineCount }} online</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

// Props
const props = defineProps({
    conversations: {
        type: Array,
        default: () => []
    },
    selectedConversation: {
        type: Object,
        default: null
    },
    searchQuery: {
        type: String,
        default: ''
    }
});

// Emits
const emit = defineEmits(['select-conversation', 'update-search']);

// Local state
const localSearchQuery = ref(props.searchQuery);

// Watch search query untuk emit ke parent
watch(localSearchQuery, (newValue) => {
    emit('update-search', newValue);
});

// Filtered conversations berdasarkan search
const filteredConversations = computed(() => {
    if (!localSearchQuery.value.trim()) {
        return props.conversations;
    }

    const query = localSearchQuery.value.toLowerCase().trim();
    return props.conversations.filter(conversation =>
        conversation.name.toLowerCase().includes(query) ||
        conversation.phone_number.includes(query) ||
        (conversation.last_message && conversation.last_message.toLowerCase().includes(query))
    );
});

// Count online users
const onlineCount = computed(() => {
    return props.conversations.filter(conv => conv.status === 'online').length;
});

// Select conversation
const selectConversation = (conversation) => {
    emit('select-conversation', conversation);
};

// Generate avatar color berdasarkan nama
const getAvatarColor = (name) => {
    const colors = [
        '#3B82F6', // blue-500
        '#EF4444', // red-500
        '#10B981', // emerald-500
        '#F59E0B', // amber-500
        '#8B5CF6', // violet-500
        '#EC4899', // pink-500
        '#06B6D4', // cyan-500
        '#84CC16', // lime-500
        '#F97316', // orange-500
        '#6366F1'  // indigo-500
    ];

    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    return colors[Math.abs(hash) % colors.length];
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

/* Smooth animations */
.conversation-item {
    transition: all 0.15s ease-in-out;
}
</style>
