<script setup>
import { onMounted, onUnmounted } from 'vue';

// Props untuk modal
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    maxWidth: {
        type: String,
        default: '2xl'
    },
    closeable: {
        type: Boolean,
        default: true
    }
});

// Emits untuk modal events
const emit = defineEmits(['close']);

// Close modal function
const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

// Close modal dengan ESC key
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

// Handle click outside modal
const handleClickOutside = (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        close();
    }
};

// Add event listeners
onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
});

// Max width classes
const maxWidthClass = {
    'sm': 'sm:max-w-sm',
    'md': 'sm:max-w-md',
    'lg': 'sm:max-w-lg',
    'xl': 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
    '4xl': 'sm:max-w-4xl',
    '5xl': 'sm:max-w-5xl',
    '6xl': 'sm:max-w-6xl',
    '7xl': 'sm:max-w-7xl'
};
</script>

<template>
    <teleport to="body">
        <transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" scroll-region>
                <div class="fixed inset-0 transform transition-all modal-overlay" @click="handleClickOutside">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto"
                    :class="maxWidthClass[maxWidth]">
                    <slot />
                </div>
            </div>
        </transition>
    </teleport>
</template>