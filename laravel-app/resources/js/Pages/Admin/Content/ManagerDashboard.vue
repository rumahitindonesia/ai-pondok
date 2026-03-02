<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import moment from 'moment';
import 'moment/dist/locale/id';
import { ref, watchEffect } from 'vue';
import RedwoodSelect from '@/Components/RedwoodSelect.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';

moment.locale('id');

const props = defineProps({
    requests: Array,
    staffMembers: Array,
    instagramAccount: Object,
});

// Fix type differences and Laravel relation object serialization
const requestsData = ref([]);
watchEffect(() => {
    requestsData.value = props.requests.map(req => {
        let assignedId = '';
        if (req.assigned_to) {
            assignedId = typeof req.assigned_to === 'object' ? String(req.assigned_to.id) : String(req.assigned_to);
        }
        return {
            ...req,
            assigned_to: assignedId
        };
    });
});

const getStatusColor = (status) => {
    switch (status) {
        case 'Menunggu Assign':
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400 border-amber-200 dark:border-amber-800';
        case 'Ditugaskan':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 border-blue-200 dark:border-blue-800';
        case 'Proses':
            return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800';
        case 'Revisi':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 border-purple-200 dark:border-purple-800';
        case 'Selesai':
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800';
        case 'Batal':
            return 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400 border-red-200 dark:border-red-800';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
    }
};

const assignForm = useForm({
    assigned_to: '',
});

const assignTask = (requestId, staffId) => {
    assignForm.assigned_to = staffId;
    assignForm.put(route('admin.content.manager.assign', requestId), {
        preserveScroll: true,
    });
};

const statusForm = useForm({
    status: '',
});

const updateStatus = (requestId, newStatus) => {
    statusForm.status = newStatus;
    statusForm.put(route('admin.content.manager.status', requestId), {
        preserveScroll: true,
    });
};

const showDetailModal = ref(false);
const selectedRequest = ref(null);
const syncLoading = ref(false);

const openDetail = (request) => {
    selectedRequest.value = request;
    showDetailModal.value = true;
};

const connectToInstagram = () => {
    window.location.href = route('social.instagram.redirect');
};

const disconnectInstagram = () => {
    if (confirm('Apakah Anda yakin ingin memutuskan koneksi Instagram?')) {
        router.delete(route('social.instagram.disconnect'));
    }
};

const syncWithInstagram = () => {
    syncLoading.value = true;
    router.post(route('admin.social.instagram.sync', selectedRequest.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            syncLoading.value = false;
            const updated = props.requests.find(r => r.id === selectedRequest.value.id);
            if (updated) {
                selectedRequest.value = updated;
            }
        },
        onError: () => {
            syncLoading.value = false;
        }
    });
};
</script>

<template>
    <Head title="Manajemen Planner Konten" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Manajemen <span class="text-[#d02e5c]">Planner</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1 font-medium italic">Atur, tugaskan, dan pantau progres seluruh konten media pesantren.</p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Social Media Connection Card (Redwood Style) -->
                <div class="bg-gradient-to-br from-[#161514] to-[#3e3c3a] dark:from-[#2a2826] dark:to-[#1a1918] p-8 rounded-3xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden border border-[#ebeae8]/10">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-gradient-to-br from-purple-500/20 to-rose-500/20 rounded-full blur-3xl"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-tr from-amber-400 via-rose-500 to-purple-600 rounded-[20px] flex items-center justify-center text-white shadow-lg transform rotate-3 hover:rotate-0 transition-transform">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.975.975 1.247 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.975.975-2.242 1.247-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.975-.975-1.247-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608.975-.975 2.242-1.247 3.608-1.308 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-1.352.062-2.277.276-3.086.591-.837.325-1.546.759-2.251 1.464s-1.139 1.414-1.464 2.251c-.315.809-.529 1.734-.591 3.086-.058 1.28-.072 1.688-.072 4.947s.014 3.667.072 4.947c.062 1.352.276 2.277.591 3.086.726 1.868 2.193 3.335 4.061 4.061.809.315 1.734.529 3.086.591 1.28.058 1.688.072 4.947.072s3.667-.014 4.947-.072c1.352-.062 2.277-.276 3.086-.591 1.868-.726 3.335-2.193 4.061-4.061.315-.809.529-1.734.591-3.086.058-1.28.072-1.688.072-4.947s-.014-3.667-.072-4.947c-.062-1.352-.276-2.277-.591-3.086-.325-.837-.759-1.546-1.464-2.251s-1.414-1.139-2.251-1.464c-.809-.315-1.734-.529-3.086-.591-1.28-.058-1.688-.072-4.947-.072zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.162 6.162 6.162 6.162-2.759 6.162-6.162-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.441s.645 1.441 1.441 1.441 1.441-.645 1.441-1.441-.645-1.441-1.441-1.441z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white">Instagram Integration Integrator</h3>
                            <p v-if="instagramAccount" class="text-sm text-[#f2e8d5]/70 mt-1">Terhubung secata aktif sebagai <span class="font-bold text-white bg-white/20 px-2 py-0.5 rounded-md">@{{ instagramAccount.instagram_username }}</span></p>
                            <p v-else class="text-sm text-[#f2e8d5]/70 mt-1">Sistem saat ini belum terhubung ke akun Instagram Business Anda.</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-3 relative z-10 w-full md:w-auto">
                        <button v-if="!instagramAccount" @click="connectToInstagram" class="w-full md:w-auto bg-white text-[#d02e5c] px-6 py-3 rounded-full font-bold shadow-lg shadow-black/20 hover:scale-105 transition-all text-sm uppercase tracking-wide border border-white/50">
                            Hubungkan Sekarang
                        </button>
                        <button v-else @click="disconnectInstagram" class="w-full md:w-auto text-xs font-black text-white/50 hover:text-rose-400 transition-colors uppercase tracking-[0.2em] bg-black/20 hover:bg-black/40 px-4 py-2 rounded-full">
                            Putuskan Koneksi
                        </button>
                    </div>
                </div>

                <!-- Modern Cards Layout for Content Requests -->
                <div class="space-y-6">
                    <h3 class="font-black text-xl text-[#161514] dark:text-[#f2e8d5] px-2 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#d02e5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Daftar Permintaan Konten
                    </h3>

                    <div v-if="requestsData.length === 0" class="bg-white dark:bg-[#1e1d1b] rounded-3xl p-12 text-center border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-[#161514] rounded-full flex items-center justify-center mx-auto mb-4 border border-[#ebeae8] dark:border-[#3e3c3a]">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h4 class="text-lg font-bold text-[#161514] dark:text-[#f2e8d5]">Belum ada data permintaan</h4>
                        <p class="text-sm text-gray-400 mt-2">Daftar task konten yang diajukan akan muncul di sini.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div v-for="request in requestsData" :key="request.id" class="bg-white dark:bg-[#1e1d1b] rounded-3xl border border-[#ebeae8] dark:border-[#3e3c3a] p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                            <!-- Status Indicator Stripe -->
                            <div :class="['absolute top-0 left-0 w-2 h-full', request.status === 'Selesai' ? 'bg-emerald-400' : (request.status === 'Proses' ? 'bg-indigo-400' : 'bg-[#d02e5c]')]"></div>

                            <div class="flex flex-col lg:flex-row gap-6 ml-4">
                                <!-- Info Column -->
                                <div class="flex-1 space-y-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xs font-black bg-gray-100 dark:bg-[#252422] text-gray-600 dark:text-gray-400 px-3 py-1 rounded-full border border-gray-200 dark:border-[#3e3c3a]">#{{ request.id }}</span>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-[#d02e5c]">Req: {{ moment(request.created_at).format('DD MMM YYYY') }}</span>
                                            </div>
                                            <h3 class="text-xl font-black text-[#161514] dark:text-[#f2e8d5] leading-tight cursor-pointer hover:text-[#d02e5c] transition-colors line-clamp-2" @click="openDetail(request)">
                                                {{ request.format }}
                                            </h3>
                                            <div class="flex items-center gap-3 mt-2 text-sm text-gray-500 font-medium">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    {{ request.requester_name }}
                                                </span>
                                                <span class="text-gray-300">&bull;</span>
                                                <span class="flex items-center gap-1 font-bold text-rose-500 bg-rose-50 dark:bg-rose-900/10 px-2 py-0.5 rounded-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    DL: {{ moment(request.deadline_date).format('DD MMM') }}, {{ request.deadline_time }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 pt-2">
                                        <div class="px-3 py-1.5 bg-[#fcf8f5] dark:bg-[#252422] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            {{ request.output_format }}
                                        </div>
                                        <div class="px-3 py-1.5 bg-[#fcf8f5] dark:bg-[#252422] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            {{ request.size }}
                                        </div>
                                        <button @click="openDetail(request)" class="px-3 py-1.5 bg-white dark:bg-[#1e1d1b] border border-[#d02e5c]/30 text-[#d02e5c] rounded-xl text-xs font-black hover:bg-[#d02e5c] hover:text-white transition-colors flex items-center gap-1">
                                            See Detail Info
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Action Column (Status & Executor) -->
                                <div class="flex flex-col gap-4 lg:w-72 justify-center lg:border-l border-[#ebeae8] dark:border-[#3e3c3a] lg:pl-6">
                                    <div class="w-full">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Progres</p>
                                        <div class="relative w-full">
                                            <select 
                                                @change="updateStatus(request.id, $event.target.value)"
                                                :class="['w-full text-xs font-black rounded-2xl px-4 py-2.5 cursor-pointer outline-none appearance-none transition-all shadow-sm border-2', getStatusColor(request.status)]"
                                                :value="request.status"
                                            >
                                                <option value="Menunggu Assign">Menunggu Assign</option>
                                                <option value="Ditugaskan">Ditugaskan</option>
                                                <option value="Proses">Proses</option>
                                                <option value="Revisi">Revisi</option>
                                                <option value="Selesai">Selesai</option>
                                                <option value="Batal">Batal</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none opacity-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                        <div v-if="request.published_at" class="mt-2 pl-1">
                                            <span class="text-[9px] font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-md flex items-center gap-1 w-max">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                                Telah Terpublish
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-full">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pilih Eksekutor</p>
                                        <div class="relative w-full group">
                                            <select 
                                                @change="assignTask(request.id, $event.target.value)"
                                                v-model="request.assigned_to"
                                                class="w-full bg-[#fcf8f5] dark:bg-[#161514] border-2 border-[#ebeae8] dark:border-[#383736] rounded-2xl p-2.5 pr-8 text-[#161514] dark:text-[#f2e8d5] focus:border-[#d02e5c] focus:ring-4 focus:ring-[#d02e5c]/10 transition-all outline-none appearance-none cursor-pointer font-bold text-xs shadow-sm hover:border-gray-300"
                                            >
                                                <option value="">-- Belum Diassign --</option>
                                                <option v-for="staff in staffMembers" :key="staff.id" :value="String(staff.id)">
                                                    {{ staff.name }}
                                                </option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400 group-hover:text-[#d02e5c] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal (Redesigned) -->
        <div v-if="showDetailModal && selectedRequest" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="showDetailModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-[#1e1d1b] rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div class="bg-gradient-to-r from-[#d02e5c] to-[#a32448] px-8 py-6 relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                        <h3 class="text-xl font-black text-white flex justify-between items-center relative z-10" id="modal-title">
                            <span class="flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Detail Request #{{ selectedRequest.id }}
                            </span>
                            <button @click="showDetailModal = false" class="text-white hover:text-rose-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </h3>
                    </div>
                    <div class="px-8 py-8 space-y-8">
                        <div>
                            <h4 class="text-xs font-black text-gray-400 mb-3 uppercase tracking-widest flex items-center gap-2"> <span class="w-1 h-1 bg-[#d02e5c] rounded-full"></span> Konteks & Isi Copywriting</h4>
                            <p class="text-[#161514] dark:text-[#f2e8d5] whitespace-pre-line p-5 bg-[#fcf8f5] dark:bg-[#161514] rounded-2xl border border-[#ebeae8] dark:border-[#383736] font-medium leading-relaxed">{{ selectedRequest.content_description }}</p>
                        </div>
                        
                        <div v-if="selectedRequest.special_notes">
                            <h4 class="text-xs font-black text-gray-400 mb-3 uppercase tracking-widest flex items-center gap-2"> <span class="w-1 h-1 bg-amber-500 rounded-full"></span> Catatan Tambahan</h4>
                            <p class="text-amber-800 dark:text-amber-400 p-5 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-200 dark:border-amber-900/50 font-bold">{{ selectedRequest.special_notes }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-6 p-5 bg-gray-50 dark:bg-[#1a1918] rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kontak Requester (WA)</h4>
                                <p class="text-[#161514] dark:text-[#f2e8d5] font-black mt-1 text-lg">{{ selectedRequest.phone_number }}</p>
                            </div>
                            <div v-if="selectedRequest.reference_link">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Link Referensi Desain</h4>
                                <a :href="selectedRequest.reference_link" target="_blank" class="text-[#d02e5c] hover:underline font-black mt-1 flex items-center break-all text-lg transition-all hover:translate-x-1">
                                    Buka Referensi <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-black text-gray-400 mb-3 uppercase tracking-widest flex items-center gap-2"> <span class="w-1 h-1 bg-sky-500 rounded-full"></span> Elemen Wajib Cantum</h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-if="selectedRequest.include_logo" class="px-4 py-2 bg-white dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border-2 border-[#ebeae8] dark:border-[#4e4d4a] rounded-xl text-xs font-black shadow-sm">🎯 Sentuh Logo</span>
                                <span v-if="selectedRequest.include_website" class="px-4 py-2 bg-white dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border-2 border-[#ebeae8] dark:border-[#4e4d4a] rounded-xl text-xs font-black shadow-sm">🌐 Website</span>
                                <span v-if="selectedRequest.include_social_media" class="px-4 py-2 bg-white dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border-2 border-[#ebeae8] dark:border-[#4e4d4a] rounded-xl text-xs font-black shadow-sm">📱 Info Sosmed</span>
                                <span v-if="selectedRequest.include_phone" class="px-4 py-2 bg-white dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border-2 border-[#ebeae8] dark:border-[#4e4d4a] rounded-xl text-xs font-black shadow-sm">📞 Tombol WA</span>
                                <span v-if="!selectedRequest.include_logo && !selectedRequest.include_website && !selectedRequest.include_social_media && !selectedRequest.include_phone" class="text-gray-400 italic text-sm font-bold">Bebas & Polos</span>
                            </div>
                        </div>
                        
                        <div class="border-t-2 border-dashed border-[#ebeae8] dark:border-[#3e3c3a] pt-8">
                            <h4 class="text-sm font-black text-[#d02e5c] mb-6 uppercase tracking-widest flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-[#d02e5c]/10 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#d02e5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    </div>
                                    Performa Pasca Upload
                                </div>
                                <button 
                                    v-if="instagramAccount && selectedRequest.published_at"
                                    @click="syncWithInstagram"
                                    :disabled="syncLoading"
                                    class="text-xs bg-gradient-to-r from-amber-400 to-rose-500 text-white px-4 py-2 rounded-full font-black shadow-md hover:shadow-lg hover:scale-105 transition-all disabled:opacity-50 flex items-center gap-2 uppercase tracking-wide border border-white/20"
                                >
                                    <svg v-if="syncLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    {{ syncLoading ? 'Synchronizing...' : 'Live Sync IG' }}
                                </button>
                            </h4>

                            <div v-if="selectedRequest.published_at" class="space-y-6">
                                <div class="flex items-center gap-3 text-sm text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/20 border-2 border-sky-200 dark:border-sky-800 rounded-2xl p-4 shadow-sm">
                                    <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-black tracking-widest opacity-60">Status Publikasi</span>
                                        <span class="font-black text-lg">Live since {{ moment(selectedRequest.published_at).format('DD MMM YYYY, HH:mm') }} WIB</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                    <div class="bg-white dark:bg-[#1a1918] p-4 rounded-2xl border-2 border-[#ebeae8] dark:border-[#3e3c3a] text-center shadow-sm hover:border-[#d02e5c] transition-colors">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Reaches</div>
                                        <div class="text-xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ (selectedRequest.reach_count || 0).toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-white dark:bg-[#1a1918] p-4 rounded-2xl border-2 border-[#ebeae8] dark:border-[#3e3c3a] text-center shadow-sm hover:border-amber-500 transition-colors">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Engages</div>
                                        <div class="text-xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ (selectedRequest.engagement_count || 0).toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-white dark:bg-[#1a1918] p-4 rounded-2xl border-2 border-[#ebeae8] dark:border-[#3e3c3a] text-center shadow-sm hover:border-sky-500 transition-colors">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Impressing</div>
                                        <div class="text-xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ (selectedRequest.impressions_count || 0).toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-white dark:bg-[#1a1918] p-4 rounded-2xl border-2 border-[#ebeae8] dark:border-[#3e3c3a] text-center shadow-sm hover:border-emerald-500 transition-colors">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Saved</div>
                                        <div class="text-xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ (selectedRequest.saved_count || 0).toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-white dark:bg-[#1a1918] p-4 rounded-2xl border-2 border-[#ebeae8] dark:border-[#3e3c3a] text-center shadow-sm hover:border-purple-500 transition-colors">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Plays</div>
                                        <div class="text-xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ (selectedRequest.video_views_count || 0).toLocaleString() }}</div>
                                    </div>
                                </div>

                                <div v-if="selectedRequest.published_url">
                                    <div class="text-[10px] font-black text-gray-400 mb-2 uppercase tracking-widest">Tautan Publikasi</div>
                                    <a :href="selectedRequest.published_url" target="_blank" class="flex items-center gap-3 w-full p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border-2 border-blue-200 dark:border-blue-900/50 hover:border-blue-400 transition-colors group">
                                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </div>
                                        <span class="text-blue-700 dark:text-blue-400 font-bold truncate flex-1">{{ selectedRequest.published_url }}</span>
                                    </a>
                                </div>

                                <div v-if="selectedRequest.insight_notes" class="mt-4">
                                    <div class="text-[10px] font-black text-gray-400 mb-2 uppercase tracking-widest">Pesan & Evaluasi Kreator</div>
                                    <div class="p-5 bg-[#fcf8f5] dark:bg-[#1a1918] rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] relative">
                                        <svg class="absolute top-4 left-4 w-8 h-8 text-gray-200 dark:text-gray-700" fill="currentColor" viewBox="0 0 32 32"><path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"></path></svg>
                                        <p class="text-gray-700 dark:text-gray-300 font-medium italic pl-10 text-sm leading-relaxed">
                                            {{ selectedRequest.insight_notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-10 bg-gray-50 dark:bg-[#1a1918] rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                <div class="w-16 h-16 bg-white dark:bg-[#252422] rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h5 class="text-gray-600 dark:text-gray-300 font-black">Menunggu Ditayangkan</h5>
                                <p class="text-gray-400 text-xs mt-1 font-medium px-8 content-center">Data Insight akan ditarik secara otomatis dari Instagram ketika status berubah menjadi Published.</p>
                            </div>
                        </div>

                    </div>
                    <div class="bg-gray-50 dark:bg-[#161514] px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse border-t border-[#ebeae8] dark:border-[#3e3c3a]">
                        <button @click="showDetailModal = false" type="button" class="w-full inline-flex justify-center flex-1 sm:flex-none rounded-full border-2 border-gray-300 dark:border-[#4e4d4a] shadow-sm px-8 py-3 bg-white dark:bg-[#2d2c2a] text-base font-black text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#3e3c3a] focus:outline-none focus:ring-4 focus:ring-gray-200 transition-all sm:ml-3 sm:text-sm">
                            Kembali & Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
