<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

// Oracle JET script loader (simplified for prototype)
const props = defineProps({
    stats: Object
});

const chartSeries = ref([]);
const chartGroups = ref([]);
const isJetLoaded = ref(false);
const jetError = ref(null);

onMounted(() => {
    // Load JET from CDN
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/@oracle/oraclejet@15.1.0/dist/css/redwood/oj-redwood-min.css';
    link.onerror = () => jetError.value = "Gagal memuat CSS JET v15.";
    document.head.appendChild(link);

    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js';
    script.onerror = () => jetError.value = "Gagal memuat RequireJS.";
    script.onload = () => {
        try {
            // Configure RequireJS for JET 15.1.0 via JsDelivr
            window.requirejs.config({
                baseUrl: 'https://cdn.jsdelivr.net/npm/@oracle/oraclejet@15.1.0/dist/js',
                paths: {
                    'knockout': 'https://cdn.jsdelivr.net/npm/knockout@3.5.1/build/output/knockout-latest',
                    'jquery': 'https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min',
                    'jqueryui-amd': 'https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/ui',
                    'ojs': 'libs/oj/min',
                    'ojL10n': 'libs/oj/ojL10n',
                    'ojtranslations': 'libs/oj/resources',
                    'signals': 'https://cdn.jsdelivr.net/npm/js-signals@1.0.0/dist/signals.min',
                    'text': 'https://cdn.jsdelivr.net/npm/requirejs-text@2.0.15/text',
                    'hammerjs': 'https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min',
                    'preact': 'https://cdn.jsdelivr.net/npm/preact@10.19.3/dist/preact.umd',
                    'preact/hooks': 'https://cdn.jsdelivr.net/npm/preact@10.19.3/hooks/dist/hooks.umd',
                    'preact/compat': 'https://cdn.jsdelivr.net/npm/preact@10.19.3/compat/dist/compat.umd',
                    'preact/jsx-runtime': 'https://cdn.jsdelivr.net/npm/preact@10.19.3/jsx-runtime/dist/jsxRuntime.umd',
                    '@oracle/oraclejet-preact': 'https://cdn.jsdelivr.net/npm/@oracle/oraclejet-preact@15.1.0/amd',
                    'ojdnd': 'libs/dnd-polyfill/dnd-polyfill-1.0.2'
                }
            });

            window.require(['knockout', 'ojs/ojarraydataprovider', 'ojs/ojchart', 'ojs/ojknockout'], (ko, ArrayDataProvider) => {
                try {
                    const data = props.stats.daily_payments && props.stats.daily_payments.length > 0 
                        ? props.stats.daily_payments 
                        : (props.stats.monthly_revenue || []);

                    if (data.length > 0) {
                        const formattedData = data.map(item => ({
                            id: item.date || item.month,
                            series: item.date ? 'Total Pembayaran' : 'Pendapatan',
                            group: item.date || item.month,
                            value: parseFloat(item.total !== undefined ? item.total : item.value)
                        }));

                        // Toggle purely for v-show visibility
                        chartSeries.value = formattedData;
                        
                        // Wait for Vue to render the empty container
                        setTimeout(() => {
                            const container = document.getElementById('jetChartContainer');
                            if (container) {
                                // Isolate Oracle JET from Vue reactivity
                                container.innerHTML = `
                                    <oj-chart 
                                        id="revenueChart"
                                        type="area"
                                        data="[[dataProvider]]"
                                        x-axis="[[axisOptions]]"
                                        y-axis="[[axisOptions]]"
                                        legend="[[legendOptions]]"
                                        style-defaults="[[styleDefaults]]"
                                        animation-on-display="auto"
                                        style="width:100%; height:100%;"
                                        class="oj-chart-redwood"
                                    >
                                        <template slot="itemTemplate" data-oj-as="item">
                                            <oj-chart-item 
                                                value="[[item.data.value]]" 
                                                group-id="[[ [item.data.group] ]]" 
                                                series-id="[[item.data.series]]">
                                            </oj-chart-item>
                                        </template>
                                    </oj-chart>
                                `;

                                // Initialize via Knockout
                                function ChartViewModel() {
                                    this.dataProvider = new ArrayDataProvider(formattedData, {keyAttributes: 'id'});
                                    
                                    // Custom style options to make text visible against dark backgrounds
                                    const textStyle = { style: { color: '#a8a196', fill: '#a8a196' } };
                                    this.axisOptions = { tickLabel: textStyle, titleStyle: { color: '#a8a196', fill: '#a8a196' } };
                                    this.legendOptions = { textStyle: { color: '#a8a196', fill: '#a8a196' } };
                                    this.styleDefaults = { dataLabelStyle: { color: '#a8a196', fill: '#a8a196' } };
                                }
                                ko.cleanNode(container);
                                ko.applyBindings(new ChartViewModel(), container);
                            }
                        }, 50);
                    }
                    isJetLoaded.value = true;
                } catch (err) {
                    jetError.value = "Gagal memetakan data grafik.";
                    console.error("Mapping Error:", err);
                }
            }, (err) => {
                jetError.value = "Gagal memuat komponen: " + (err.requireModules || "Unknown module");
                console.error("RequireJS Error:", err);
            });
        } catch (err) {
            jetError.value = "Gagal konfigurasi RequireJS.";
            console.error(err);
        }
    };
    document.head.appendChild(script);
});
</script>

<template>
    <Head title="Dashboard Rumah IT Indonesia" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Ringkasan <span class="text-[#c97e60]">Dashboard</span>
            </h2>
        </template>

        <div class="space-y-8 lg:space-y-12">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] relative group hover:border-[#c97e60] transition-all duration-500 lg:hover:-translate-y-1">
                     <!-- Redwood Organic Pattern -->
                    <div class="absolute top-0 right-0 w-32 lg:w-40 h-32 lg:h-40 opacity-[0.03] dark:opacity-10 pointer-events-none transition-opacity group-hover:opacity-20">
                        <svg viewBox="0 0 100 100" fill="#c97e60"><circle cx="70" cy="30" r="50" /></svg>
                    </div>
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4 transition-colors">Total Santri</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#161514] dark:text-[#f2e8d5] tracking-tighter transition-colors">{{ stats.total_santri }}</div>
                </div>
                
                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] group hover:border-[#c97e60] transition-all duration-500 lg:hover:-translate-y-1">
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4 transition-colors">Santri Aktif</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#c97e60] tracking-tighter transition-colors">{{ stats.active_santri }}</div>
                </div>
                
                <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] group hover:border-[#c97e60] transition-all duration-500 lg:hover:-translate-y-1">
                    <div class="text-[10px] font-black uppercase tracking-[0.4em] text-[#a8a196] mb-3 lg:mb-4 transition-colors">Pendaftaran Baru</div>
                    <div class="text-4xl lg:text-6xl font-bold text-[#161514] dark:text-[#f2e8d5] tracking-tighter transition-colors">{{ stats.new_registrations }}</div>
                </div>
            </div>

            <!-- Chart Section (Oracle JET) -->
            <div class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] lg:rounded-[40px] p-6 lg:p-12 border border-[#ebeae8] dark:border-[#3e3c3a] transition-all duration-500">
                <h3 class="text-xl lg:text-2xl font-bold text-[#161514] dark:text-[#f2e8d5] mb-8 lg:mb-10 tracking-tight flex items-center gap-4 transition-colors">
                    <span class="w-2.5 h-8 lg:h-10 bg-[#c97e60] rounded-full shadow-[0_0_15px_rgba(201,126,96,0.5)]"></span>
                    Pembayaran Bulan Ini (Harian)
                </h3>
                
                <div class="w-full h-[300px] lg:h-[450px]">
                    <!-- Oracle JET Web Component Container -->
                    <template v-if="isJetLoaded">
                        <div id="jetChartContainer" class="w-full h-full" v-show="chartSeries.length > 0"></div>
                        <div v-show="chartSeries.length === 0" class="flex flex-col items-center justify-center h-full text-[#a8a196] space-y-4">
                            <div class="w-16 h-16 bg-[#c97e60]/5 rounded-3xl flex items-center justify-center border border-[#ebeae8] dark:border-[#3e3c3a]">
                                <svg class="w-8 h-8 text-[#c97e60]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span class="text-sm font-bold tracking-wider">Belum ada aktivitas pembayaran hari ini.</span>
                            <span class="text-xs">Data akan otomatis muncul setelah ada santri yang membayar.</span>
                        </div>
                    </template>
                    <div v-else-if="jetError" class="flex flex-col items-center justify-center h-full text-rose-500 space-y-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span class="text-sm font-black">{{ jetError }}</span>
                        <RedwoodButton variant="outline" size="sm" @click="() => window.location.reload()">Refresh Halaman</RedwoodButton>
                    </div>
                    <div v-else class="flex items-center justify-center h-full text-[#a8a196]">
                        <div class="flex flex-col items-center gap-6">
                            <div class="w-12 lg:w-16 h-12 lg:h-16 border-4 border-[#ebeae8] dark:border-[#3e3c3a] border-t-[#c97e60] rounded-full animate-spin"></div>
                            <span class="text-sm lg:text-base font-semibold tracking-wider">Memuat Oracle JET Engine...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Oracle JET Custom Styling to match our dark theme */
.oj-chart-redwood {
    --oj-chart-bg-color: transparent;
    --oj-chart-series-color-1: #c97e60;
}
</style>
