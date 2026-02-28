<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    referralCount: {
        type: Number,
        default: 0
    },
    referralCode: {
        type: String,
        default: ''
    }
});

const copySuccess = ref(false);
const referralLink = computed(() => {
    if (typeof window !== 'undefined' && props.referralCode) {
        return `${window.location.origin}/register-psb?ref=${props.referralCode}`;
    }
    return '';
});

const copyToClipboard = async () => {
    if (!referralLink.value) return;
    try {
        await navigator.clipboard.writeText(referralLink.value);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 3000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Pengaturan <span class="text-[#c97e60]">Profil</span>
            </h2>
        </template>

        <div class="py-12 bg-[#f5f4f2] text-[#161514] min-h-screen dark:bg-[#161514] dark:text-[#f2e8d5]">
            <div class="mx-auto max-w-7xl space-y-10 sm:px-6 lg:px-8">
                <div class="bg-white shadow-[0_30px_60px_rgba(0,0,0,0.05)] rounded-[40px] border border-[#ebeae8] p-8 sm:p-12 dark:bg-[#262524] dark:border-[#383736]">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Program Referral Section -->
                <div class="bg-white shadow-[0_30px_60px_rgba(0,0,0,0.05)] rounded-[40px] border border-[#ebeae8] p-8 sm:p-12 dark:bg-[#262524] dark:border-[#383736]">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#c97e60]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                Program Referral PSB
                            </h2>
                            <p class="mt-1 text-sm text-[#a8a196]">
                                Bagikan link ini ke calon santri atau wali santri. Setiap pendaftar yang menggunakan link ini akan tercatat sebagai referensi Anda.
                            </p>
                        </header>

                        <div class="mt-6">
                            <label class="block text-xs font-bold uppercase tracking-wide text-[#a8a196] mb-2 cursor-pointer">Link Pendaftaran Anda</label>
                            <div class="flex items-center gap-3">
                                <input 
                                    type="text" 
                                    readonly 
                                    :value="referralLink" 
                                    class="flex-1 bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-4 py-3 text-sm text-[#161514] dark:text-[#f2e8d5] font-medium"
                                />
                                <RedwoodButton variant="primary" @click="copyToClipboard" class="shrink-0">
                                    <template #icon>
                                        <svg v-if="copySuccess" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    </template>
                                    {{ copySuccess ? 'Tersalin!' : 'Copy Link' }}
                                </RedwoodButton>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pendaftar dari Link Anda:</p>
                                <p class="text-3xl font-black text-[#161514] dark:text-white mt-1">{{ referralCount }} <span class="text-sm font-bold text-[#c97e60] ml-1">Orang</span></p>
                            </div>
                            <div class="w-16 h-16 rounded-full bg-[#c97e60]/10 flex items-center justify-center">
                                <svg class="w-8 h-8 text-[#c97e60]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="bg-white shadow-[0_30px_60px_rgba(0,0,0,0.05)] rounded-[40px] border border-[#ebeae8] p-8 sm:p-12 dark:bg-[#262524] dark:border-[#383736]">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white shadow-[0_30px_60px_rgba(0,0,0,0.05)] rounded-[40px] border border-[#ebeae8] p-8 sm:p-12 dark:bg-[#262524] dark:border-[#383736]">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
