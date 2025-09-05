<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <!-- Gradient Background -->
        <div
            class="min-h-screen bg-gradient-to-br from-pink-100 via-purple-50 to-white flex items-center justify-center p-4">
            <!-- Main Register Card -->
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl p-8 relative">
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
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Join WhatsApp AI</h1>
                        <p class="text-gray-600 text-sm">Create your account to get started</p>
                    </div>

                    <!-- Register Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name Field -->
                        <div>
                            <TextInput id="name" type="text"
                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                                v-model="form.name" placeholder="Full Name" required autofocus autocomplete="name" />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Email Field -->
                        <div>
                            <TextInput id="email" type="email"
                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                                v-model="form.email" placeholder="Email Address" required autocomplete="username" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <TextInput id="password" type="password"
                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                                v-model="form.password" placeholder="Password" required autocomplete="new-password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Confirm Password Field -->
                        <div>
                            <TextInput id="password_confirmation" type="password"
                                class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                                v-model="form.password_confirmation" placeholder="Confirm Password" required
                                autocomplete="new-password" />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Register Button -->
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
                                    Creating account...
                                </span>
                                <span v-else>Create Account</span>
                            </PrimaryButton>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center pt-4">
                            <Link :href="route('login')"
                                class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200">
                            Already have an account? Sign in
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
