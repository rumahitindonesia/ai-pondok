<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import moment from 'moment';
import 'moment/dist/locale/id';
import { ref, computed } from 'vue';

moment.locale('id');

const props = defineProps({
    tasks: Array,
});

const getStatusColor = (status) => {
    switch (status) {
        case 'Ditugaskan':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 border-blue-200 dark:border-blue-800';
        case 'Proses':
            return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800';
        case 'Revisi':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 border-purple-200 dark:border-purple-800';
        case 'Selesai':
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
    }
};

const statusForm = useForm({
    status: '',
});

const updateStatus = (taskId, newStatus) => {
    statusForm.status = newStatus;
    statusForm.put(route('media.tasks.status', taskId), {
        preserveScroll: true,
    });
};

const showDetailModal = ref(false);
const selectedTask = ref(null);

const openDetail = (task) => {
    selectedTask.value = task;
    showDetailModal.value = true;
};

// Group tasks for Kanban board
const newTasks = computed(() => props.tasks.filter(t => t.status === 'Ditugaskan'));
const progressTasks = computed(() => props.tasks.filter(t => t.status === 'Proses' || t.status === 'Revisi'));
const completedTasks = computed(() => props.tasks.filter(t => t.status === 'Selesai'));

</script>

<template>
    <Head title="Tugas Media Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-extrabold text-2xl text-[#161514] dark:text-[#f2e8d5] leading-tight flex items-center">
                <svg class="w-8 h-8 mr-3 text-[#d02e5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Tugas Media Saya
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Kanban Board Layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Column 1: Ditugaskan (Baru) -->
                    <div class="bg-blue-50/50 dark:bg-[#1e1d1b] rounded-2xl p-4 border border-blue-100 dark:border-[#3e3c3a] h-fit">
                        <div class="flex items-center justify-between mb-4 border-b border-blue-200 dark:border-[#3e3c3a] pb-2">
                            <h3 class="font-bold text-blue-900 dark:text-blue-400 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                                Tugas Baru
                            </h3>
                            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-bold px-2 py-1 rounded-full">{{ newTasks.length }}</span>
                        </div>
                        <div class="space-y-4">
                            <div v-for="task in newTasks" :key="task.id" class="bg-white dark:bg-[#252422] p-4 rounded-xl shadow-sm border border-[#ebeae8] dark:border-[#4e4d4a] cursor-pointer hover:shadow-md transition-shadow" @click="openDetail(task)">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 px-2 py-1 rounded-md">
                                        DL: {{ moment(task.deadline_date).format('DD MMM') }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-[#161514] dark:text-[#f2e8d5] text-sm mb-1">{{ task.format }} / {{ task.output_format }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ task.content_description }}</p>
                                <div class="mt-3 flex items-center text-xs text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ task.requester_name }}
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-[#3e3c3a] flex gap-2" @click.stop>
                                    <button @click="updateStatus(task.id, 'Proses')" class="flex-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-xs font-bold py-1.5 rounded-lg transition-colors border border-indigo-200 dark:border-indigo-800">
                                        Mulai Kerjakan
                                    </button>
                                </div>
                            </div>
                            <div v-if="newTasks.length === 0" class="text-center py-6 text-sm text-gray-400 dark:text-gray-500 border-2 border-dashed border-gray-200 dark:border-[#3e3c3a] rounded-xl font-medium">
                                Tidak ada tugas baru
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Proses & Revisi -->
                    <div class="bg-indigo-50/50 dark:bg-[#1e1d1b] rounded-2xl p-4 border border-indigo-100 dark:border-[#3e3c3a] h-fit">
                        <div class="flex items-center justify-between mb-4 border-b border-indigo-200 dark:border-[#3e3c3a] pb-2">
                            <h3 class="font-bold text-indigo-900 dark:text-indigo-400 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>
                                Sedang Dikerjakan
                            </h3>
                            <span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 text-xs font-bold px-2 py-1 rounded-full">{{ progressTasks.length }}</span>
                        </div>
                        <div class="space-y-4">
                            <div v-for="task in progressTasks" :key="task.id" class="bg-white dark:bg-[#252422] p-4 rounded-xl shadow-sm border border-[#ebeae8] dark:border-[#4e4d4a] cursor-pointer hover:shadow-md transition-shadow" @click="openDetail(task)">
                                <div class="flex justify-between items-start mb-2">
                                    <span :class="['text-xs font-bold px-2 py-1 rounded-md border', getStatusColor(task.status)]">
                                        {{ task.status }}
                                    </span>
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                        {{ moment(task.deadline_date).format('DD MMM') }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-[#161514] dark:text-[#f2e8d5] text-sm mb-1">{{ task.format }} / {{ task.output_format }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ task.content_description }}</p>
                                <div class="mt-3 flex items-center text-xs text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ task.requester_name }}
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-[#3e3c3a] flex gap-2" @click.stop>
                                    <button @click="updateStatus(task.id, 'Selesai')" class="flex-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-xs font-bold py-1.5 rounded-lg transition-colors border border-emerald-200 dark:border-emerald-800">
                                        Tandai Selesai
                                    </button>
                                </div>
                            </div>
                            <div v-if="progressTasks.length === 0" class="text-center py-6 text-sm text-gray-400 dark:text-gray-500 border-2 border-dashed border-gray-200 dark:border-[#3e3c3a] rounded-xl font-medium">
                                Kosong
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Selesai -->
                    <div class="bg-emerald-50/50 dark:bg-[#1e1d1b] rounded-2xl p-4 border border-emerald-100 dark:border-[#3e3c3a] h-fit opacity-75 hover:opacity-100 transition-opacity">
                        <div class="flex items-center justify-between mb-4 border-b border-emerald-200 dark:border-[#3e3c3a] pb-2">
                            <h3 class="font-bold text-emerald-900 dark:text-emerald-500 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                                Selesai
                            </h3>
                            <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-xs font-bold px-2 py-1 rounded-full">{{ completedTasks.length }}</span>
                        </div>
                        <div class="space-y-4">
                            <div v-for="task in completedTasks" :key="task.id" class="bg-white dark:bg-[#252422] p-4 rounded-xl shadow-sm border border-[#ebeae8] dark:border-[#4e4d4a] cursor-pointer hover:shadow-md transition-shadow" @click="openDetail(task)">
                                <div class="flex justify-between items-start mb-2">
                                    <span :class="['text-xs font-bold px-2 py-1 rounded-md border', getStatusColor(task.status)]">
                                        {{ task.status }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-[#161514] dark:text-[#f2e8d5] text-sm mb-1">{{ task.format }} / {{ task.output_format }}</h4>
                                <div class="mt-2 flex items-center text-xs text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ task.requester_name }}
                                </div>
                            </div>
                            <div v-if="completedTasks.length === 0" class="text-center py-6 text-sm text-gray-400 dark:text-gray-500 border-2 border-dashed border-gray-200 dark:border-[#3e3c3a] rounded-xl font-medium">
                                Belum ada yang selesai
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Detail Modal (Same as Manager Dashboard) -->
        <div v-if="showDetailModal && selectedTask" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/75 transition-opacity" aria-hidden="true" @click="showDetailModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-[#1e1d1b] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div class="bg-gradient-to-r from-[#d02e5c] to-[#a32448] px-6 py-4">
                        <h3 class="text-lg leading-6 font-bold text-white flex justify-between items-center" id="modal-title">
                            Detail Tugas Konten (ID: #{{ selectedTask.id }})
                            <button @click="showDetailModal = false" class="text-white hover:text-rose-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </h3>
                    </div>
                    <div class="px-6 py-6 pb-8 space-y-6">
                        
                        <div class="flex items-center justify-between border-b border-[#ebeae8] dark:border-[#3e3c3a] pb-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pengaju</p>
                                <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ selectedTask.requester_name }} <span class="text-xs font-normal text-gray-500">({{ selectedTask.jabatan ? selectedTask.jabatan.nama : 'Lainnya' }})</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Deadline</p>
                                <p class="font-bold text-rose-600 dark:text-rose-400">{{ moment(selectedTask.deadline_date).format('DD MMM YYYY') }} - {{ selectedTask.deadline_time }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Deskripsi / Isi Konten</h4>
                            <p class="text-[#161514] dark:text-[#f2e8d5] whitespace-pre-line p-4 bg-gray-50 dark:bg-[#1a1918] rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a]">{{ selectedTask.content_description }}</p>
                        </div>
                        
                        <div v-if="selectedTask.special_notes">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Catatan Khusus</h4>
                            <p class="text-amber-800 dark:text-amber-400 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-900/50">{{ selectedTask.special_notes }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nomor Kontak (WA)</h4>
                                <a :href="'https://wa.me/' + selectedTask.phone_number.replace(/^0/, '62')" target="_blank" class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline font-semibold mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> 
                                    Wa: {{ selectedTask.phone_number }}
                                </a>
                            </div>
                            <div v-if="selectedTask.reference_link">
                                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Link Referensi</h4>
                                <a :href="selectedTask.reference_link" target="_blank" class="text-[#d02e5c] hover:underline font-semibold mt-1 flex items-center break-all">
                                    Buka Tautan <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Elemen yang Dicantumkan</h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-if="selectedTask.include_logo" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ Logo</span>
                                <span v-if="selectedTask.include_website" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ Website</span>
                                <span v-if="selectedTask.include_social_media" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ Sosial Media</span>
                                <span v-if="selectedTask.include_phone" class="px-3 py-1 bg-gray-100 dark:bg-[#2d2c2a] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#4e4d4a] rounded-full text-xs font-bold">✅ No. Whatsapp</span>
                                <span v-if="!selectedTask.include_logo && !selectedTask.include_website && !selectedTask.include_social_media && !selectedTask.include_phone" class="text-gray-400 italic text-sm">Tidak ada</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Bar -->
                    <div class="bg-gray-50 dark:bg-[#1a1918] px-4 py-3 sm:px-6 flex flex-col sm:flex-row justify-between items-center border-t border-[#ebeae8] dark:border-[#3e3c3a] gap-3">
                        
                        <div class="flex gap-2">
                            <button v-if="selectedTask.status === 'Ditugaskan'" @click="updateStatus(selectedTask.id, 'Proses'); showDetailModal = false;" class="inline-flex justify-center rounded-full border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm transition-colors">
                                Mulai Proses
                            </button>
                            <button v-if="selectedTask.status === 'Proses' || selectedTask.status === 'Revisi'" @click="updateStatus(selectedTask.id, 'Selesai'); showDetailModal = false;" class="inline-flex justify-center rounded-full border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:text-sm transition-colors">
                                Tandai Selesai
                            </button>
                        </div>
                        
                        <button @click="showDetailModal = false" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-full border border-gray-300 dark:border-[#4e4d4a] shadow-sm px-4 py-2 bg-white dark:bg-[#2d2c2a] text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#3e3c3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d02e5c] sm:text-sm transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
