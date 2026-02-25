<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    jenisPembayarans: Array,
});

// --- Add Form ---
const form = useForm({ nama: '', kode: '', sifat: 'sekali', nominal_default: '', keterangan: '' });
const submit = () => form.post(route('finance.jenis.store'), { onSuccess: () => form.reset() });

// --- Edit Form ---
const editingId = ref(null);
const editForm = useForm({ nama: '', kode: '', sifat: 'sekali', nominal_default: '', keterangan: '' });

const startEdit = (jenis) => {
    swipedId.value = null; // close swipe
    editingId.value = jenis.id;
    editForm.nama = jenis.nama;
    editForm.kode = jenis.kode;
    editForm.sifat = jenis.sifat;
    editForm.nominal_default = jenis.nominal_default ?? '';
    editForm.keterangan = jenis.keterangan ?? '';
};

const cancelEdit = () => { editingId.value = null; editForm.reset(); };
const saveEdit = (id) => editForm.patch(route('finance.jenis.update', id), { onSuccess: () => { editingId.value = null; } });

// --- Delete ---
const deleteJenis = (id) => {
    swipedId.value = null;
    if (confirm('Hapus jenis pembayaran ini?')) {
        router.delete(route('finance.jenis.destroy', id), { preserveScroll: true });
    }
};

// --- Swipe to Reveal ---
const REVEAL_WIDTH = 128;
const SWIPE_THRESHOLD = 50;

const swipedId = ref(null);
const hoveredId = ref(null);
const dragging = ref(false);
const currentOffset = ref(0);
const startX = ref(0);
const startOffset = ref(0);

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
    // Only allow left swipe (negative), clamp between -REVEAL_WIDTH and 0
    currentOffset.value = id === (swipedId.value ?? id) ? Math.min(0, Math.max(-REVEAL_WIDTH, raw)) : 0;
    if (raw < -SWIPE_THRESHOLD / 2 && swipedId.value !== id) {
        swipedId.value = id;
    }
};

const onPointerUp = (e, id) => {
    if (!dragging.value) return;
    dragging.value = false;
    const dx = e.clientX - startX.value;
    const total = startOffset.value + dx;
    // Snap: open if dragged past threshold, close otherwise
    if (total < -SWIPE_THRESHOLD) {
        swipedId.value = id;
    } else {
        swipedId.value = null;
    }
    currentOffset.value = 0;
};

const itemOffset = (id) => {
    if (editingId.value === id) return 0;
    if (swipedId.value === id) {
        if (dragging.value) return currentOffset.value;
        return -REVEAL_WIDTH;
    }
    if (hoveredId.value === id && !dragging.value) return -REVEAL_WIDTH;
    return 0;
};

const closeAllSwipe = (exceptId = null) => {
    if (swipedId.value !== exceptId) swipedId.value = null;
};

const sifatLabel = { bulanan: 'Bulanan', sekali: 'Sekali', cicilan: 'Bisa Dicicil' };
const sifatColor = { bulanan: 'bg-blue-100 text-blue-700', sekali: 'bg-emerald-100 text-emerald-700', cicilan: 'bg-amber-100 text-amber-700' };
const formatCurrency = (v) => v ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v) : '-';
</script>

<template>
    <Head title="Pengaturan Keuangan" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <Link :href="route('finance.index')" class="p-3 text-[#a8a196] hover:text-[#c97e60] transition-all bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl shadow-sm group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                    Pengaturan <span class="text-[#c97e60]">Jenis Pembayaran</span>
                </h2>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Form Tambah -->
            <div class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm self-start">
                <h3 class="text-xl font-black mb-6">Tambah Jenis Pembayaran</h3>
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama</label>
                        <input v-model="form.nama" type="text" placeholder="Misal: Infak Tahunan" class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60]" required>
                        <div v-if="form.errors.nama" class="text-xs text-rose-500">{{ form.errors.nama }}</div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Kode (unik)</label>
                        <input v-model="form.kode" type="text" placeholder="Misal: infak_tahunan" class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm font-mono focus:ring-2 focus:ring-[#c97e60]" required>
                        <div v-if="form.errors.kode" class="text-xs text-rose-500">{{ form.errors.kode }}</div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Sifat Pembayaran</label>
                        <select v-model="form.sifat" class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60]" required>
                            <option value="sekali">Sekali Bayar</option>
                            <option value="bulanan">Bulanan (tiap bulan)</option>
                            <option value="cicilan">Bisa Dicicil</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nominal Default (Rp, opsional)</label>
                        <input v-model="form.nominal_default" type="number" step="1000" placeholder="0" class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60]">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan (opsional)</label>
                        <textarea v-model="form.keterangan" class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60] min-h-[80px]"></textarea>
                    </div>
                    <RedwoodButton type="submit" class="w-full" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan...' : 'Tambah Jenis Pembayaran' }}
                    </RedwoodButton>
                </form>
            </div>

            <!-- Daftar Jenis -->
            <div class="bg-white dark:bg-[#161514] overflow-hidden rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                <div class="px-8 py-6 border-b border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between">
                    <h3 class="font-black text-lg">Daftar Jenis Pembayaran</h3>
                    <p class="text-[10px] text-[#a8a196] font-bold uppercase tracking-widest hidden sm:block">← Geser untuk aksi</p>
                </div>

                <div class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                    <template v-for="jenis in jenisPembayarans" :key="jenis.id">

                        <!-- Edit Mode (inline form, no swipe) -->
                        <div v-if="editingId === jenis.id" class="p-6 bg-[#fcf8f5] dark:bg-[#1a1918]">
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[9px] font-black uppercase tracking-widest text-[#a8a196]">Nama</label>
                                        <input v-model="editForm.nama" type="text" class="w-full p-3 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm focus:ring-2 focus:ring-[#c97e60]" required>
                                        <div v-if="editForm.errors.nama" class="text-xs text-rose-500 mt-1">{{ editForm.errors.nama }}</div>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black uppercase tracking-widest text-[#a8a196]">Kode</label>
                                        <input v-model="editForm.kode" type="text" class="w-full p-3 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm font-mono focus:ring-2 focus:ring-[#c97e60]" required>
                                        <div v-if="editForm.errors.kode" class="text-xs text-rose-500 mt-1">{{ editForm.errors.kode }}</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[9px] font-black uppercase tracking-widest text-[#a8a196]">Sifat</label>
                                        <select v-model="editForm.sifat" class="w-full p-3 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm focus:ring-2 focus:ring-[#c97e60]">
                                            <option value="sekali">Sekali Bayar</option>
                                            <option value="bulanan">Bulanan</option>
                                            <option value="cicilan">Bisa Dicicil</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black uppercase tracking-widest text-[#a8a196]">Nominal Default</label>
                                        <input v-model="editForm.nominal_default" type="number" step="1000" class="w-full p-3 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm focus:ring-2 focus:ring-[#c97e60]">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan</label>
                                    <input v-model="editForm.keterangan" type="text" class="w-full p-3 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-sm focus:ring-2 focus:ring-[#c97e60]">
                                </div>
                                <div class="flex gap-3 pt-1">
                                    <button @click="saveEdit(jenis.id)" :disabled="editForm.processing" class="flex-1 py-2 bg-[#c97e60] text-white text-xs font-black rounded-xl hover:bg-[#b06a4f] transition-all disabled:opacity-50">
                                        {{ editForm.processing ? 'Menyimpan...' : '✓ Simpan' }}
                                    </button>
                                    <button @click="cancelEdit" class="flex-1 py-2 bg-[#f5f4f2] dark:bg-[#262524] text-[#a8a196] text-xs font-black rounded-xl hover:bg-gray-200 transition-all">
                                        ✕ Batal
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Swipe Row (view mode) -->
                        <div v-else class="relative overflow-hidden select-none"
                             @mouseenter="hoveredId = jenis.id"
                             @mouseleave="hoveredId = null"
                        >
                            <!-- Revealed action buttons (behind the row) -->
                            <div class="absolute inset-y-0 right-0 flex" style="width: 128px">
                                <button
                                    @click="startEdit(jenis)"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-[#c97e60] text-white text-[10px] font-black uppercase transition-all hover:brightness-110 active:brightness-90"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit
                                </button>
                                <button
                                    @click="deleteJenis(jenis.id)"
                                    class="flex-1 flex flex-col items-center justify-center gap-1 bg-rose-500 text-white text-[10px] font-black uppercase transition-all hover:brightness-110 active:brightness-90"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Hapus
                                </button>
                            </div>

                            <!-- Swipeable Row Content -->
                            <div
                                class="relative bg-white dark:bg-[#161514] p-6 flex items-center justify-between cursor-grab active:cursor-grabbing"
                                :style="{
                                    transform: `translateX(${itemOffset(jenis.id)}px)`,
                                    transition: dragging ? 'none' : 'transform 0.25s cubic-bezier(0.25, 1, 0.5, 1)',
                                    touchAction: 'pan-y'
                                }"
                                @pointerdown="(e) => onPointerDown(e, jenis.id)"
                                @pointermove="(e) => onPointerMove(e, jenis.id)"
                                @pointerup="(e) => onPointerUp(e, jenis.id)"
                            >
                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <p class="font-bold text-sm">{{ jenis.nama }}</p>
                                        <span :class="['px-2 py-0.5 text-[9px] font-black uppercase rounded-full', sifatColor[jenis.sifat]]">{{ sifatLabel[jenis.sifat] }}</span>
                                    </div>
                                    <p class="text-xs text-[#a8a196] font-mono">{{ jenis.kode }}</p>
                                    <p v-if="jenis.nominal_default" class="text-xs text-[#c97e60] font-bold mt-0.5">{{ formatCurrency(jenis.nominal_default) }}</p>
                                    <p v-if="jenis.keterangan" class="text-xs text-[#a8a196] mt-0.5 italic">{{ jenis.keterangan }}</p>
                                </div>

                                <!-- Swipe hint indicator -->
                                <div class="flex items-center gap-1 text-[#d0cec9]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                    <svg class="w-4 h-4 -ml-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                </div>
                            </div>
                        </div>

                    </template>
                    <div v-if="jenisPembayarans.length === 0" class="p-10 text-center text-sm text-[#a8a196] italic">
                        Belum ada jenis pembayaran.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
