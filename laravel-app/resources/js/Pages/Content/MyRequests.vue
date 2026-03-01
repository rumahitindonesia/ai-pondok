<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import moment from 'moment';
import 'moment/dist/locale/id';

moment.locale('id');

const props = defineProps({
    requests: Array,
});

const getStatusColor = (status) => {
    switch (status) {
        case 'Menunggu Assign':
        case 'Ditugaskan':
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500 border-amber-200 dark:border-amber-800';
        case 'Proses':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-500 border-blue-200 dark:border-blue-800';
        case 'Revisi':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-500 border-purple-200 dark:border-purple-800';
        case 'Selesai':
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-500 border-emerald-200 dark:border-emerald-800';
        case 'Batal':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-500 border-red-200 dark:border-red-800';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
    }
};
</script>

<template>
    <Head title="Pengajuan Konten Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-extrabold text-2xl text-[#161514] dark:text-[#f2e8d5] leading-tight flex items-center">
                    <svg class="w-8 h-8 mr-3 text-[#d02e5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Pengajuan Konten Saya
                </h2>
                <Link :href="route('content-requests.create')" class="inline-flex items-center px-4 py-2 bg-[#d02e5c] border border-transparent rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-[#b0224a] focus:bg-[#b0224a] active:bg-[#911a3b] focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:ring-offset-2 dark:focus:ring-offset-[#161514] transition ease-in-out duration-150 shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Baru
                </Link>
            </div>
        </template>

        <div class="pb-12 pt-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="requests.length === 0" class="bg-white dark:bg-[#1e1d1b] overflow-hidden shadow-sm sm:rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-300 dark:text-[#3e3c3a] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="text-xl font-bold text-[#161514] dark:text-[#f2e8d5]">Belum Ada Pengajuan</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Anda belum pernah mengajukan request konten ke tim Media.</p>
                    <Link :href="route('content-requests.create')" class="mt-6 inline-flex items-center px-6 py-3 bg-[#d02e5c] border border-transparent rounded-full font-bold text-sm text-white hover:bg-[#b0224a] focus:bg-[#b0224a] focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:ring-offset-2 dark:focus:ring-offset-[#161514] transition ease-in-out duration-150 shadow-md">
                        Mulai Request Konten
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="request in requests" :key="request.id" class="bg-white dark:bg-[#1e1d1b] rounded-2xl shadow-sm border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden hover:shadow-md transition-shadow group flex flex-col">
                        <div class="p-6 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <span :class="['px-3 py-1 rounded-full text-xs font-bold border', getStatusColor(request.status)]">
                                    {{ request.status }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                                    {{ moment(request.created_at).format('DD MMM YYYY') }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-[#161514] dark:text-[#f2e8d5] mb-2 line-clamp-2" :title="request.content_description">
                                {{ request.content_description }}
                            </h3>

                            <div class="space-y-3 mt-4">
                                <div class="flex items-center text-sm">
                                    <span class="w-8 flex justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path></svg>
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-300 font-medium">{{ request.format }}</span>
                                    <span class="mx-2 text-gray-300 dark:text-[#3e3c3a]">•</span>
                                    <span class="text-gray-600 dark:text-gray-300">{{ request.size }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm">
                                    <span class="w-8 flex justify-center text-rose-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </span>
                                    <div class="flex flex-col">
                                        <span class="text-[#161514] dark:text-[#f2e8d5] font-bold">
                                            DL: {{ moment(request.deadline_date).format('DD MMM YYYY') }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400 text-xs text-rose-500 font-medium">Jam {{ request.deadline_time }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center text-sm pt-3 border-t border-gray-100 dark:border-[#2d2c2a]">
                                    <span class="w-8 flex justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-300">
                                        Eksekutor: <strong class="text-[#161514] dark:text-[#f2e8d5]">{{ request.assigned_to ? request.assigned_to.name : 'Belum ditentukan' }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
