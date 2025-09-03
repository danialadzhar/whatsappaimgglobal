<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <!-- Gradient Background -->
        <div
            class="min-h-screen bg-gradient-to-br from-pink-100 via-purple-50 to-white flex items-center justify-center p-4">
            <!-- Main Login Card -->
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl p-8 relative">
                    <!-- Status Message -->
                    <div v-if="status" class="mb-6 text-sm font-medium text-green-600 text-center">
                        {{ status }}
                    </div>

                    <!-- Logo Section -->
                    <div class="text-center mb-8">
                        <!-- Logo Icon -->
                        <div class="flex justify-center mb-4">
                            <div class="relative">
                                <!-- Green Triangle -->
                                <div
                                    class="w-12 h-12 bg-green-400 rounded-lg transform rotate-45 absolute -top-1 -left-1">
                                </div>
                                <!-- Purple Triangle -->
                                <div class="w-12 h-12 bg-purple-400 rounded-lg transform rotate-45"></div>
                            </div>
                        </div>

                        <!-- Title -->
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome to WhatsApp AI</h1>
                        <p class="text-gray-600 text-sm">Log in to your account</p>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email Field -->
                        <div>
                            <TextInput id="email" type="email"
                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                                v-model="form.email" placeholder="Email" required autofocus autocomplete="username" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <TextInput id="password" type="password"
                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                                v-model="form.password" placeholder="Password" required
                                autocomplete="current-password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>

                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200">
                            Forgot password?
                            </Link>
                        </div>

                        <!-- Login Button -->
                        <div class="pt-2">
                            <PrimaryButton
                                class="w-full py-3 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-xl transition-all duration-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing">
                                <span v-if="form.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Logging in...
                                </span>
                                <span v-else>Login</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
