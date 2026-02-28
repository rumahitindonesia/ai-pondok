<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    stats: Object
});

const chartData = computed(() => {
    const data = props.stats.daily_payments && props.stats.daily_payments.length > 0 
        ? props.stats.daily_payments 
        : (props.stats.monthly_revenue || []);
    
    return data.map(item => ({
        x: item.date || item.month,
        y: parseFloat(item.total !== undefined ? item.total : item.value)
    }));
});

const chartOptions = computed(() => ({
    chart: {
        type: 'area',
        fontFamily: 'inherit',
        toolbar: { show: false },
        zoom: { enabled: false },
        background: 'transparent'
    },
    colors: ['#d02e5c'],
    dataLabels: { enabled: false },
    stroke: {
        curve: 'smooth',
        width: 3
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.05,
            stops: [0, 100]
        }
    },
    xaxis: {
        type: 'category',
        labels: {
            style: { colors: '#a8a196' }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: {
        labels: {
            style: { colors: '#a8a196' },
            formatter: (value) => {
                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                return 'Rp ' + value;
            }
        }
    },
    grid: {
        borderColor: '#3e3c3a',
        strokeDashArray: 4,
        xaxis: { lines: { show: false } },
        yaxis: { lines: { show: true } }
    },
    theme: { mode: 'dark' },
    tooltip: {
        theme: 'dark',
        y: {
            formatter: function (val) {
                return "Rp " + val.toLocaleString('id-ID')
            }
        }
    }
}));

const series = computed(() => [{
    name: 'Total Pembayaran',
    data: chartData.value
}]);
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
                     <!-- Redwood Organic Pattern -->
                    <div class="absolute top-0 right-0 w-32 lg:w-40 h-32 lg:h-40 opacity-[0.03] dark:opacity-10 pointer-events-none transition-opacity group-hover:opacity-20">
                        <svg viewBox="0 0 100 100" fill="#d02e5c"><circle cx="70" cy="30" r="50" /></svg>
                    </div>
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4 transition-colors">Total Santri</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#161514] dark:text-[#f2e8d5] tracking-tighter transition-colors">{{ stats.total_santri }}</div>
                </div>
                
                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] group hover:border-[#d02e5c] transition-all duration-500 lg:hover:-translate-y-1">
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4 transition-colors">Santri Aktif</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#d02e5c] tracking-tighter transition-colors">{{ stats.active_santri }}</div>
                </div>
                
                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] group hover:border-[#d02e5c] transition-all duration-500 lg:hover:-translate-y-1">
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4 transition-colors">Pendaftaran Baru</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#161514] dark:text-[#f2e8d5] tracking-tighter transition-colors">{{ stats.new_registrations }}</div>
                </div>
            </div>

            <!-- Chart Section (ApexCharts) -->
            <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] lg:rounded-[40px] p-6 lg:p-12 border border-[#ebeae8] dark:border-[#3e3c3a] transition-all duration-500">
                <h3 class="text-xl lg:text-2xl font-bold text-[#161514] dark:text-[#f2e8d5] mb-8 lg:mb-10 tracking-tight flex items-center gap-4 transition-colors">
                    <span class="w-2.5 h-8 lg:h-10 bg-[#d02e5c] rounded-full shadow-[0_0_15px_rgba(201,126,96,0.5)]"></span>
                    Pembayaran Bulan Ini (Harian)
                </h3>
                
                <div class="w-full h-[300px] lg:h-[450px]">
                    <div v-if="chartData.length > 0" class="w-full h-full relative" style="overflow: hidden;">
                        <VueApexCharts 
                            width="100%" 
                            height="100%" 
                            type="area" 
                            :options="chartOptions" 
                            :series="series" 
                        />
                    </div>
                    <div v-else class="flex flex-col items-center justify-center h-full text-[#a8a196] space-y-4">
                        <div class="w-16 h-16 bg-[#d02e5c]/5 rounded-3xl flex items-center justify-center border border-[#ebeae8] dark:border-[#3e3c3a]">
                            <svg class="w-8 h-8 text-[#d02e5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="text-sm font-bold tracking-wider">Belum ada aktivitas pembayaran hari ini.</span>
                        <span class="text-xs">Data akan otomatis muncul setelah ada santri yang membayar.</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
