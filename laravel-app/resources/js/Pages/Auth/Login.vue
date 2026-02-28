<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import loginBg from '../../../images/login-redwood.png';

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
    <div class="min-h-screen flex bg-white dark:bg-[#0a0a0a] transition-colors duration-500 font-sans">
        <Head title="Login | Rumah IT Indonesia" />

        <!-- Left Side: Visual Anchor (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden group">
            <img 
                :src="loginBg" 
                alt="Rumah IT Indonesia Background" 
                class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
            />
            <div class="absolute inset-0 bg-gradient-to-tr from-[#161514]/80 via-transparent to-transparent"></div>
            
            <!-- Floating Brand Element over image -->
            <div class="absolute bottom-12 left-12 right-12 z-10 animate-fade-in-slow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/20">
                         <ApplicationLogo class="w-8 h-8 object-contain" />
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tighter uppercase">
                        Rumah IT <span class="text-brand-rose-vibrant">Indonesia</span>
                    </h1>
                </div>
                <p class="text-white/60 text-lg font-medium leading-relaxed max-w-md">
                    Transformasi Digital Pendidikan Islam Kelas Dunia. Silakan masuk untuk mengelola ekosistem teknologi Anda.
                </p>
            </div>

            <!-- Redwood Organic Curve Overlay -->
            <div class="absolute bottom-0 right-0 w-1/2 h-full pointer-events-none opacity-20">
                <svg class="w-full h-full text-brand-rose" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path fill="currentColor" d="M100 0 C 80 50, 90 80, 100 100 L 100 100 L 100 0 Z" />
                </svg>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 sm:px-12 lg:px-24 py-12 relative overflow-hidden">
            <!-- Background Decoration (Subtle Redwood Circles) -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-brand-teal/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-brand-rose/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-md w-full mx-auto relative z-10">
                <!-- Mobile Branding Header -->
                <div class="lg:hidden flex flex-col items-center mb-10">
                    <ApplicationLogo class="w-16 h-16 object-contain mb-4" />
                    <h2 class="text-2xl font-black tracking-tighter uppercase text-[#161514] dark:text-[#f2e8d5]">
                        Rumah IT <span class="text-brand-rose">Indonesia</span>
                    </h2>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <span class="text-[10px] font-black text-brand-rose uppercase tracking-[0.4em] mb-4 block">Portal Akses</span>
                    <h2 class="text-4xl font-black text-[#161514] dark:text-[#f2e8d5] tracking-tight leading-none mb-4">
                        Selamat Datang <span class="text-brand-teal">Kembali</span>
                    </h2>
                    <p class="text-[#a8a196] font-medium">Gunakan kredensial pengurus Anda untuk melanjutkan.</p>
                </div>

                <div v-if="status" class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <InputLabel for="email" value="Email Address" class="text-[10px] uppercase tracking-widest text-[#a8a196] font-black" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            placeholder="nama@rumahithub.com"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <InputLabel for="password" value="Security Password" class="text-[10px] uppercase tracking-widest text-[#a8a196] font-black" />
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-bold text-brand-rose hover:text-brand-rose-deep dark:hover:text-brand-rose-vibrant transition-colors"
                            >
                                Lupa Password?
                            </Link>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            v-model="form.password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <label class="flex items-center transition-all group cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-3 text-xs font-bold text-[#4e4b47] dark:text-[#a8a196] group-hover:text-brand-rose transition-colors"
                                >Ingat Saya</span
                            >
                        </label>
                    </div>

                    <div class="pt-4">
                        <RedwoodButton
                            class="w-full"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </template>
                            Masuk ke Dashboard
                        </RedwoodButton>
                    </div>
                </form>

                <!-- Footer / Extra info -->
                <div class="mt-12 text-center text-[10px] font-medium text-[#a8a196] tracking-widest">
                    &copy; 2026 RUMAH IT INDONESIA. SEMUA HAK DILINDUNGI.
                </div>
            </div>
        </div>
    </div>
</template>
