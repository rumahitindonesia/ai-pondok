<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { notify } from '@/Utils/toast';

const props = defineProps({
    registrations: Object
});

// --- Swipe & Hover Logic ---
const REVEAL_WIDTH = 280; // Adjusted for 4 buttons
const SWIPE_THRESHOLD = 50;
const swipedId = ref(null);
const hoveredId = ref(null);
const dragging = ref(false);
const startX = ref(0);
const startOffset = ref(0);
const currentOffset = ref(0);

const onPointerDown = (e, id) => {
    dragging.value = true;
    startX.value = e.clientX;
    startOffset.value = (swipedId.value === id) ? -REVEAL_WIDTH : 0;
    e.currentTarget.setPointerCapture(e.pointerId);
};

const onPointerMove = (e, id) => {
    if (!dragging.value) return;
    const dx = e.clientX - startX.value;
    const raw = startOffset.value + dx;
    currentOffset.value = Math.min(0, Math.max(-REVEAL_WIDTH, raw));
    if (raw < -SWIPE_THRESHOLD / 2 && swipedId.value !== id) swipedId.value = id;
};

const onPointerUp = (e, id) => {
    if (!dragging.value) return;
    dragging.value = false;
    const dx = e.clientX - startX.value;
    const total = startOffset.value + dx;
    swipedId.value = total < -SWIPE_THRESHOLD ? id : null;
    currentOffset.value = 0;
};

const rowOffset = (id) => {
    if (swipedId.value === id) return dragging.value ? currentOffset.value : -REVEAL_WIDTH;
    if (hoveredId.value === id && !dragging.value) return -REVEAL_WIDTH;
    return 0;
};

const isModalOpen = ref(false);
const selectedRegistration = ref(null);

const openResponsesModal = (reg) => {
    selectedRegistration.value = reg;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => { selectedRegistration.value = null; }, 300);
};

const statusForm = useForm({
    status_seleksi: ''
});

const updateStatus = (id, status) => {
    statusForm.status_seleksi = status;
    statusForm.patch(route('admin.psb.status', id), {
        preserveScroll: true
    });
};

const copyLink = async (token) => {
    const url = window.location.origin + '/psb/lengkapi-data/' + token;
    
    try {
        // Try modern clipboard API
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(url);
            notify('Link formulir tahap 2 berhasil disalin!');
        } else {
            throw new Error('Clipboard API unavailable');
        }
    } catch (err) {
        // Fallback for non-secure contexts or older browsers
        const textArea = document.createElement("textarea");
        textArea.value = url;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            notify('Link biodata berhasil disalin (fallback mode)');
        } catch (fallbackErr) {
            notify('Gagal menyalin link secara otomatis', 'error');
        }
        document.body.removeChild(textArea);
    }
};
</script>

<template>
    <Head title="Manajemen Registrasi PSB" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Penerimaan <span class="text-[#d02e5c]">Santri Baru</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Table Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <!-- Header -->
                    <div class="flex items-center border-b border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50/50 dark:bg-[#1a1918]/50">
                        <div class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] shrink-0 w-[120px] hidden lg:block">Tgl Daftar</div>
                        <div class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] flex-1 min-w-0">Calon Santri</div>
                        <div class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] shrink-0 w-[200px] hidden md:block">Wali / Kontak</div>
                        <div class="px-6 py-4 text-left text-xs font-black text-[#a8a196] uppercase tracking-wider shrink-0 w-[150px] hidden xl:block">Referensi</div>
                        <div class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] shrink-0 w-[120px] hidden lg:block">Berkas</div>
                        <div class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] shrink-0 w-[150px] hidden sm:block">Status Seleksi</div>
                        <div class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] shrink-0 w-[60px] text-right truncate">←</div>
                    </div>

                    <!-- Body -->
                    <div class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                        <div v-for="reg in registrations.data" :key="reg.id" 
                            class="relative bg-white dark:bg-[#161514] hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors overflow-hidden"
                            @mouseenter="hoveredId = reg.id"
                            @mouseleave="hoveredId = null"
                        >
                            <!-- Revealed action buttons (Fixed behind row) -->
                            <div class="absolute inset-y-0 right-0 flex z-0" :style="{ width: REVEAL_WIDTH + 'px' }">
                                <button
                                    v-if="reg.token"
                                    @click="copyLink(reg.token)"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-blue-500 text-white text-[10px] font-black uppercase hover:brightness-110 active:brightness-90 transition-all border-l border-white/10"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                    Copy
                                </button>
                                <button
                                    @click="openResponsesModal(reg)"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-indigo-500 text-white text-[10px] font-black uppercase hover:brightness-110 active:brightness-90 transition-all border-l border-white/10"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Detail
                                </button>
                                <button
                                    @click="updateStatus(reg.id, 'diterima')"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-emerald-500 text-white text-[10px] font-black uppercase hover:brightness-110 active:brightness-90 transition-all border-l border-white/10"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Terima
                                </button>
                                <button
                                    @click="updateStatus(reg.id, 'ditolak')"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-rose-500 text-white text-[10px] font-black uppercase hover:brightness-110 active:brightness-90 transition-all border-l border-white/10"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Tolak
                                </button>
                            </div>

                            <!-- Swipeable content layer -->
                            <div class="flex items-center bg-inherit w-full relative z-10 cursor-grab active:cursor-grabbing"
                                :style="{
                                    transform: `translateX(${rowOffset(reg.id)}px)`,
                                    transition: dragging ? 'none' : 'transform 0.25s cubic-bezier(0.25,1,0.5,1)',
                                    touchAction: 'pan-y',
                                }"
                                @pointerdown="(e) => onPointerDown(e, reg.id)"
                                @pointermove="(e) => onPointerMove(e, reg.id)"
                                @pointerup="(e) => onPointerUp(e, reg.id)"
                            >
                                <div class="px-8 py-5 shrink-0 w-[120px] hidden lg:block">
                                    <p class="text-xs font-medium">{{ new Date(reg.created_at).toLocaleDateString('id-ID') }}</p>
                                </div>
                                <div class="px-8 py-5 flex-1 min-w-0">
                                    <div>
                                        <p class="font-bold text-[#161514] dark:text-[#f2e8d5] truncate">{{ reg.nama_calon }}</p>
                                        <p class="text-[10px] text-[#a8a196]">{{ reg.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    </div>
                                </div>
                                <div class="px-8 py-5 shrink-0 w-[200px] hidden md:block">
                                    <p class="text-sm font-medium truncate">{{ reg.nama_wali }}</p>
                                    <p class="text-xs text-[#d02e5c] font-bold">{{ reg.no_hp_wali }}</p>
                                </div>
                                <div class="px-6 py-4 shrink-0 w-[150px] hidden xl:block">
                                    <div v-if="reg.referrer" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#d02e5c]/10 text-[#d02e5c] text-xs font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                        {{ reg.referrer.name }}
                                    </div>
                                    <span v-else class="text-sm text-[#a8a196]">-</span>
                                </div>
                                <div class="px-8 py-5 shrink-0 w-[120px] hidden lg:flex">
                                    <div class="flex gap-2 text-center items-center justify-center">
                                        <a v-if="reg.berkas_kk" :href="'/storage/' + reg.berkas_kk" target="_blank" class="w-8 h-8 rounded-lg bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-[#a8a196] hover:text-[#d02e5c] transition-colors border border-transparent hover:border-[#d02e5c]/20" title="KK">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </a>
                                        <a v-if="reg.berkas_akta" :href="'/storage/' + reg.berkas_akta" target="_blank" class="w-8 h-8 rounded-lg bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-[#a8a196] hover:text-[#d02e5c] transition-colors border border-transparent hover:border-[#d02e5c]/20" title="Akta">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </a>
                                        <a v-if="reg.berkas_ijazah" :href="'/storage/' + reg.berkas_ijazah" target="_blank" class="w-8 h-8 rounded-lg bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-[#a8a196] hover:text-[#d02e5c] transition-colors border border-transparent hover:border-[#d02e5c]/20" title="Ijazah">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="px-8 py-5 shrink-0 w-[150px] hidden sm:block">
                                    <span 
                                        :class="[
                                            'px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-wider block text-center',
                                            reg.status_seleksi === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                            reg.status_seleksi === 'diterima' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                            reg.status_seleksi === 'cadangan' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                                            'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
                                        ]"
                                    >
                                        {{ reg.status_seleksi }}
                                    </span>
                                </div>
                                <div class="px-8 py-5 shrink-0 w-[60px] text-right flex items-center justify-end text-[#d0cec9]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Empty state -->
                        <div v-if="registrations.data.length === 0" class="py-16 text-center text-[#a8a196] font-medium italic">
                            Belum ada pendaftar yang masuk.
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="px-8 py-6 border-t border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between bg-gray-50/30 dark:bg-[#1a1918]/30">
                    <p class="text-xs text-[#a8a196]">Menampilkan {{ registrations.total }} pendaftar</p>
                    <div class="flex gap-2">
                         <!-- Pagination content -->
                    </div>
                </div>
            </div>
        </div>

        <!-- View Responses Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" aria-hidden="true" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#161514] rounded-2xl shadow-xl shadow-black/20 sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-8 border border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5] border-b border-[#ebeae8] dark:border-[#3e3c3a] pb-4 mb-6" id="modal-title">
                                Detail Pendaftar: <span class="text-brand-rose dark:text-brand-rose-dark">{{ selectedRegistration?.nama_calon }}</span>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-h-[70vh] overflow-y-auto custom-scrollbar pr-4">
                                <!-- Stage 1: Basic Info -->
                                <div class="space-y-6">
                                    <div class="bg-gray-50 dark:bg-[#1a1918] p-6 rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-4">Informasi Dasar</h4>
                                        <div class="space-y-4">
                                            <div>
                                                <p class="text-xs text-[#a8a196]">Nama Lengkap</p>
                                                <p class="text-base font-bold text-[#161514] dark:text-[#f2e8d5]">{{ selectedRegistration?.nama_calon }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-[#a8a196]">Jenis Kelamin</p>
                                                <p class="text-base font-bold text-[#161514] dark:text-[#f2e8d5]">{{ selectedRegistration?.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-[#a8a196]">Nama Wali</p>
                                                <p class="text-base font-bold text-[#161514] dark:text-[#f2e8d5]">{{ selectedRegistration?.nama_wali }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-[#a8a196]">Kontak Wali</p>
                                                <p class="text-base font-bold text-brand-rose dark:text-brand-rose-dark">{{ selectedRegistration?.no_hp_wali }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-[#1a1918] p-6 rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-4">Dokumen Tahap 1</h4>
                                        <div class="flex flex-wrap gap-3">
                                            <a v-if="selectedRegistration?.berkas_kk" :href="'/storage/' + selectedRegistration.berkas_kk" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm font-bold hover:text-brand-rose dark:hover:text-brand-rose-vibrant transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Kartu Keluarga
                                            </a>
                                            <a v-if="selectedRegistration?.berkas_akta" :href="'/storage/' + selectedRegistration.berkas_akta" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm font-bold hover:text-brand-rose dark:hover:text-brand-rose-vibrant transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Akta Kelahiran
                                            </a>
                                            <a v-if="selectedRegistration?.berkas_ijazah" :href="'/storage/' + selectedRegistration.berkas_ijazah" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm font-bold hover:text-brand-rose dark:hover:text-brand-rose-vibrant transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                                                Ijazah / SKL
                                            </a>
                                            <p v-if="!selectedRegistration?.berkas_kk && !selectedRegistration?.berkas_akta && !selectedRegistration?.berkas_ijazah" class="text-xs italic text-[#a8a196]">Belum ada berkas diunggah.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stage 2: Dynamic Form Responses -->
                                <div class="space-y-6">
                                    <div class="bg-gray-50 dark:bg-[#1a1918] p-6 rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Biodata Lengkap (Tahap 2)</h4>
                                            <span v-if="selectedRegistration?.responses?.length > 0" class="px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-[10px] font-bold rounded-md uppercase">Sudah Diisi</span>
                                            <span v-else class="px-2 py-0.5 bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 text-[10px] font-bold rounded-md uppercase">Belum Diisi</span>
                                        </div>
                                        
                                        <div class="space-y-6">
                                            <div v-for="response in selectedRegistration?.responses" :key="response.id" class="border-b border-[#ebeae8]/50 dark:border-[#3e3c3a]/50 pb-4 last:border-0 last:pb-0">
                                                <h5 class="text-xs font-bold text-[#a8a196] mb-1.5">{{ response.question?.question_text }}</h5>
                                                
                                                <div v-if="response.question?.type === 'file' && response.response_file_path">
                                                    <a :href="'/storage/' + response.response_file_path" target="_blank" class="inline-flex items-center gap-2 text-brand-rose dark:text-brand-rose-vibrant font-bold text-xs bg-brand-rose-muted dark:bg-brand-rose-surface px-3 py-1.5 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                        Lihat Dokumen
                                                    </a>
                                                </div>
                                                <div v-else class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5]">
                                                    {{ response.response_text || '-' }}
                                                </div>
                                            </div>

                                            <p v-if="selectedRegistration?.responses?.length === 0" class="text-sm italic text-[#a8a196] text-center py-4">Pendaftar belum mengisi biodata formulir tahap 2.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 sm:flex justify-end gap-3 border-t border-[#ebeae8] dark:border-[#3e3c3a] pt-6">
                        <RedwoodButton @click="closeModal" variant="outline" class="w-full sm:w-auto">Tutup Detail</RedwoodButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
