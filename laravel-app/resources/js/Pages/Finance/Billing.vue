<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    billings: Object,
    entitasOptions: Array,
    statusOptions: Array,
});

const showGenerateModal = ref(false);

const genForm = useForm({
    bulan: '',
    tahun: new Date().getFullYear(),
    jumlah: 500000,
    jatuh_tempo: '',
    entitas: '',
    status: '',
});

const generateBills = () => {
    genForm.post(route('finance.billings.generate'), {
        onSuccess: () => {
            showGenerateModal.value = false;
        }
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
</script>

<template>
    <Head title="Daftar Tagihan SPP" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Daftar <span class="text-[#d02e5c]">Tagihan</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Action Bar -->
            <div class="flex justify-end gap-4">
                <RedwoodButton 
                    as="Link"
                    :href="route('finance.billings.create')"
                    variant="outline"
                >
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </template>
                    Input Manual
                </RedwoodButton>
                <RedwoodButton 
                    variant="primary"
                    @click="showGenerateModal = true"
                >
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </template>
                    Generate Tagihan
                </RedwoodButton>
            </div>
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Santri</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Periode</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jumlah</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jatuh Tempo</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Status</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="bill in billings.data" :key="bill.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-5">
                                    <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ bill.santri.nama }}</p>
                                    <p class="text-[10px] text-[#a8a196] font-bold uppercase">{{ bill.santri.nis }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-medium">{{ bill.bulan }} {{ bill.tahun }}</p>
                                </td>
                                <td class="px-8 py-5 font-bold text-[#161514] dark:text-[#f2e8d5]">
                                    {{ formatCurrency(bill.jumlah) }}
                                </td>
                                <td class="px-8 py-5 text-xs font-medium text-[#a8a196]">
                                    {{ new Date(bill.jatuh_tempo).toLocaleDateString('id-ID') }}
                                </td>
                                <td class="px-8 py-5">
                                    <span 
                                        :class="[
                                            'px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest',
                                            bill.status === 'lunas' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
                                        ]"
                                    >
                                        {{ bill.status === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <Link v-if="bill.status === 'belum_lunas'" :href="route('finance.payments.create', bill.id)" class="px-6 py-2 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all shadow-md shadow-emerald-500/10">
                                        Bayar Now
                                    </Link>
                                    <span v-else class="text-emerald-500 font-black text-[10px] uppercase">Terbayar</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Generate Modal -->
        <div v-if="showGenerateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <div @click="showGenerateModal = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-[#161514] w-full max-w-lg rounded-[40px] p-12 shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] transform transition-all scale-100">
                <h3 class="text-3xl font-black tracking-tight mb-8">Generate <span class="text-[#d02e5c]">Tagihan</span></h3>
                
                <form @submit.prevent="generateBills" class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Bulan</label>
                            <select v-model="genForm.bulan" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]" required>
                                <option value="" disabled>Pilih Bulan</option>
                                <option v-for="m in months" :key="m" :value="m">{{ m }}</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Tahun</label>
                            <input v-model="genForm.tahun" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nominal SPP (Rp)</label>
                        <input v-model="genForm.jumlah" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Filter Entitas</label>
                            <select v-model="genForm.entitas" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]">
                                <option value="">Semua Entitas</option>
                                <option v-for="ent in entitasOptions" :key="ent" :value="ent">{{ ent }}</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Filter Status</label>
                            <select v-model="genForm.status" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]">
                                <option value="">Semua Status Aktif</option>
                                <option v-for="st in statusOptions" :key="st" :value="st">{{ st }}</option>
                            </select>
                            <p class="text-[9px] text-[#a8a196] mt-1 italic">Default: Santri Aktif jika kosong</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jatuh Tempo</label>
                        <input v-model="genForm.jatuh_tempo" type="date" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 outline-none focus:ring-2 focus:ring-[#d02e5c]" required>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6">
                        <button type="button" @click="showGenerateModal = false" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-[#a8a196] hover:text-[#161514] transition-all">Batal</button>
                        <button type="submit" :disabled="genForm.processing" class="px-10 py-4 bg-[#d02e5c] text-white font-black rounded-2xl shadow-xl hover:bg-[#b06a4f] transition-all disabled:opacity-50">
                            {{ genForm.processing ? 'Sabar ya...' : 'Terbitkan Sekarang' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
