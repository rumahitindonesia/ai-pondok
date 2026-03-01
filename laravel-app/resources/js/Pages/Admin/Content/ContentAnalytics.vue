<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import moment from 'moment';

const props = defineProps({
    overallStats: Object,
    formatStats: Array,
    topContent: Array,
    monthlyTrend: Array,
    aiInsight: String,
});

const markdownToHtml = (text) => {
    if (!text) return '';
    // Basic markdown to HTML conversion
    let html = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
        .replace(/\*(.*?)\*/g, '<em>$1</em>') // Italic
        .replace(/^\s*[-*+]\s+(.*)$/gm, '<li class="ml-4 list-disc">$1</li>') // Unordered list
        .replace(/\n\n/g, '<br><br>') // Paragraphs
        .replace(/\n/g, '<br>'); // Line breaks
    return html;
};

const getFormatIcon = (format) => {
    if (format.includes('Video') || format.includes('Reels')) return 'reels';
    if (format.includes('Carousel')) return 'carousel';
    return 'post';
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num || 0);
};

// Calculate insights
const topFormat = computed(() => {
    if (!props.formatStats.length) return null;
    return props.formatStats.reduce((prev, current) => (prev.avg_reach > current.avg_reach) ? prev : current);
});

const engagementRate = computed(() => {
    if (!props.overallStats.total_reach) return 0;
    return (props.overallStats.total_engagement / props.overallStats.total_reach * 100).toFixed(2);
});
</script>

<template>
    <Head title="Content Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-2xl text-[#161514] dark:text-[#f2e8d5] leading-tight uppercase tracking-tighter">
                        Content <span class="text-[#d02e5c]">Analytics</span>
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium italic">
                        Ambil keputusan berbasis data untuk strategi konten Media Rumah IT.
                    </p>
                </div>
                <Link :href="route('admin.content.manager.index')" class="bg-white dark:bg-[#1e1d1b] border border-[#ebeae8] dark:border-[#3e3c3a] text-[#161514] dark:text-[#f2e8d5] px-6 py-2 rounded-full font-bold text-sm shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Manager
                </Link>
            </div>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
            <!-- Overall Scorecards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-[#1e1d1b] p-6 rounded-3xl shadow-sm border border-[#ebeae8] dark:border-[#3e3c3a] relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Total Reach</div>
                        <div class="text-3xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ formatNumber(overallStats.total_reach) }}</div>
                        <div class="mt-2 text-[10px] font-bold text-rose-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Cumulative Awareness
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e1d1b] p-6 rounded-3xl shadow-sm border border-[#ebeae8] dark:border-[#3e3c3a] relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 dark:bg-amber-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Interactions</div>
                        <div class="text-3xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ formatNumber(overallStats.total_engagement) }}</div>
                        <div class="mt-2 text-[10px] font-bold text-amber-500 flex items-center gap-1">
                            Avg Rate: {{ engagementRate }}%
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e1d1b] p-6 rounded-3xl shadow-sm border border-[#ebeae8] dark:border-[#3e3c3a] relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-sky-50 dark:bg-sky-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Saved Posts</div>
                        <div class="text-3xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ formatNumber(overallStats.total_saved) }}</div>
                        <div class="mt-2 text-[10px] font-bold text-sky-500 flex items-center gap-1 italic">
                            High-intent audience
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e1d1b] p-6 rounded-3xl shadow-sm border border-[#ebeae8] dark:border-[#3e3c3a] relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Analyzed Posts</div>
                        <div class="text-3xl font-black text-[#161514] dark:text-[#f2e8d5]">{{ overallStats.total_posts }}</div>
                        <div class="mt-2 text-[10px] font-bold text-emerald-500 flex items-center gap-1">
                             Dataset: 30 days
                        </div>
                    </div>
                </div>
            </div>

            <!-- Format Comparison -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Comparison Table -->
                <div class="bg-white dark:bg-[#1e1d1b] rounded-3xl shadow-sm border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                    <div class="p-6 border-b border-[#ebeae8] dark:border-[#3e3c3a]">
                        <h3 class="font-black text-lg text-[#161514] dark:text-[#f2e8d5] uppercase tracking-tight">Format Performance Battle</h3>
                        <p class="text-xs text-gray-400 font-medium">Rata-rata performa berdasarkan jenis output konten.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-[#1a1918]">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Format</th>
                                    <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Posts</th>
                                    <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Avg Reach</th>
                                    <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Avg Engage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                                <tr v-for="stat in formatStats" :key="stat.output_format" class="hover:bg-gray-50 dark:hover:bg-[#1a1918] transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div :class="[
                                                'w-8 h-8 rounded-xl flex items-center justify-center',
                                                stat.output_format.includes('Video') ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-600' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600'
                                            ]">
                                                <svg v-if="stat.output_format.includes('Video')" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15.5v-7l6 3.5-6 3.5z"/><path d="M21 19V5H3v14h18zm0-16c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h18z"/></svg>
                                                <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4 2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z"/></svg>
                                            </div>
                                            <span class="text-sm font-black text-[#161514] dark:text-[#f2e8d5]">{{ stat.output_format }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-center text-sm font-bold text-gray-500">{{ stat.count }}</td>
                                    <td class="px-4 py-5 text-center text-md font-black text-[#d02e5c]">{{ formatNumber(stat.avg_reach) }}</td>
                                    <td class="px-4 py-5 text-center text-md font-black text-amber-600">{{ formatNumber(stat.avg_engagement) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- AI Strategic Advice Box -->
                <div class="bg-gradient-to-br from-[#161514] to-[#3e3c3a] dark:from-[#2a2826] dark:to-[#1a1918] rounded-3xl p-8 text-[#f2e8d5] shadow-xl flex flex-col justify-between relative overflow-hidden border border-[#ebeae8]/10">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#d02e5c]/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <span class="bg-[#d02e5c] text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M22 11c0-5.523-4.477-10-10-10S2 5.477 2 11c0 4.14 2.52 7.68 6.1 9.22l.9-2.02C6.18 17.15 4 14.33 4 11s2.68-6.55 6-6.91V21h4V4.09C17.32 4.45 20 7.67 20 11c0 3.33-2.18 6.15-5 7.2l.9 2.02c3.58-1.54 6.1-5.08 6.1-9.22z"/></svg>
                                    Gemini AI
                                </span>
                            </div>
                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest italic tracking-tighter">Strategic Insight Engine</span>
                        </div>
                        
                        <h3 class="text-2xl font-black mb-6 leading-tight text-white">Rekomendasi Strategis</h3>
                        
                        <div class="prose prose-sm prose-invert max-w-none">
                            <div v-html="markdownToHtml(aiInsight)" class="text-[#f2e8d5]/90 space-y-4 text-sm leading-relaxed"></div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5 relative z-10 flex items-center justify-between">
                        <p class="text-[9px] text-gray-500 italic font-medium">Data-driven analysis from latest performance</p>
                        <div class="flex items-center gap-1 opacity-50">
                            <div class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter">Live Analysis</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Content List -->
            <div class="bg-white dark:bg-[#1e1d1b] rounded-3xl shadow-sm border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="p-6 border-b border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-lg text-[#161514] dark:text-[#f2e8d5] uppercase tracking-tight">🏆 Hall of Fame</h3>
                        <p class="text-xs text-gray-400 font-medium">5 Konten dengan Performa Terbaik</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-0 divide-x divide-[#ebeae8] dark:divide-[#3e3c3a] border-b border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div v-for="(content, index) in topContent" :key="content.id" class="p-6 hover:bg-gray-50 dark:hover:bg-[#1a1918] transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-4xl font-black opacity-10 group-hover:opacity-30 transition-opacity">#{{ index + 1 }}</span>
                            <span :class="[
                                'text-[9px] font-black px-2 py-0.5 rounded-full uppercase',
                                content.output_format.includes('Video') ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800'
                            ]">{{ content.output_format }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5] line-clamp-2 mb-4 h-10">{{ content.content_description }}</h4>
                        <div class="flex items-center justify-between text-[10px] font-bold text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-[#d02e5c]" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                {{ formatNumber(content.reach_count) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                {{ formatNumber(content.engagement_count) }}
                            </span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-[#ebeae8] dark:border-[#3e3c3a]">
                            <a :href="content.published_url" target="_blank" class="text-[10px] font-black text-[#d02e5c] uppercase flex items-center justify-between hover:underline">
                                View Post
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
