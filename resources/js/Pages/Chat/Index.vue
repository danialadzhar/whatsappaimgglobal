<template>

    <Head title="Chat" />

    <AuthenticatedLayout>
        <div class="h-screen flex bg-gray-50">
            <!-- Chat Sidebar -->
            <ChatSidebar :conversations="conversations" :selected-conversation="selectedConversation"
                :search-query="searchQuery" @select-conversation="selectConversation" @update-search="updateSearch" />

            <!-- Chat Main Area -->
            <div class="flex-1 flex flex-col">
                <ChatWindow v-if="selectedConversation" :conversation="selectedConversation" :messages="messages"
                    :loading="loading" @send-message="sendMessage" />

                <!-- Empty State -->
                <div v-else class="flex-1 flex items-center justify-center bg-white">
                    <div class="text-center">
                        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Select a conversation</h3>
                        <p class="text-gray-500">Choose a conversation from the sidebar to start chatting</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChatSidebar from '@/Components/Chat/ChatSidebar.vue';
import ChatWindow from '@/Components/Chat/ChatWindow.vue';
import axios from 'axios';

// Props dari controller
const props = defineProps({
    conversations: {
        type: Array,
        default: () => []
    }
});

// State management
const selectedConversation = ref(null);
const messages = ref([]);
const searchQuery = ref('');
const loading = ref(false);

// Pilih conversation
const selectConversation = async (conversation) => {
    selectedConversation.value = conversation;
    loading.value = true;

    try {
        const response = await axios.get(`/api/chat/messages/${conversation.id}`);
        if (response.data.success) {
            // Flatten messages untuk display (ambil yang wujud sahaja)
            const flatMessages = [];
            response.data.messages.forEach(msgGroup => {
                if (msgGroup.customer_message?.text) {
                    flatMessages.push(msgGroup.customer_message);
                }
                if (msgGroup.ai_message?.text) {
                    flatMessages.push(msgGroup.ai_message);
                }
            });
            messages.value = flatMessages;
        }
    } catch (error) {
        console.error('Error loading messages:', error);
        messages.value = [];
    } finally {
        loading.value = false;
    }
};

// Update search query
const updateSearch = (query) => {
    searchQuery.value = query;
};

// Send message
const sendMessage = async (messageData) => {
    if (!selectedConversation.value) return;

    try {
        const response = await axios.post('/api/chat/send', {
            customer_id: selectedConversation.value.id,
            message: messageData.text,
            sender: messageData.sender
        });

        if (response.data.success) {
            // Server kini pulang 1 mesej sahaja di data.message
            if (response.data.data?.message?.text) {
                messages.value.push(response.data.data.message);
            }
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
};

// Auto-select first conversation jika ada
onMounted(() => {
    if (props.conversations.length > 0) {
        selectConversation(props.conversations[0]);
    }
});
</script>

<style scoped>
/* Custom scrollbar untuk chat */
:deep(.chat-scroll) {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

:deep(.chat-scroll)::-webkit-scrollbar {
    width: 6px;
}

:deep(.chat-scroll)::-webkit-scrollbar-track {
    background: #f7fafc;
}

:deep(.chat-scroll)::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}

:deep(.chat-scroll)::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}
</style>
