<script setup>
import { ref, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

// Props untuk form
const props = defineProps({
    branches: {
        type: Array,
        required: true,
        default: () => []
    },
    isSubmitting: {
        type: Boolean,
        default: false
    },
    countdownTimer: {
        type: Number,
        default: 0
    }
});

// Emits untuk form events
const emit = defineEmits(['close', 'submit']);

// Form data
const form = ref({
    question: '',
    answer: '',
    branch: ''
});

// Form validation
const errors = ref({});

// Computed untuk check if form is valid
const isFormValid = computed(() => {
    return form.value.question.trim() !== '' &&
        form.value.answer.trim() !== '' &&
        form.value.branch !== '';
});

// Reset form
const resetForm = () => {
    form.value = {
        question: '',
        answer: '',
        branch: ''
    };
    errors.value = {};
};

// Close modal
const closeModal = () => {
    resetForm();
    emit('close');
};

// Submit form
const submitForm = async () => {
    if (!isFormValid.value) {
        errors.value = {
            question: form.value.question.trim() === '' ? 'Question is required' : '',
            answer: form.value.answer.trim() === '' ? 'Answer is required' : '',
            branch: form.value.branch === '' ? 'Branch is required' : ''
        };
        return;
    }

    errors.value = {};

    try {
        // Emit form data to parent
        emit('submit', {
            question: form.value.question.trim(),
            answer: form.value.answer.trim(),
            branch: form.value.branch
        });

        // Reset form after successful submission
        resetForm();
    } catch (error) {
        console.error('Error submitting FAQ:', error);
        errors.value = { general: 'An error occurred while saving FAQ' };
    }
};
</script>

<template>
    <div class="bg-white">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    Add New FAQ
                </h3>
                <button @click="closeModal"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- General Error -->
                <div v-if="errors.general" class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800">{{ errors.general }}</p>
                        </div>
                    </div>
                </div>

                <!-- Question Input -->
                <div>
                    <InputLabel for="question" value="Question" />
                    <TextInput id="question" v-model="form.question" type="text" class="mt-1 block w-full"
                        placeholder="Enter FAQ question..."
                        :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.question }" />
                    <InputError :message="errors.question" class="mt-2" />
                </div>

                <!-- Answer Input -->
                <div>
                    <InputLabel for="answer" value="Answer" />
                    <textarea id="answer" v-model="form.answer" rows="4"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.answer }"
                        placeholder="Enter answer for this question..."></textarea>
                    <InputError :message="errors.answer" class="mt-2" />
                </div>

                <!-- Branch Select -->
                <div>
                    <InputLabel for="branch" value="Branch" />
                    <select id="branch" v-model="form.branch"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.branch }">
                        <option value="">Select branch...</option>
                        <option value="BERTAM">Bertam</option>
                        <option value="PADANG_SERAI">Padang Serai</option>
                        <option value="IPOH">Ipoh</option>
                    </select>
                    <InputError :message="errors.branch" class="mt-2" />
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
            <SecondaryButton @click="closeModal" :disabled="isSubmitting">
                Cancel
            </SecondaryButton>
            <PrimaryButton @click="submitForm" :disabled="!isFormValid || isSubmitting"
                :class="{ 'opacity-50 cursor-not-allowed': !isFormValid || isSubmitting }">
                <span v-if="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span v-if="countdownTimer > 0">Saving... ({{ countdownTimer }}s)</span>
                    <span v-else>Saving...</span>
                </span>
                <span v-else>
                    Save FAQ
                </span>
            </PrimaryButton>
        </div>
    </div>
</template>
