<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object
});

const chartData = computed(() => {
    return props.stats?.daily_payments?.length > 0 ? props.stats.daily_payments : [];
});

const maxValue = computed(() => {
    const vals = chartData.value.map(d => d.total);
    return Math.max(...vals, 1);
});

const formatCurrency = (val) => {
    if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
    if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'K';
    return 'Rp ' + val;
};
</script>

<template>
    <Head title="Dashboard Rumah IT Indonesia" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Ringkasan <span class="text-[#d02e5c]">Dashboard</span>
            </h2>
        </template>

        <div class="space-y-8 lg:space-y-12">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] relative group hover:border-[#d02e5c] transition-all duration-500 lg:hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 lg:w-40 h-32 lg:h-40 opacity-[0.03] dark:opacity-10 pointer-events-none group-hover:opacity-20 transition-opacity">
                        <svg viewBox="0 0 100 100" fill="#d02e5c"><circle cx="70" cy="30" r="50" /></svg>
                    </div>
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4">Total Santri</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#161514] dark:text-[#f2e8d5] tracking-tighter">{{ stats?.total_santri ?? 0 }}</div>
                </div>

                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] group hover:border-[#d02e5c] transition-all duration-500 lg:hover:-translate-y-1">
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4">Santri Aktif</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#d02e5c] tracking-tighter">{{ stats?.active_santri ?? 0 }}</div>
                </div>

                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] group hover:border-[#d02e5c] transition-all duration-500 lg:hover:-translate-y-1">
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4">Pendaftaran Baru</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#161514] dark:text-[#f2e8d5] tracking-tighter">{{ stats?.new_registrations ?? 0 }}</div>
                </div>
            </div>

            <!-- Lightweight Bar Chart (no external library) -->
            <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] lg:rounded-[40px] p-6 lg:p-12 border border-[#ebeae8] dark:border-[#3e3c3a]">
                <h3 class="text-xl lg:text-2xl font-bold text-[#161514] dark:text-[#f2e8d5] mb-8 tracking-tight flex items-center gap-4">
                    <span class="w-2.5 h-8 lg:h-10 bg-[#d02e5c] rounded-full shadow-[0_0_15px_rgba(208,46,92,0.4)]"></span>
                    Pembayaran Bulan Ini (Harian)
                </h3>

                <div v-if="chartData.length > 0" class="w-full overflow-x-auto">
                    <div class="flex items-end gap-1 h-48" :style="{ minWidth: chartData.length * 28 + 'px' }">
                        <div
                            v-for="(item, i) in chartData"
                            :key="i"
                            class="relative flex-1 flex flex-col items-center gap-1 group/bar min-w-[20px]"
                        >
                            <!-- Tooltip -->
                            <div
                                v-if="item.total > 0"
                                class="absolute bottom-full mb-2 hidden group-hover/bar:block bg-[#161514] dark:bg-[#f2e8d5] text-white dark:text-[#161514] text-[9px] font-bold px-2 py-1 rounded-lg whitespace-nowrap z-10 -translate-x-1/2 left-1/2"
                            >
                                {{ formatCurrency(item.total) }}
                            </div>
                            <!-- Bar -->
                            <div
                                class="w-full rounded-t-md transition-all duration-500"
                                :class="item.total > 0 ? 'bg-[#d02e5c] hover:bg-[#b0224a]' : 'bg-[#ebeae8] dark:bg-[#262524]'"
                                :style="{ height: item.total > 0 ? Math.max((item.total / maxValue) * 160, 4) + 'px' : '4px' }"
                                :title="item.date + ': ' + formatCurrency(item.total)"
                            ></div>
                            <!-- Date label every 5 bars -->
                            <span v-if="i % 5 === 0" class="text-[8px] text-[#a8a196] font-bold truncate w-full text-center">{{ item.date.split(' ')[0] }}</span>
                            <span v-else class="text-[8px] text-transparent select-none">.</span>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center h-48 text-[#a8a196] space-y-4">
                    <div class="w-16 h-16 bg-[#d02e5c]/5 rounded-3xl flex items-center justify-center border border-[#ebeae8] dark:border-[#3e3c3a]">
                        <svg class="w-8 h-8 text-[#d02e5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-bold tracking-wider">Belum ada aktivitas pembayaran bulan ini.</span>
                    <span class="text-xs">Data akan otomatis muncul setelah ada santri yang membayar.</span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
