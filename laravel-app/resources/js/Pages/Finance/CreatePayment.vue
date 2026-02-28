<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    billing: Object
});

const form = useForm({
    tagihan_id: props.billing.id,
    tanggal_bayar: new Date().toISOString().split('T')[0],
    jumlah_bayar: props.billing.jumlah,
    metode_pembayaran: '',
    bukti_pembayaran: null,
});

const submit = () => {
    form.post(route('finance.payments.store'), {
        forceFormData: true,
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <Head title="Catat Pembayaran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <RedwoodButton 
                    as="Link"
                    :href="route('finance.billings')"
                    variant="ghost"
                >
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </template>
                </RedwoodButton>
                <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                    Catat <span class="text-[#d02e5c]">Pembayaran</span>
                </h2>
            </div>
        </template>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-[#161514] shadow-[0_30px_60px_rgba(0,0,0,0.05)] dark:shadow-[0_30px_60px_rgba(0,0,0,0.3)] rounded-[40px] border border-[#ebeae8] dark:border-[#3e3c3a] p-12">
                <!-- Billing Summary Info -->
                <div class="p-8 bg-[#fcf8f5] dark:bg-[#1a1918] rounded-[24px] mb-10 border border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Informasi Tagihan</p>
                        <span class="px-4 py-1 bg-rose-500/10 text-rose-500 rounded-full text-[9px] font-black uppercase italic tracking-tighter">BELUM LUNAS</span>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-2xl font-black">{{ props.billing.santri.nama }}</h4>
                        <p class="text-[#a8a196] font-medium italic whitespace-nowrap">Periode: {{ props.billing.bulan }} {{ props.billing.tahun }}</p>
                        <p class="text-2xl font-black text-[#d02e5c] pt-2">{{ formatCurrency(props.billing.jumlah) }}</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Tanggal Bayar</label>
                        <input v-model="form.tanggal_bayar" type="date" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jumlah Bayar (Rp)</label>
                        <input v-model="form.jumlah_bayar" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none font-bold text-xl text-[#d02e5c] focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Metode Pembayaran</label>
                        <select v-model="form.metode_pembayaran" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all" required>
                            <option value="" disabled>Pilih Metode</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Tunai / Cash">Tunai / Cash</option>
                            <option value="OVO/Gopay/DANA">E-Wallet (OVO/Gopay)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Bukti Pembayaran (Opsional)</label>
                        <input type="file" @input="form.bukti_pembayaran = $event.target.files[0]" class="block w-full text-sm text-[#a8a196] file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#d02e5c]/10 file:text-[#d02e5c] hover:file:bg-[#d02e5c]/20 transition-all">
                    </div>

                    <div class="pt-6">
                        <RedwoodButton 
                            type="submit" 
                            variant="primary"
                            size="lg"
                            :disabled="form.processing"
                            class="w-full !bg-emerald-500 !hover:bg-emerald-600 !shadow-emerald-500/30"
                        >
                            <template #icon>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </template>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Transaksi' }}
                        </RedwoodButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
