<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link } from '@inertiajs/vue3';
import moment from 'moment';
import 'moment/dist/locale/id';

moment.locale('id');

const props = defineProps({
    requests: Array,
});

const showDetailModal = ref(false);
const selectedRequest = ref(null);

const openDetail = (request) => {
    selectedRequest.value = request;
    showDetailModal.value = true;
};

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
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Pengajuan <span class="text-[#d02e5c]">Konten</span>
            </h2>
        </template>

        <div class="pb-12 pt-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Action Row -->
                <div class="flex justify-end">
                    <RedwoodButton 
                        as="Link"
                        :href="route('content-requests.create')"
                        variant="primary"
                    >
                        <template #icon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </template>
                        Buat Request Baru
                    </RedwoodButton>
                </div>
                
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

                        <div class="px-6 py-4 bg-gray-50 dark:bg-[#1a1918] border-t border-[#ebeae8] dark:border-[#3e3c3a] flex justify-between items-center">
                            <div v-if="request.published_at" class="flex items-center text-[10px] font-black bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-400 border border-sky-200 dark:border-sky-800 rounded-full px-2 py-0.5">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                Published
                            </div>
                            <div v-else></div>
                            <button @click="openDetail(request)" class="text-[#d02e5c] hover:text-[#b0224a] font-bold text-sm flex items-center group-hover:translate-x-1 transition-transform">
                                Detail Info
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Detail Modal -->
        <div v-if="showDetailModal && selectedRequest" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/75 transition-opacity" aria-hidden="true" @click="showDetailModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-[#1e1d1b] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div class="bg-gradient-to-r from-[#d02e5c] to-[#a32448] px-6 py-4">
                        <h3 class="text-lg leading-6 font-bold text-white flex justify-between items-center" id="modal-title">
                            Detail Pengajuan Konten
                            <button @click="showDetailModal = false" class="text-white hover:text-rose-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </h3>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        <!-- Status Badge -->
                        <div class="flex justify-between items-center">
                            <span :class="['px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border', getStatusColor(selectedRequest.status)]">
                                Status: {{ selectedRequest.status }}
                            </span>
                            <span v-if="selectedRequest.published_at" class="text-[10px] font-black bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-400 border border-sky-200 dark:border-sky-800 rounded-full px-3 py-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                SUDAH DIPUBLISH PADA {{ moment(selectedRequest.published_at).format('DD MMM YYYY') }}
                            </span>
                        </div>

                        <div>
                            <h4 class="text-[10px] font-black text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-[0.2em]">Deskripsi Materi</h4>
                            <p class="text-[#161514] dark:text-[#f2e8d5] whitespace-pre-line p-4 bg-gray-50 dark:bg-[#1a1918] rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a] font-medium text-sm leading-relaxed">{{ selectedRequest.content_description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-[10px] font-black text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-[0.2em]">Format & Ukuran</h4>
                                <p class="text-[#161514] dark:text-[#f2e8d5] font-bold text-sm">{{ selectedRequest.format }} - {{ selectedRequest.size }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Output: {{ selectedRequest.output_format }}</p>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-[0.2em]">Deadline</h4>
                                <p class="text-[#161514] dark:text-[#f2e8d5] font-bold text-sm text-rose-600 dark:text-rose-400">{{ moment(selectedRequest.deadline_date).format('DD MMMM YYYY') }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Pukul {{ selectedRequest.deadline_time }} WIB</p>
                            </div>
                        </div>

                        <!-- Results / Metrics Section (if published) -->
                        <div v-if="selectedRequest.published_at" class="border-t border-[#ebeae8] dark:border-[#3e3c3a] pt-6">
                            <h4 class="text-sm font-black text-[#d02e5c] mb-4 uppercase tracking-widest flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                Performa Konten
                            </h4>
                            
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="bg-gray-50 dark:bg-[#1a1918] p-3 rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] text-center">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Reach</div>
                                    <div class="text-lg font-black text-[#161514] dark:text-[#f2e8d5]">{{ selectedRequest.reach_count.toLocaleString() }}</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-[#1a1918] p-3 rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] text-center">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Engagement</div>
                                    <div class="text-lg font-black text-[#161514] dark:text-[#f2e8d5]">{{ selectedRequest.engagement_count.toLocaleString() }}</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-[#1a1918] p-3 rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] text-center">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Clicks</div>
                                    <div class="text-lg font-black text-[#161514] dark:text-[#f2e8d5]">{{ selectedRequest.link_clicks.toLocaleString() }}</div>
                                </div>
                            </div>

                            <div v-if="selectedRequest.published_url" class="mb-4">
                                <h4 class="text-[10px] font-black text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-[0.2em]">Link Publish</h4>
                                <a :href="selectedRequest.published_url" target="_blank" class="text-blue-600 dark:text-blue-400 font-bold text-sm hover:underline break-all block p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-900/50">
                                    {{ selectedRequest.published_url }}
                                </a>
                            </div>

                            <div v-if="selectedRequest.insight_notes">
                                <h4 class="text-[10px] font-black text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-[0.2em]">Evaluasi / Insight</h4>
                                <p class="text-emerald-800 dark:text-emerald-400 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-900/50 text-sm italic">
                                    "{{ selectedRequest.insight_notes }}"
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-[#1a1918] px-6 py-4 flex justify-end border-t border-[#ebeae8] dark:border-[#3e3c3a]">
                        <button @click="showDetailModal = false" class="px-6 py-2 bg-white dark:bg-[#2d2c2a] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-full font-bold text-sm text-[#161514] dark:text-[#f2e8d5] hover:bg-gray-100 dark:hover:bg-[#3e3c3a] transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
