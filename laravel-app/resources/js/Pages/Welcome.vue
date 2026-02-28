<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { computed } from 'vue';
import heroBg from '../../images/homepage-hero.png';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    sections: {
        type: Object,
        default: () => ({})
    },
    featuredSantris: {
        type: Array,
        default: () => []
    }
});

const hero = computed(() => props.sections.hero || {});
const programs = computed(() => props.sections.programs || {});
const tracks = computed(() => props.sections.tracks || {});
</script>

<template>
    <Head>
        <title>Rumah IT Indonesia | Lembaga Pendidikan IT Berbasis Pondok</title>
        <meta name="description" content="Rumah IT Indonesia mengajarkan Agama dan Skill IT sekaligus untuk santri ikhwan dan akhwat. Cetak generasi Qurani ahli teknologi.">
        <meta name="keywords" content="Pondok IT, Belajar Programming, Bisnis Digital, Pendidikan IT Islam, Rumah IT Indonesia">
        <meta property="og:title" content="Rumah IT Indonesia | Pendidikan IT Islami">
        <meta property="og:description" content="Mencetak Generasi Qurani yang Ahli di Bidang Teknologi.">
        <meta property="og:image" content="/images/homepage-hero.png">
    </Head>

    <div class="min-h-screen bg-[#fcf8f5] dark:bg-[#0a0a0a] text-[#161514] dark:text-[#f2e8d5] font-sans selection:bg-brand-rose selection:text-white transition-colors duration-500">
        
        <!-- Header / Navigation -->
        <header class="fixed top-0 w-full z-50 bg-white/80 dark:bg-[#161514]/80 backdrop-blur-xl border-b border-[#ebeae8] dark:border-[#383736] transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center gap-3">
                        <ApplicationLogo class="w-10 h-10" />
                        <span class="text-xl font-black tracking-tighter uppercase hidden sm:block">
                            Rumah IT <span class="text-brand-rose-vibrant">Indonesia</span>
                        </span>
                    </div>
                    
                    <nav class="flex items-center gap-4">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-bold hover:text-brand-rose transition-colors">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold hover:text-brand-rose transition-colors">
                                Log in
                            </Link>
                            <RedwoodButton v-if="canRegister" as="Link" :href="route('psb.register')" size="sm" class="ml-2">
                                Daftar Terbuka
                            </RedwoodButton>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <main class="pt-20">
            <!-- Hero Section -->
            <section v-if="hero.is_active" class="relative overflow-hidden min-h-[90vh] flex items-center">
                <div class="absolute inset-0 z-0">
                    <img :src="heroBg" alt="Hero Background" class="w-full h-full object-cover opacity-90 dark:opacity-40" />
                    <div class="absolute inset-0 bg-gradient-to-r from-[#fcf8f5] via-[#fcf8f5]/80 to-transparent dark:from-[#0a0a0a] dark:via-[#0a0a0a]/80"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-20">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-rose/10 text-brand-rose font-bold text-xs uppercase tracking-widest mb-6 border border-brand-rose/20">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-rose opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-rose"></span>
                            </span>
                            Penerimaan Santri Baru Aktif
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[1.1] mb-6">
                            {{ hero.title || 'Lembaga Pendidikan IT Berbasis Pondok' }}
                        </h1>
                        <p class="text-xl md:text-2xl text-[#4e4b47] dark:text-[#a8a196] mb-4 font-medium leading-relaxed">
                            {{ hero.subtitle || 'Mencetak Generasi Qurani yang Ahli di Bidang Teknologi' }}
                        </p>
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-xl">
                            {{ hero.content?.description }}
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <RedwoodButton as="Link" :href="route('psb.register')" size="lg" class="shadow-xl shadow-brand-rose/30">
                                {{ hero.content?.cta_text || 'Daftar Sekarang' }}
                            </RedwoodButton>
                            <RedwoodButton as="a" href="#programs" variant="outline" size="lg">
                                Lihat Program
                            </RedwoodButton>
                        </div>
                    </div>
                </div>
                
                <!-- Redwood Curve Decoration -->
                <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-10">
                    <svg class="relative block w-full h-[100px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V0C50.63,19.34,103.54,39.67,159,51.81,213.62,63.69,268.03,66.6,321.39,56.44Z" class="fill-[#fcf8f5] dark:fill-[#0a0a0a]"></path>
                    </svg>
                </div>
            </section>

            <!-- Programs Section -->
            <section id="programs" v-if="programs.is_active" class="py-24 relative bg-[#fcf8f5] dark:bg-[#0a0a0a]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-sm font-black text-brand-teal uppercase tracking-widest mb-2">{{ programs.title }}</h2>
                        <h3 class="text-4xl md:text-5xl font-black tracking-tight">{{ programs.subtitle }}</h3>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                        <!-- Ikhwan Card -->
                        <div v-if="programs.content?.ikhwan" class="bg-white dark:bg-[#161514] rounded-[32px] p-8 md:p-12 shadow-2xl shadow-black/5 dark:shadow-black/40 border border-[#ebeae8] dark:border-[#383736] relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-teal/5 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none transition-transform group-hover:scale-110"></div>
                            
                            <div class="w-16 h-16 bg-brand-teal/10 rounded-2xl flex items-center justify-center mb-8 border border-brand-teal/20 text-brand-teal">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            
                            <h4 class="text-2xl font-black mb-6">{{ programs.content.ikhwan.title }}</h4>
                            <ul class="space-y-4 mb-8">
                                <li v-for="(track, index) in programs.content.ikhwan.tracks" :key="index" class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-teal"></div>
                                    <span class="font-bold text-lg text-[#4e4b47] dark:text-[#a8a196]">{{ track }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Akhwat Card -->
                        <div v-if="programs.content?.akhwat" class="bg-white dark:bg-[#161514] rounded-[32px] p-8 md:p-12 shadow-2xl shadow-black/5 dark:shadow-black/40 border border-[#ebeae8] dark:border-[#383736] relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-rose/5 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none transition-transform group-hover:scale-110"></div>
                            
                            <div class="w-16 h-16 bg-brand-rose/10 rounded-2xl flex items-center justify-center mb-8 border border-brand-rose/20 text-brand-rose">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            
                            <h4 class="text-2xl font-black mb-6">{{ programs.content.akhwat.title }}</h4>
                            <ul class="space-y-4 mb-8">
                                <li v-for="(track, index) in programs.content.akhwat.tracks" :key="index" class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-rose"></div>
                                    <span class="font-bold text-lg text-[#4e4b47] dark:text-[#a8a196]">{{ track }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Portfolio Section -->
            <section v-if="featuredSantris && featuredSantris.length > 0" class="py-24 relative bg-white dark:bg-[#161514] border-t border-[#ebeae8] dark:border-[#383736]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-sm font-black text-brand-rose uppercase tracking-widest mb-2">Portofolio & Prestasi</h2>
                        <h3 class="text-4xl md:text-5xl font-black tracking-tight">Karya Nyata Santri Sukses</h3>
                        <p class="mt-4 text-lg text-[#4e4b47] dark:text-[#a8a196]">Mengenal lebih dekat santri-santri pilihan yang telah menunjukkan dedikasi dan karya gemilang di Rumah IT Indonesia.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <Link v-for="santri in featuredSantris" :key="santri.id" :href="route('public.santri.profile', santri.nis)" class="bg-[#fcf8f5] dark:bg-[#0a0a0a] rounded-[32px] overflow-hidden shadow-xl shadow-black/5 dark:shadow-black/20 border border-[#ebeae8] dark:border-[#383736] group hover:-translate-y-2 transition-transform duration-500 block">
                            <!-- Photo -->
                            <div class="aspect-[4/5] overflow-hidden relative bg-gray-200 dark:bg-gray-800">
                                <img v-if="santri.foto" :src="`/storage/${santri.foto}`" :alt="`Foto ${santri.nama}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div v-else class="w-full h-full flex flex-col items-center justify-center text-[#a8a196]">
                                    <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4 text-white">
                                    <h4 class="text-xl font-black mb-1 line-clamp-1 title-shadow">{{ santri.nama }}</h4>
                                    <p v-if="santri.entitas || santri.angkatan" class="text-xs font-bold text-white/80 uppercase tracking-widest">
                                        {{ santri.entitas }} <span v-if="santri.entitas && santri.angkatan">•</span> {{ santri.angkatan ? `Angkatan ${santri.angkatan}` : '' }}
                                    </p>
                                </div>
                            </div>
                            <!-- Content -->
                            <div class="p-6">
                                <div v-if="santri.cita_cita" class="mb-4">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-1">Cita-cita / Fokus</div>
                                    <p class="font-bold text-brand-teal line-clamp-2">{{ santri.cita_cita }}</p>
                                </div>
                                <div class="text-sm text-[#4e4b47] dark:text-[#a8a196] line-clamp-3 leading-relaxed">
                                    {{ santri.bio || 'Mempersiapkan diri menjadi profesional IT yang berakhlak mulia.' }}
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Tracks Details Section -->
            <section v-if="tracks.is_active" class="py-24 relative bg-white dark:bg-[#161514] border-t border-[#ebeae8] dark:border-[#383736]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                        <div class="max-w-2xl">
                            <h2 class="text-sm font-black text-brand-rose uppercase tracking-widest mb-2">{{ tracks.title }}</h2>
                            <h3 class="text-4xl md:text-5xl font-black tracking-tight">{{ tracks.subtitle }}</h3>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div v-for="(trackData, key) in tracks.content" :key="key" class="bg-[#fcf8f5] dark:bg-[#0a0a0a] rounded-[24px] p-8 border border-[#ebeae8] dark:border-[#383736]">
                            <h4 class="text-xl font-black mb-6">{{ trackData.name }}</h4>
                            <div class="flex flex-wrap gap-3">
                                <span v-for="(spec, idx) in trackData.specializations" :key="idx" class="px-4 py-2 bg-white dark:bg-[#161514] rounded-xl text-sm font-bold shadow-sm border border-[#ebeae8] dark:border-[#383736]">
                                    {{ spec }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-[#161514] text-[#a8a196] py-12 border-t border-[#383736]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:text-left">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-3">
                        <ApplicationLogo class="w-8 h-8 opacity-50 grayscale" />
                        <span class="text-xl font-black tracking-tighter uppercase text-white">
                            RUMAH IT <span class="text-brand-rose-vibrant">INDONESIA</span>
                        </span>
                    </div>
                    
                    <div class="text-sm font-medium">
                        &copy; {{ new Date().getFullYear() }} Rumah IT Indonesia. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
