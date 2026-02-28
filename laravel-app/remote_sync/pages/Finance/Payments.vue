<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    payments: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <Head title="Riwayat Pembayaran" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Riwayat <span class="text-[#c97e60]">Pembayaran</span>
            </h2>
        </template>

        <div class="space-y-8">
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Waktu</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Santri</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Periode SPP</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Metode</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-right">Jumlah</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-center">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="payment in payments.data" :key="payment.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-5">
                                    <p class="text-xs font-medium">{{ new Date(payment.tanggal_bayar).toLocaleDateString('id-ID') }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ payment.tagihan.santri.nama }}</p>
                                    <p class="text-[10px] text-[#a8a196] font-bold uppercase">{{ payment.tagihan.santri.nis }}</p>
                                </td>
                                <td class="px-8 py-5 text-sm font-medium">
                                    {{ payment.tagihan.bulan }} {{ payment.tagihan.tahun }}
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-4 py-1 bg-[#161514] text-white dark:bg-[#262524] rounded-full text-[9px] font-black uppercase tracking-widest">
                                        {{ payment.metode_pembayaran }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right font-black text-emerald-500">
                                    {{ formatCurrency(payment.jumlah_bayar) }}
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <a v-if="payment.bukti_pembayaran" :href="'/storage/' + payment.bukti_pembayaran" target="_blank" class="w-10 h-10 rounded-xl bg-[#c97e60]/10 flex items-center justify-center text-[#c97e60] mx-auto hover:bg-[#c97e60]/20 transition-all border border-transparent hover:border-[#c97e60]/20">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
