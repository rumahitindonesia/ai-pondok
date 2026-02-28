<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    santris: Object,
    filters: Object,
    options: Object,
    stats: Object
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const kelas = ref(props.filters.kelas || '');
const kamar = ref(props.filters.kamar || '');
const angkatan = ref(props.filters.angkatan || '');
const entitas = ref(props.filters.entitas || '');

let timeout = null;
watch([search, status, kelas, kamar, angkatan, entitas], () => {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('santri.index'), {
            search: search.value,
            status: status.value,
            kelas: kelas.value,
            kamar: kamar.value,
            angkatan: angkatan.value,
            entitas: entitas.value,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
        }, { 
            preserveState: true,
            preserveScroll: true,
            replace: true 
        });
    }, 300);
});

const sort = (field) => {
    let dir = props.filters.sort_dir === 'asc' ? 'desc' : 'asc';
    if (props.filters.sort_by !== field) dir = 'asc';
    
    router.get(route('santri.index'), {
        ...props.filters,
        sort_by: field,
        sort_dir: dir
    }, { preserveState: true, preserveScroll: true });
};

// --- Swipe to Reveal ---
const REVEAL_WIDTH = 144;
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

// --- Bulk Selection ---
const selectedIds = ref([]);
const bulkStatus = ref('');
const isProcessingBulk = ref(false);

const isAllSelected = computed(() => {
    return props.santris.data.length > 0 && selectedIds.value.length === props.santris.data.length;
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.santris.data.map(s => s.id);
    }
};

const toggleSelect = (id) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
};

const applyBulkStatus = () => {
    if (!bulkStatus.value || selectedIds.value.length === 0) return;
    
    isProcessingBulk.value = true;
    router.post(route('santri.bulk-status'), {
        ids: selectedIds.value,
        status: bulkStatus.value
    }, {
        onSuccess: () => {
            selectedIds.value = [];
            bulkStatus.value = '';
            isProcessingBulk.value = false;
        },
        onError: () => {
            isProcessingBulk.value = false;
        }
    });
};
</script>

<template>
    <Head title="Manajemen Santri" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Database <span class="text-[#c97e60]">Santri</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Action Bar -->
            <div class="space-y-6">
                <!-- Search Row (Full Width) -->
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-[#a8a196]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input 
                        v-model="search"
                        type="text" 
                        placeholder="Cari nama atau NIS secara cepat..."
                        class="block w-full pl-12 pr-4 py-4 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl text-base focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all shadow-sm font-bold"
                    >
                </div>

                <!-- Filters & Action Row -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <RedwoodButton 
                            as="Link"
                            :href="route('santri.settings')"
                            variant="outline"
                            class="group"
                        >
                            <template #icon>
                                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </template>
                        </RedwoodButton>

                        <!-- Status Filter -->
                        <select 
                            v-model="status" 
                            class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm min-w-[150px]"
                        >
                            <option value="">Semua Status</option>
                            <option value="Santri Aktif">Santri Aktif</option>
                            <option value="Santri Keluar">Santri Keluar</option>
                            <option value="Santri Lulus - Alumni">Santri Lulus - Alumni</option>
                            <option value="Alumni Aktif - dipondok">Alumni Aktif - dipondok</option>
                            <option value="Alumni Tidak Aktif - diluar pondok">Alumni Tidak Aktif - diluar pondok</option>
                            <option value="Santri Magang">Santri Magang</option>
                            <option value="Alumni Magang">Alumni Magang</option>
                        </select>

                        <!-- Kelas Filter -->
                        <select 
                            v-model="kelas" 
                            class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm min-w-[150px]"
                        >
                            <option value="">Semua Kelas</option>
                            <option v-for="k in options.kelas" :key="k" :value="k">{{ k }}</option>
                        </select>

                        <!-- Kamar Filter -->
                        <select 
                            v-model="kamar" 
                            class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm min-w-[150px]"
                        >
                            <option value="">Semua Kamar</option>
                            <option v-for="k in options.kamar" :key="k" :value="k">{{ k }}</option>
                        </select>

                        <!-- Angkatan Filter -->
                        <select 
                            v-model="angkatan" 
                            class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm min-w-[150px]"
                        >
                            <option value="">Semua Angkatan</option>
                            <option v-for="a in options.angkatan" :key="a" :value="a">Angkatan {{ a }}</option>
                        </select>

                        <!-- Entitas Filter -->
                        <select 
                            v-model="entitas" 
                            class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm min-w-[150px]"
                        >
                            <option value="">Semua Entitas</option>
                            <option v-for="e in options.entitas" :key="e" :value="e">{{ e }}</option>
                        </select>
                    </div>
                    
                    <RedwoodButton 
                        as="Link"
                        :href="route('santri.create')"
                        variant="primary"
                    >
                        <template #icon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </template>
                        Tambah Santri
                    </RedwoodButton>
                </div>
            </div>

            <!-- Stats Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total -->
                <div class="bg-white dark:bg-[#161514] p-6 rounded-[24px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm group hover:border-[#c97e60] transition-all">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-2">Total Santri</p>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-black text-[#161514] dark:text-[#f2e8d5] tracking-tighter">{{ stats.total }}</span>
                        <span class="text-[10px] font-bold text-[#a8a196] mb-1">Jiwa</span>
                    </div>
                </div>
                <!-- Aktif -->
                <div class="bg-white dark:bg-[#161514] p-6 rounded-[24px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm group hover:border-emerald-500/30 transition-all">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-2 text-emerald-500/70">Santri Aktif</p>
                    <div class="flex items-end gap-2 text-emerald-500">
                        <span class="text-3xl font-black tracking-tighter">{{ stats.aktif }}</span>
                        <span class="text-[10px] font-bold opacity-70 mb-1 text-[#a8a196]">Aktif</span>
                    </div>
                </div>
                <!-- Alumni -->
                <div class="bg-white dark:bg-[#161514] p-6 rounded-[24px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm group hover:border-blue-500/30 transition-all">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-2 text-blue-500/70">Alumni</p>
                    <div class="flex items-end gap-2 text-blue-500">
                        <span class="text-3xl font-black tracking-tighter">{{ stats.alumni }}</span>
                        <span class="text-[10px] font-bold opacity-70 mb-1 text-[#a8a196]">Lulus</span>
                    </div>
                </div>
                <!-- Keluar -->
                <div class="bg-white dark:bg-[#161514] p-6 rounded-[24px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm group hover:border-rose-500/30 transition-all">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-2 text-rose-500/70">Keluar</p>
                    <div class="flex items-end gap-2 text-rose-500">
                        <span class="text-3xl font-black tracking-tighter">{{ stats.keluar }}</span>
                        <span class="text-[10px] font-bold opacity-70 mb-1 text-[#a8a196]">Jiwa</span>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">

                <!-- Header Row -->
                <div class="flex items-center border-b border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50/50 dark:bg-[#1a1918]/50 px-0">
                    <div class="w-16 shrink-0 flex items-center justify-center py-5">
                        <input 
                            type="checkbox" 
                            :checked="isAllSelected"
                            @change="toggleSelectAll"
                            class="w-5 h-5 rounded-lg border-[#ebeae8] dark:border-[#3e3c3a] text-[#c97e60] focus:ring-[#c97e60] dark:bg-[#161514] transition-all cursor-pointer"
                        >
                    </div>
                    <div @click="sort('nama')" class="flex-1 min-w-0 px-4 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] cursor-pointer hover:text-[#c97e60] transition-colors flex items-center gap-1">
                        Informasi Santri / Nama <svg v-if="filters.sort_by === 'nama'" class="w-3 h-3" :class="filters.sort_dir === 'asc' ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </div>
                    <div @click="sort('status')" class="w-48 shrink-0 px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] cursor-pointer hover:text-[#c97e60] transition-colors hidden lg:flex items-center gap-1">
                        Status <svg v-if="filters.sort_by === 'status'" class="w-3 h-3" :class="filters.sort_dir === 'asc' ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </div>
                    <div class="w-44 shrink-0 px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] hidden lg:block">Kelas / Kamar</div>
                    <div class="w-10 shrink-0 pr-4 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] text-right">←</div>
                </div>

                <!-- Swipeable Rows -->
                <div class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                    <div v-for="santri in santris.data" :key="santri.id"
                         class="relative flex items-center bg-white dark:bg-[#161514] hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors overflow-hidden"
                         @mouseenter="hoveredId = santri.id"
                         @mouseleave="hoveredId = null"
                    >
                        <!-- Static Checkbox -->
                        <div class="w-16 shrink-0 flex items-center justify-center py-5 z-20 bg-inherit border-r border-[#ebeae8]/50 dark:border-[#3e3c3a]/50">
                            <input 
                                type="checkbox" 
                                :checked="selectedIds.includes(santri.id)"
                                @change="toggleSelect(santri.id)"
                                @pointerdown.stop
                                @mousedown.stop
                                class="w-5 h-5 rounded-lg border-[#ebeae8] dark:border-[#3e3c3a] text-[#c97e60] focus:ring-[#c97e60] dark:bg-[#161514] transition-all cursor-pointer relative z-30"
                            >
                        </div>

                        <!-- Swipeable Area -->
                        <div class="flex-1 relative overflow-hidden">
                            <!-- Revealed action buttons (Fixed behind row) -->
                            <div class="absolute inset-y-0 right-0 flex z-0" style="width:144px">
                                <Link
                                    :href="route('santri.show', santri.id)"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-teal-500 text-white text-[10px] font-black uppercase hover:brightness-110 active:brightness-90 transition-all"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Lihat
                                </Link>
                                <Link
                                    :href="route('santri.edit', santri.id)"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-blue-500 text-white text-[10px] font-black uppercase hover:brightness-110 active:brightness-90 transition-all"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit
                                </Link>
                            </div>

                            <!-- Swipeable content row -->
                            <div
                                class="flex items-center bg-white dark:bg-[#161514] w-full relative z-10 cursor-grab active:cursor-grabbing"
                                :style="{
                                    transform: `translateX(${rowOffset(santri.id)}px)`,
                                    transition: dragging ? 'none' : 'transform 0.25s cubic-bezier(0.25,1,0.5,1)',
                                    touchAction: 'pan-y',
                                }"
                                @pointerdown="(e) => onPointerDown(e, santri.id)"
                                @pointermove="(e) => onPointerMove(e, santri.id)"
                                @pointerup="(e) => onPointerUp(e, santri.id)"
                            >
                                <!-- Nama & Info -->
                                <div class="flex-1 min-w-0 px-4 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-[#c97e60]/10 flex items-center justify-center overflow-hidden border border-[#ebeae8] dark:border-[#3e3c3a] shrink-0">
                                            <img v-if="santri.foto" :src="`/storage/${santri.foto}`" class="w-full h-full object-cover">
                                            <span v-else class="text-[#c97e60] font-bold text-xs uppercase">{{ santri.nama?.charAt(0) ?? '?' }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[#161514] dark:text-[#f2e8d5] truncate flex items-center gap-2">
                                                {{ santri.nama }}
                                                <span v-if="santri.entitas" class="font-mono text-[9px] px-1.5 py-0.5 rounded bg-[#f2e8d5] dark:bg-[#3e3c3a] text-[#c97e60]">{{ santri.entitas }}</span>
                                            </p>
                                            <div class="flex items-center gap-2 text-[10px] text-[#a8a196] font-medium">
                                                <span>{{ santri.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                                <span class="text-[#ebeae8] dark:text-[#3e3c3a]">•</span>
                                                <span v-if="santri.angkatan" class="text-[#c97e60]">Akt. {{ santri.angkatan }}</span>
                                                <span v-if="santri.angkatan && santri.nis" class="text-[#ebeae8] dark:text-[#3e3c3a]">•</span>
                                                <span class="font-mono text-[9px]">{{ santri.nis || 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="w-48 shrink-0 px-8 py-5 hidden lg:block">
                                    <span :class="[
                                        'px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider block text-center shadow-sm',
                                        santri.status === 'Santri Aktif' || santri.status === 'Alumni Aktif - dipondok' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' :
                                        santri.status === 'Santri Keluar' || santri.status === 'Alumni Tidak Aktif - diluar pondok' ? 'bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/30' :
                                        santri.status === 'Santri Lulus - Alumni' ? 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30' :
                                        santri.status === 'Santri Magang' || santri.status === 'Alumni Magang' ? 'bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30' :
                                        'bg-gray-50 text-gray-600 border border-gray-100'
                                    ]">{{ santri.status }}</span>
                                </div>
                                <!-- Kelas/Kamar -->
                                <div class="w-44 shrink-0 px-8 py-5 hidden lg:block">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center gap-2 text-xs font-bold text-[#161514] dark:text-[#f2e8d5]">
                                            <svg class="w-3.5 h-3.5 text-[#c97e60] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            <span class="truncate">{{ santri.kelas || '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-medium text-[#a8a196]">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                            <span class="truncate">{{ santri.kamar || '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Swipe hint -->
                                <div class="w-10 shrink-0 pr-4 py-5 flex items-center justify-end text-[#d0cec9]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="santris.data.length === 0" class="py-16 text-center text-[#a8a196] font-medium italic">
                        Tidak ada data santri yang ditemukan.
                    </div>
                </div>

                
                <!-- Pagination -->
                <div class="px-8 py-6 border-t border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between bg-gray-50/30 dark:bg-[#1a1918]/30">
                    <p class="text-xs text-[#a8a196]">Menampilkan {{ santris.from }} - {{ santris.to }} dari {{ santris.total }} santri</p>
                    <div class="flex gap-2">
                        <template v-for="link in santris.links" :key="link.label">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-4 py-2 text-xs font-bold rounded-xl transition-all',
                                    link.active ? 'bg-[#c97e60] text-white shadow-md' : 'bg-white dark:bg-[#262524] text-[#a8a196] hover:text-[#c97e60] border border-[#ebeae8] dark:border-[#3e3c3a]'
                                ]"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Bulk Action Bar -->
        <Transition
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="transform translate-y-24 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-400 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform translate-y-24 opacity-0"
        >
            <div v-if="selectedIds.length > 0" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 w-full max-w-2xl px-4">
                <div class="bg-[#161514] dark:bg-[#f2e8d5] text-[#f2e8d5] dark:text-[#161514] rounded-[32px] p-4 shadow-[0_25px_60px_rgba(0,0,0,0.5)] border border-[#3e3c3a] dark:border-white/20 flex flex-col md:flex-row items-center gap-4">
                    <div class="flex items-center gap-4 px-4 border-r border-[#3e3c3a] dark:border-gray-200/20">
                        <div class="w-10 h-10 rounded-full bg-[#c97e60] text-white flex items-center justify-center font-black text-sm">
                            {{ selectedIds.length }}
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-[9px] font-black uppercase tracking-widest opacity-50">Terpilih</p>
                            <p class="text-xs font-bold uppercase tracking-widest">Santri</p>
                        </div>
                    </div>

                    <div class="flex-1 flex items-center gap-2 w-full">
                        <select 
                            v-model="bulkStatus"
                            class="flex-1 bg-transparent border-none text-sm font-bold focus:ring-0 outline-none cursor-pointer py-2 pl-0"
                        >
                            <option value="" disabled class="text-[#161514]">Pilih Status Baru...</option>
                            <option value="Santri Aktif" class="text-[#161514]">Santri Aktif</option>
                            <option value="Santri Keluar" class="text-[#161514]">Santri Keluar</option>
                            <option value="Santri Lulus - Alumni" class="text-[#161514]">Santri Lulus - Alumni</option>
                            <option value="Alumni Aktif - dipondok" class="text-[#161514]">Alumni Aktif - dipondok</option>
                            <option value="Alumni Tidak Aktif - diluar pondok" class="text-[#161514]">Alumni Tidak Aktif - diluar pondok</option>
                            <option value="Santri Magang" class="text-[#161514]">Santri Magang</option>
                            <option value="Alumni Magang" class="text-[#161514]">Alumni Magang</option>
                        </select>
                    </div>

                    <RedwoodButton 
                        @click="applyBulkStatus"
                        variant="primary"
                        class="w-full md:w-auto"
                        :disabled="!bulkStatus || isProcessingBulk"
                    >
                        <template #icon>
                            <svg v-if="!isProcessingBulk" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg v-else class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        Terapkan
                    </RedwoodButton>

                    <button 
                        @click="selectedIds = []" 
                        class="p-4 hover:text-[#c97e60] transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>
