<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    recent_payments: Array
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
    <Head title="Manajemen Keuangan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Manajemen <span class="text-[#c97e60]">Keuangan</span>
            </h2>
        </template>

        <div class="space-y-10">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Total Billed -->
                <div class="bg-white dark:bg-[#161514] p-10 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm relative overflow-hidden group hover:border-[#c97e60] transition-all">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#c97e60]/5 rounded-full blur-2xl group-hover:bg-[#c97e60]/10 transition-all"></div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-4">Total Tagihan</p>
                    <h3 class="text-3xl font-black text-[#161514] dark:text-[#f2e8d5] tracking-tight">{{ formatCurrency(stats.total_billed) }}</h3>
                </div>

                <!-- Total Collected -->
                <div class="bg-white dark:bg-[#161514] p-10 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm relative overflow-hidden group hover:border-emerald-500/30 transition-all">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-4">Total Terkumpul</p>
                    <h3 class="text-3xl font-black text-emerald-500 tracking-tight">{{ formatCurrency(stats.total_paid) }}</h3>
                </div>

                <!-- Unpaid Count -->
                <div class="bg-white dark:bg-[#161514] p-10 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm relative overflow-hidden group hover:border-rose-500/30 transition-all">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-all"></div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] mb-4">Belum Lunas</p>
                    <h3 class="text-3xl font-black text-rose-500 tracking-tight">{{ stats.unpaid_count }} <span class="text-sm font-medium text-[#a8a196]">Siswa</span></h3>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <Link :href="route('finance.billings')" class="flex items-center gap-6 p-8 bg-[#161514] text-white rounded-[40px] shadow-2xl hover:bg-black transition-all group">
                    <div class="w-16 h-16 bg-[#c97e60] rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black">Daftar Tagihan</h4>
                        <p class="text-[#a8a196] text-sm">Kelola semua tagihan santri</p>
                    </div>
                </Link>

                <Link :href="route('finance.payments')" class="flex items-center gap-6 p-8 bg-white dark:bg-[#161514] rounded-[40px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm transition-all group hover:border-[#c97e60]">
                    <div class="w-16 h-16 bg-[#f5f4f2] dark:bg-[#262524] rounded-2xl flex items-center justify-center text-[#c97e60] group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black dark:text-[#f2e8d5]">Riwayat Pembayaran</h4>
                        <p class="text-[#a8a196] text-sm font-medium">Lihat seluruh transaksi pembayaran</p>
                    </div>
                </Link>

                <Link :href="route('finance.settings')" class="flex items-center gap-6 p-8 bg-white dark:bg-[#161514] rounded-[40px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm transition-all group hover:border-[#c97e60]">
                    <div class="w-16 h-16 bg-[#f5f4f2] dark:bg-[#262524] rounded-2xl flex items-center justify-center text-[#c97e60] group-hover:rotate-90 transition-transform duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black dark:text-[#f2e8d5]">Jenis Pembayaran</h4>
                        <p class="text-[#a8a196] text-sm font-medium">Kelola jenis pembayaran dinamis</p>
                    </div>
                </Link>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="px-10 py-8 border-b border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between">
                    <h3 class="text-xl font-bold tracking-tight">Transaksi Terakhir</h3>
                    <RedwoodButton 
                        as="Link"
                        :href="route('finance.payments')"
                        variant="ghost"
                    >
                        Lihat Semua
                    </RedwoodButton>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50">
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Tanggal</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Santri</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Metode</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="payment in recent_payments" :key="payment.id" class="hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-4 text-xs font-medium">{{ new Date(payment.tanggal_bayar).toLocaleDateString('id-ID') }}</td>
                                <td class="px-8 py-4">
                                    <p class="font-bold text-sm">{{ payment.tagihan.santri.nama }}</p>
                                    <p class="text-[10px] text-[#a8a196] uppercase font-bold">{{ payment.tagihan.bulan }} {{ payment.tagihan.tahun }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-[#262524] rounded-full text-[10px] font-black uppercase tracking-tighter">{{ payment.metode_pembayaran }}</span>
                                </td>
                                <td class="px-8 py-4 text-right font-bold text-emerald-500">{{ formatCurrency(payment.jumlah_bayar) }}</td>
                            </tr>
                            <tr v-if="recent_payments.length === 0">
                                <td colspan="4" class="px-8 py-10 text-center text-[#a8a196] font-medium italic">Belum ada transaksi pembayaran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
