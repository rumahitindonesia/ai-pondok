<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    santris: Array,
    jenisPembayarans: Array,
    entitasOptions: Array,
});

import { ref, watch } from 'vue';

const selectedEntitas = ref('');
const selectedAngkatan = ref('');

// Get unique angkatans for the selected entitas
const availableAngkatans = computed(() => {
    if (!selectedEntitas.value) return [];
    const angkatans = props.santris
        .filter(s => s.entitas === selectedEntitas.value)
        .map(s => s.angkatan)
        .filter(Boolean);
    return [...new Set(angkatans)].sort((a, b) => b - a);
});

// Filter santri based on entitas and angkatan selections
const filteredSantris = computed(() => {
    return props.santris.filter(s => {
        const matchEntitas = !selectedEntitas.value || s.entitas === selectedEntitas.value;
        const matchAngkatan = !selectedAngkatan.value || s.angkatan == selectedAngkatan.value;
        return matchEntitas && matchAngkatan;
    });
});

// Reset angkatan and santri when entitas changes
watch(selectedEntitas, () => {
    selectedAngkatan.value = '';
    form.santri_id = '';
});

// Reset santri when angkatan changes
watch(selectedAngkatan, () => {
    form.santri_id = '';
});

const form = useForm({
    santri_id: '',
    jenis_pembayaran_id: '',
    bulan: '',
    tahun: new Date().getFullYear(),
    jumlah: '',
    jatuh_tempo: '',
    keterangan: '',
});

const selectedJenis = computed(() =>
    props.jenisPembayarans.find(j => j.id == form.jenis_pembayaran_id)
);

const isBulanan = computed(() => selectedJenis.value?.sifat === 'bulanan');

// Auto-fill nominal default when jenis changes
const onJenisChange = () => {
    if (selectedJenis.value?.nominal_default) {
        form.jumlah = selectedJenis.value.nominal_default;
    }
};

const submit = () => {
    form.post(route('finance.billings.store'));
};

const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
</script>

<template>
    <Head title="Input Tagihan Manual" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <RedwoodButton as="Link" :href="route('finance.billings')" variant="ghost">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </template>
                </RedwoodButton>
                <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                    Input Tagihan <span class="text-[#d02e5c]">Manual</span>
                </h2>
            </div>
        </template>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-[#161514] shadow-[0_30px_60px_rgba(0,0,0,0.05)] dark:shadow-[0_30px_60px_rgba(0,0,0,0.3)] rounded-[40px] border border-[#ebeae8] dark:border-[#3e3c3a] p-12">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Jenis Pembayaran -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jenis Pembayaran</label>
                        <select v-model="form.jenis_pembayaran_id" @change="onJenisChange" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all font-bold" required>
                            <option value="" disabled>-- Pilih Jenis Pembayaran --</option>
                            <option v-for="jenis in jenisPembayarans" :key="jenis.id" :value="jenis.id">
                                {{ jenis.nama }}
                                <template v-if="jenis.sifat === 'bulanan'"> (Bulanan)</template>
                                <template v-else-if="jenis.sifat === 'cicilan'"> (Bisa Dicicil)</template>
                                <template v-else> (Sekali Bayar)</template>
                            </option>
                        </select>
                        <div v-if="form.errors.jenis_pembayaran_id" class="text-xs text-rose-500">{{ form.errors.jenis_pembayaran_id }}</div>
                    </div>

                    <!-- Filter Kelompok Santri -->
                    <div class="grid grid-cols-2 gap-6 bg-[#fcf8f5]/50 dark:bg-[#21201f]/50 p-6 rounded-[28px] border border-[#ebeae8] dark:border-[#3e3c3a]">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Filter Entitas</label>
                            <select v-model="selectedEntitas" class="w-full bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c] transition-all font-bold">
                                <option value="">Semua Entitas</option>
                                <option v-for="ent in entitasOptions" :key="ent" :value="ent">{{ ent }}</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Filter Angkatan</label>
                            <select v-model="selectedAngkatan" :disabled="!selectedEntitas" class="w-full bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c] transition-all font-bold disabled:opacity-50">
                                <option value="">Semua Angkatan</option>
                                <option v-for="ang in availableAngkatans" :key="ang" :value="ang">Angkatan {{ ang }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pilih Santri -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Pilih Santri ({{ filteredSantris.length }} Orang)</label>
                        <select v-model="form.santri_id" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all" required>
                            <option value="" disabled>-- Pilih Nama Santri --</option>
                            <option v-for="santri in filteredSantris" :key="santri.id" :value="santri.id">
                                {{ santri.nama }} ({{ santri.nis || 'No NIS' }}) - {{ santri.entitas }} {{ santri.angkatan ? 'Akt. ' + santri.angkatan : '' }}
                            </option>
                        </select>
                        <div v-if="form.errors.santri_id" class="text-xs text-rose-500">{{ form.errors.santri_id }}</div>
                    </div>

                    <!-- Bulan & Tahun (hanya untuk SPP Bulanan) -->
                    <div v-if="isBulanan" class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Bulan</label>
                            <select v-model="form.bulan" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]" :required="isBulanan">
                                <option value="" disabled>Pilih Bulan</option>
                                <option v-for="m in months" :key="m" :value="m">{{ m }}</option>
                            </select>
                            <div v-if="form.errors.bulan" class="text-xs text-rose-500">{{ form.errors.bulan }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Tahun</label>
                            <input v-model="form.tahun" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]" :required="isBulanan">
                        </div>
                    </div>

                    <!-- Nominal -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nominal Tagihan (Rp)</label>
                        <input v-model="form.jumlah" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none font-bold text-xl text-[#d02e5c] focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all" required>
                        <div v-if="form.errors.jumlah" class="text-xs text-rose-500">{{ form.errors.jumlah }}</div>
                    </div>

                    <!-- Jatuh Tempo -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jatuh Tempo</label>
                        <input v-model="form.jatuh_tempo" type="date" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]" required>
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan (opsional)</label>
                        <textarea v-model="form.keterangan" placeholder="Misal: cicilan ke-2, dll" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c] min-h-[80px] text-sm"></textarea>
                    </div>

                    <div class="pt-2">
                        <RedwoodButton type="submit" variant="secondary" size="lg" :disabled="form.processing" class="w-full">
                            <template #icon>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            </template>
                            {{ form.processing ? 'Menyimpan...' : 'Terbitkan Tagihan' }}
                        </RedwoodButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
