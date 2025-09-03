<script setup>
// Emit untuk handle customer service button click
const emit = defineEmits(['contact-customer-service']);

// Handle customer service button click
const handleContactCustomerService = () => {
    emit('contact-customer-service');
};
</script>

<template>
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Not finding the answer you're looking for?
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>
                        Contact our customer service team for more help.
                        We are ready to help you 24/7 via WhatsApp or email.
                    </p>
                </div>
                <div class="mt-4">
                    <button @click="handleContactCustomerService"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Contact Customer Service
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
