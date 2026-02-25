<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    santris: Object,
    filters: Object,
    options: Object
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const kelas = ref(props.filters.kelas || '');
const kamar = ref(props.filters.kamar || '');

let timeout = null;
watch([search, status, kelas, kamar], () => {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('santri.index'), {
            search: search.value,
            status: status.value,
            kelas: kelas.value,
            kamar: kamar.value,
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
                            <option value="aktif">Aktif</option>
                            <option value="alumni">Alumni</option>
                            <option value="keluar">Keluar</option>
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

            <!-- Table Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">

                <!-- Header Row -->
                <div class="flex items-center border-b border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50/50 dark:bg-[#1a1918]/50 px-0">
                    <div @click="sort('nis')" class="w-32 shrink-0 px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] cursor-pointer hover:text-[#c97e60] transition-colors flex items-center gap-1">
                        NIS <svg v-if="filters.sort_by === 'nis'" class="w-3 h-3" :class="filters.sort_dir === 'asc' ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </div>
                    <div @click="sort('nama')" class="flex-1 min-w-0 px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] cursor-pointer hover:text-[#c97e60] transition-colors flex items-center gap-1">
                        Nama Santri <svg v-if="filters.sort_by === 'nama'" class="w-3 h-3" :class="filters.sort_dir === 'asc' ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </div>
                    <div class="w-40 shrink-0 px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] hidden md:block">Wali</div>
                    <div @click="sort('status')" class="w-[116px] shrink-0 px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] cursor-pointer hover:text-[#c97e60] transition-colors hidden lg:flex items-center gap-1">
                        Status <svg v-if="filters.sort_by === 'status'" class="w-3 h-3" :class="filters.sort_dir === 'asc' ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </div>
                    <div class="w-36 shrink-0 px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] hidden lg:block">Kelas / Kamar</div>
                    <div class="w-10 shrink-0 pr-4 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] text-right">←</div>
                </div>

                <!-- Swipeable Rows -->
                <div class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                    <div v-for="santri in santris.data" :key="santri.id"
                         class="relative overflow-hidden"
                         @mouseenter="hoveredId = santri.id"
                         @mouseleave="hoveredId = null"
                    >
                        <!-- Revealed action buttons (hidden behind row) -->
                        <div class="absolute inset-y-0 right-0 flex" style="width:144px">
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
                            class="flex items-center bg-white dark:bg-[#161514] hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors cursor-grab active:cursor-grabbing"
                            :style="{
                                transform: `translateX(${rowOffset(santri.id)}px)`,
                                transition: dragging ? 'none' : 'transform 0.25s cubic-bezier(0.25,1,0.5,1)',
                                touchAction: 'pan-y',
                            }"
                            @pointerdown="(e) => onPointerDown(e, santri.id)"
                            @pointermove="(e) => onPointerMove(e, santri.id)"
                            @pointerup="(e) => onPointerUp(e, santri.id)"
                        >
                            <!-- NIS -->
                            <div class="w-32 shrink-0 px-8 py-5">
                                <span class="font-mono text-xs text-[#c97e60] bg-[#c97e60]/10 px-2 py-1 rounded-md">{{ santri.nis || 'N/A' }}</span>
                            </div>
                            <!-- Nama -->
                            <div class="flex-1 min-w-0 px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-sm font-bold border border-[#ebeae8] dark:border-[#3e3c3a] shrink-0">
                                        {{ santri.nama?.charAt(0) ?? '?' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#161514] dark:text-[#f2e8d5] truncate">{{ santri.nama }}</p>
                                        <p class="text-[10px] text-[#a8a196]">{{ santri.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Wali -->
                            <div class="w-40 shrink-0 px-8 py-5 hidden md:block">
                                <p class="text-sm font-medium truncate">{{ santri.wali?.nama ?? '-' }}</p>
                                <p class="text-[10px] text-[#a8a196] italic">{{ santri.wali?.hubungan }}</p>
                            </div>
                            <!-- Status -->
                            <div class="w-[116px] shrink-0 px-8 py-5 hidden lg:block">
                                <span :class="[
                                    'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider',
                                    santri.status === 'aktif' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                    santri.status === 'alumni' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                                    'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
                                ]">{{ santri.status }}</span>
                            </div>
                            <!-- Kelas/Kamar -->
                            <div class="w-36 shrink-0 px-8 py-5 hidden lg:block">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-xs">
                                        <svg class="w-3 h-3 text-[#c97e60] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        <span class="truncate">{{ santri.kelas || '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <svg class="w-3 h-3 text-[#a8a196] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
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
    </AuthenticatedLayout>
</template>
