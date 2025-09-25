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
import { ref, onMounted, nextTick, onUnmounted } from 'vue';
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
const POLLING_INTERVAL_MS = 2000;
let pollingTimer = null;
let notificationAudio = null;
let isAudioUnlocked = false;
let audioListenersAttached = false;

const detachAudioUnlockListeners = () => {
    if (!audioListenersAttached) return;
    document.removeEventListener('click', unlockAudioOnInteraction);
    document.removeEventListener('keydown', unlockAudioOnInteraction);
    audioListenersAttached = false;
};

const attachAudioUnlockListeners = () => {
    if (audioListenersAttached || isAudioUnlocked) return;
    document.addEventListener('click', unlockAudioOnInteraction, { once: false });
    document.addEventListener('keydown', unlockAudioOnInteraction, { once: false });
    audioListenersAttached = true;
};

function unlockAudioOnInteraction() {
    if (!notificationAudio) return;

    notificationAudio
        .play()
        .then(() => {
            notificationAudio.pause();
            notificationAudio.currentTime = 0;
            isAudioUnlocked = true;
            detachAudioUnlockListeners();
        })
        .catch(() => {
            isAudioUnlocked = false;
        });
}

const playNotificationSound = () => {
    if (!notificationAudio) return;

    if (!isAudioUnlocked) {
        attachAudioUnlockListeners();
        return;
    }

    try {
        notificationAudio.currentTime = 0;
        notificationAudio.play().catch((error) => {
            if (error?.name === 'NotAllowedError') {
                isAudioUnlocked = false;
                attachAudioUnlockListeners();
            }
        });
    } catch (error) {
        attachAudioUnlockListeners();
    }
};

// Utility: flatten API response into single array
const flattenMessages = (messageGroups = []) => {
    const flatMessages = [];

    messageGroups
        .filter(Boolean)
        .forEach((msgGroup) => {
            if (msgGroup.customer_message?.text) {
                flatMessages.push(msgGroup.customer_message);
            }
            if (msgGroup.ai_message?.text) {
                flatMessages.push(msgGroup.ai_message);
            }
        });

    return flatMessages;
};

// Utility: check if two message arrays are equal
const areMessagesEqual = (currentMessages, newMessages) => {
    if (currentMessages.length !== newMessages.length) return false;

    for (let index = 0; index < currentMessages.length; index += 1) {
        const current = currentMessages[index];
        const incoming = newMessages[index];

        if (
            current.id !== incoming.id ||
            current.text !== incoming.text ||
            current.sender !== incoming.sender
        ) {
            return false;
        }
    }

    return true;
};

// Fetch messages dari API
const fetchMessages = async (customerId, { showLoading = true } = {}) => {
    if (!customerId) return;

    if (showLoading) {
        loading.value = true;
    }

    try {
        const response = await axios.get(`/api/chat/messages/${customerId}`);

        // Jika user bertukar conversation semasa request, abaikan response
        if (!selectedConversation.value || selectedConversation.value.id !== customerId) {
            return;
        }

        if (response.data.success) {
            const flatMessages = flattenMessages(response.data.messages);

            if (!areMessagesEqual(messages.value, flatMessages)) {
                const previousLength = messages.value.length;
                const nextLength = flatMessages.length;

                messages.value = flatMessages;

                // Only play sound on polling updates (not initial load) and when there's new incoming customer messages
                if (!showLoading && nextLength > previousLength) {
                    const newMessages = flatMessages.slice(previousLength);
                    const hasIncoming = newMessages.some(m => m?.sender === 'customer' && m?.text);
                    if (hasIncoming) {
                        playNotificationSound();
                    }
                }
            }
        }
    } catch (error) {
        console.error('Error loading messages:', error);
        if (showLoading) {
            messages.value = [];
        }
    } finally {
        if (showLoading) {
            loading.value = false;
        }
    }
};

// Hentikan polling sedia ada
const stopPolling = () => {
    if (pollingTimer) {
        clearInterval(pollingTimer);
        pollingTimer = null;
    }
};

// Mulakan polling
const startPolling = (customerId) => {
    stopPolling();

    pollingTimer = setInterval(() => {
        fetchMessages(customerId, { showLoading: false });
    }, POLLING_INTERVAL_MS);
};

// Pilih conversation
const selectConversation = async (conversation) => {
    if (!conversation) return;

    stopPolling();
    selectedConversation.value = conversation;

    await fetchMessages(conversation.id, { showLoading: true });
    startPolling(conversation.id);
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
    // Prepare notification audio (file should be available at /notification_sound/apple-pay-sound.mp3)
    try {
        notificationAudio = new Audio('/notification_sound/apple-pay-sound.mp3');
        notificationAudio.preload = 'auto';
        notificationAudio.volume = 1.0;
        isAudioUnlocked = false;
    } catch (e) {
        notificationAudio = null;
    }

    attachAudioUnlockListeners();

    if (props.conversations.length > 0) {
        selectConversation(props.conversations[0]);
    }
});

onUnmounted(() => {
    stopPolling();
    detachAudioUnlockListeners();
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