<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import moment from 'moment';
import 'moment/dist/locale/id';
import { ref } from 'vue';
import RedwoodSelect from '@/Components/RedwoodSelect.vue';

moment.locale('id');

const props = defineProps({
    requests: Array,
    staffMembers: Array,
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

const openDetail = (request) => {
    selectedRequest.value = request;
    showDetailModal.value = true;
};
</script>

<template>
    <Head title="Manajemen Planner Konten" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Manajemen <span class="text-[#d02e5c]">Planner</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-[#1e1d1b] shadow-sm rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <thead class="bg-[#f5f4f2] dark:bg-[#1a1918]">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal & Deadline</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengaju</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Materi Konten</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Eksekutor (Staff)</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1e1d1b] divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                                <tr v-for="request in requests" :key="request.id" class="hover:bg-gray-50 dark:hover:bg-[#252422] transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-[#161514] dark:text-[#f2e8d5]">Req: {{ moment(request.created_at).format('DD MMM YYYY') }}</div>
                                        <div class="text-sm font-bold text-rose-600 dark:text-rose-400 mt-1">
                                            DL: {{ moment(request.deadline_date).format('DD MMM') }} ({{ request.deadline_time }})
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5]">{{ request.requester_name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ request.jabatan ? request.jabatan.nama : 'Santri/Lainnya' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5] line-clamp-1" :title="request.content_description">
                                            {{ request.format }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ request.size }} &bull; {{ request.output_format }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <RedwoodSelect 
                                            @change="updateStatus(request.id, $event.target.value)"
                                            v-model="request.status"
                                        >
                                            <option value="Menunggu Assign">Menunggu Assign</option>
                                            <option value="Ditugaskan">Ditugaskan</option>
                                            <option value="Proses">Proses</option>
                                            <option value="Revisi">Revisi</option>
                                            <option value="Selesai">Selesai</option>
                                            <option value="Batal">Batal</option>
                                        </RedwoodSelect>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <RedwoodSelect 
                                            @change="assignTask(request.id, $event.target.value)"
                                            v-model="request.assigned_to"
                                        >
                                            <option value="">-- Belum Diassign --</option>
                                            <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">
                                                {{ staff.name }}
                                            </option>
                                        </RedwoodSelect>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openDetail(request)" class="text-[#d02e5c] hover:text-[#b0224a] font-bold">Detail Info</button>
                                    </td>
                                </tr>
                                <tr v-if="requests.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada pengajuan konten.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                            Detail Request Konten (ID: #{{ selectedRequest.id }})
                            <button @click="showDetailModal = false" class="text-white hover:text-rose-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </h3>
                    </div>
                    <div class="px-6 py-6 pb-8 space-y-6">
                        <div>
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Deskripsi / Isi Konten</h4>
                            <p class="text-[#161514] dark:text-[#f2e8d5] whitespace-pre-line p-4 bg-gray-50 dark:bg-[#1a1918] rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a]">{{ selectedRequest.content_description }}</p>
                        </div>
                        
                        <div v-if="selectedRequest.special_notes">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Catatan Khusus</h4>
                            <p class="text-amber-800 dark:text-amber-400 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-900/50">{{ selectedRequest.special_notes }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nomor Kontak (WA)</h4>
                                <p class="text-[#161514] dark:text-[#f2e8d5] font-semibold mt-1">{{ selectedRequest.phone_number }}</p>
                            </div>
                            <div v-if="selectedRequest.reference_link">
                                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Link Referensi</h4>
                                <a :href="selectedRequest.reference_link" target="_blank" class="text-[#d02e5c] hover:underline font-semibold mt-1 flex items-center break-all">
                                    Buka Tautan <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Elemen yang Dicantumkan</h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-if="selectedRequest.include_logo" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ Logo</span>
                                <span v-if="selectedRequest.include_website" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ Website</span>
                                <span v-if="selectedRequest.include_social_media" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ Sosial Media</span>
                                <span v-if="selectedRequest.include_phone" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ No. Whatsapp</span>
                                <span v-if="!selectedRequest.include_logo && !selectedRequest.include_website && !selectedRequest.include_social_media && !selectedRequest.include_phone" class="text-gray-400 italic text-sm">Tidak ada</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-[#1a1918] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-[#ebeae8] dark:border-[#3e3c3a]">
                        <button @click="showDetailModal = false" type="button" class="w-full inline-flex justify-center rounded-full border border-gray-300 dark:border-[#4e4d4a] shadow-sm px-4 py-2 bg-white dark:bg-[#2d2c2a] text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#3e3c3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d02e5c] sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
