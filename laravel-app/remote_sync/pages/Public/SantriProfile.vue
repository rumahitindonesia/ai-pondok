<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

const props = defineProps({
    santri: Object,
    stats: Object
});

// Helper for formatting dates
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return format(new Date(dateString), 'dd MMMM yyyy', { locale: id });
};

// Calculate age
const age = computed(() => {
    if (!props.santri.tanggal_lahir) return '-';
    const birthDate = new Date(props.santri.tanggal_lahir);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
});

// Group attendance by type
const attendanceByType = computed(() => {
    const groups = {};
    props.santri.absensis.forEach(a => {
        const typeName = a.jenis_absensi?.nama_kegiatan || 'Umum';
        if (!groups[typeName]) {
            groups[typeName] = { total: 0, hadir: 0 };
        }
        groups[typeName].total++;
        if (a.status === 'hadir') {
            groups[typeName].hadir++;
        }
    });
    
    // Map to array and calculate percentage
    return Object.keys(groups).map(name => ({
        name,
        percentage: Math.round((groups[name].hadir / groups[name].total) * 100)
    }));
});

// Latest payments
const latestPayments = computed(() => {
    let payments = [];
    props.santri.tagihan_spps.forEach(bill => {
        bill.pembayarans.forEach(p => {
            payments.push({
                ...p,
                bill_month: bill.bulan,
                bill_year: bill.tahun
            });
        });
    });
    
    // Sort descending by payment date
    payments.sort((a, b) => new Date(b.tanggal_bayar) - new Date(a.tanggal_bayar));
    return payments.slice(0, 5); // Take top 5
});
</script>

<template>
    <Head>
        <title>{{ `Profil - ${santri.nama}` }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <!-- Main Foldout Container: Full Screen, Horizontal Scroll with Snapping -->
    <div class="min-h-screen bg-[#f2e8d5] font-['Inter',sans-serif] overflow-x-auto text-[#161514] flex flex-row snap-x snap-mandatory scroll-smooth">
        
        <!-- Left Fixed Profile Panel (Hearth/Dark Theme) -->
        <div class="w-[calc(100vw-3rem)] md:w-80 md:min-w-[320px] bg-[#2d2a26] text-[#f2e8d5] p-8 md:p-10 flex flex-col relative shrink-0 font-['Plus_Jakarta_Sans',sans-serif] snap-center md:snap-align-none">
            <!-- Decorative Background Element -->
            <div class="absolute top-0 right-0 w-48 h-48 bg-[#c97e60]/20 rounded-bl-full pointer-events-none"></div>
            
            <div class="relative z-10">
                <!-- Avatar -->
                <div class="w-32 h-32 md:w-36 md:h-36 rounded-3xl bg-[#3e3c3a] overflow-hidden mb-8 border-4 border-[#161514] shadow-xl mx-auto md:mx-0">
                    <img v-if="santri.foto" :src="`/storage/${santri.foto}`" :alt="santri.nama" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-4xl font-bold text-[#a8a196]">
                        {{ santri.nama.charAt(0) }}
                    </div>
                </div>
                
                <!-- Name & Title -->
                <h1 class="text-2xl md:text-2xl font-extrabold mb-1 tracking-tight text-center md:text-left font-['Plus_Jakarta_Sans',sans-serif]">{{ santri.nama }}</h1>
                <p class="text-[#a8a196] font-semibold mb-8 text-center md:text-left text-[10px] uppercase tracking-[0.15em] opacity-80">
                    {{ santri.status }} • {{ santri.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    <span v-if="santri.angkatan"> • Akt. {{ santri.angkatan }}</span>
                </p>
                
                <!-- Contact Info Blocks -->
                <div class="space-y-6 text-sm">
                    <div>
                        <p class="text-[#a8a196] mb-1.5 font-bold text-[9px] tracking-[0.2em] uppercase opacity-70">Nomor Induk</p>
                        <p class="font-bold font-mono text-base text-[#c97e60] tracking-tight">{{ santri.nis }}</p>
                    </div>
                    
                    <div>
                        <p class="text-[#a8a196] mb-1.5 font-bold text-[9px] tracking-[0.2em] uppercase opacity-70">Wali Santri</p>
                        <p class="font-bold text-sm">{{ santri.wali?.nama_ayah || santri.wali?.nama_ibu || '-' }}</p>
                        <p class="text-[#a8a196] mt-0.5 text-xs font-medium">{{ santri.wali?.no_hp || '-' }}</p>
                    </div>

                    <div v-if="santri.email || santri.no_hp">
                        <p class="text-[#a8a196] mb-1.5 font-bold text-[9px] tracking-[0.2em] uppercase opacity-70">Kontak Pribadi</p>
                        <p class="font-semibold text-xs break-all tracking-tight">{{ santri.email || '-' }}</p>
                        <p class="text-[#a8a196] mt-0.5 text-xs font-medium">{{ santri.no_hp || '-' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-[#a8a196] mb-1.5 font-bold text-[9px] tracking-[0.2em] uppercase opacity-70">Alamat</p>
                        <p class="font-medium leading-relaxed opacity-90 text-xs tracking-tight">{{ santri.alamat }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-auto pt-8 flex gap-3 text-[#a8a196] justify-center md:justify-start">
                <a :href="`https://wa.me/${santri.wali?.no_hp?.replace(/[^0-9]/g, '')}`" target="_blank" class="w-9 h-9 rounded-full bg-[#3e3c3a] hover:bg-[#c97e60] hover:text-white transition-all flex items-center justify-center border border-white/5 shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                </a>
            </div>
        </div>

        <!-- Right Horizontal Foldout Panels -->
        <div class="flex-1 flex flex-row p-6 md:p-10 gap-8 md:gap-12 min-h-full">
            
            <!-- Column 1: Organization & Placement -->
            <div class="w-[calc(100vw-3rem)] md:w-72 shrink-0 flex flex-col gap-8 snap-center md:snap-align-none">
                <div class="header-group">
                    <h2 class="text-2xl font-bold text-[#161514] tracking-tight mb-2 font-['Plus_Jakarta_Sans',sans-serif]">Penempatan</h2>
                    <div class="w-10 h-1.5 bg-[#c97e60] rounded-sm"></div>
                </div>
                
                <div class="space-y-4">
                    <p class="text-[9px] font-extrabold text-[#c97e60] tracking-[0.25em] uppercase font-['Plus_Jakarta_Sans',sans-serif]">Organization > Structure</p>
                    
                    <div class="bg-white rounded-[20px] p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-[#e1d8c9] hover:shadow-lg transition-all duration-500 group">
                        <div class="flex items-center gap-4 border-b border-[#f2e8d5] pb-5 mb-5">
                            <div class="w-12 h-12 bg-[#c97e60]/10 text-[#c97e60] rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform border border-[#c97e60]/5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-[#a8a196] uppercase tracking-[0.2em] mb-0.5 opacity-70">Asrama / Kamar</p>
                                <p class="text-lg font-bold text-[#161514] font-['Plus_Jakarta_Sans',sans-serif] tracking-tight">{{ santri.kamar?.nama_kamar || 'Belum Ditempatkan' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-[#2d2a26]/5 text-[#2d2a26] rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-[#a8a196] uppercase tracking-[0.2em] mb-0.5 opacity-70">Kelas Akademik</p>
                                <p class="text-lg font-bold text-[#161514] font-['Plus_Jakarta_Sans',sans-serif] tracking-tight">{{ santri.kelas?.nama_kelas || '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 2: About Me & Biodata -->
            <div class="w-[calc(100vw-3rem)] md:w-[400px] shrink-0 flex flex-col gap-8 snap-center md:snap-align-none">
                <div class="header-group">
                    <h2 class="text-2xl font-bold text-[#161514] tracking-tight mb-2 font-['Plus_Jakarta_Sans',sans-serif]">About me</h2>
                    <div class="w-10 h-1.5 bg-[#c97e60] rounded-sm"></div>
                </div>
                
                <div class="space-y-6 text-[#5a5753]">
                    <div class="text-[15px] leading-relaxed font-normal">
                        <p v-if="santri.bio" class="tracking-tight">
                            {{ santri.bio }}
                        </p>
                        <p v-else class="tracking-tight">
                            Santri yang saat ini menempuh pendidikan di <span class="text-[#c97e60] font-semibold">Pondok Pesantren BSD</span> dengan Nomor Induk <span class="text-[#161514] underline decoration-[#c97e60] decoration-2">{{ santri.nis }}</span>.
                            Lahir di {{ santri.tempat_lahir || '-' }} pada {{ formatDate(santri.tanggal_lahir) }} ({{ age }} tahun).
                        </p>
                    </div>

                    <div v-if="santri.cita_cita" class="bg-[#c97e60]/5 p-5 rounded-2xl border-l-[6px] border-[#c97e60] shadow-sm">
                        <p class="text-[9px] font-extrabold text-[#c97e60] uppercase tracking-[0.25em] mb-1.5 font-['Plus_Jakarta_Sans',sans-serif]">Cita-cita / Impian</p>
                        <p class="text-[15px] font-bold text-[#161514] font-['Plus_Jakarta_Sans',sans-serif] italic">"{{ santri.cita_cita }}"</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-bold text-[#a8a196] uppercase tracking-[0.2em] mb-4 opacity-70">Categories / Tags</p>
                        <div class="flex flex-wrap gap-2.5">
                            <span v-if="santri.entitas" class="px-5 py-2 bg-[#c97e60] text-white text-[10px] font-black uppercase tracking-[0.1em] rounded-full shadow-lg">
                                {{ santri.entitas }}
                            </span>
                            <span v-for="tag in (santri.minat_bakat ? santri.minat_bakat.split(',') : [santri.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'])" :key="tag" 
                                  class="px-5 py-2 bg-white border border-[#e1d8c9] text-[#161514] text-[10px] font-bold uppercase tracking-[0.1em] rounded-full shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:border-[#c97e60] hover:text-[#c97e60] transition-all whitespace-nowrap">
                                {{ tag.trim() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 3: Skills & Development -->
            <div class="w-[calc(100vw-3rem)] md:w-[400px] shrink-0 flex flex-col gap-8 snap-center md:snap-align-none">
                <div class="header-group">
                    <h2 class="text-2xl font-bold text-[#161514] tracking-tight mb-2 font-['Plus_Jakarta_Sans',sans-serif]">Achievement</h2>
                    <div class="w-10 h-1.5 bg-[#c97e60] rounded-sm"></div>
                </div>
                
                <div class="space-y-8">
                    <div class="navigation-group flex gap-5 text-[9px] font-bold font-['Plus_Jakarta_Sans',sans-serif]">
                        <p class="text-[#c97e60] tracking-[0.2em] uppercase">Overview</p>
                        <p class="text-[#a8a196] tracking-[0.2em] uppercase opacity-70">Curriculum</p>
                        <p class="text-[#161514] tracking-[0.2em] uppercase border-b-[3px] border-[#161514] pb-0.5">Learning Stack</p>
                    </div>

                    <!-- Global Displine Score Card (Optional, kept but moved or changed if needed) -->
                    <div class="bg-[#161514] text-[#f2e8d5] p-6 rounded-[28px] relative overflow-hidden group shadow-xl border border-white/5">
                        <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-[#c97e60]/20 rounded-full blur-3xl group-hover:bg-[#c97e60]/40 transition-all duration-1000"></div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-[#c97e60] rounded-xl flex items-center justify-center text-white shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-[#a8a196] uppercase tracking-[0.2em] mb-0.5 opacity-70 font-['Plus_Jakarta_Sans',sans-serif]">Materi Dikuasai</p>
                                <p class="text-2xl font-bold font-['Plus_Jakarta_Sans',sans-serif]">{{ (santri.achievements || []).length }} Subjek</p>
                            </div>
                        </div>
                    </div>

                    <!-- Learning Stacks (Materi) -->
                    <div>
                        <div class="flex justify-between items-center mb-5">
                            <p class="text-[10px] font-bold text-[#a8a196] uppercase tracking-[0.2em] opacity-70">Mastered Skills</p>
                            <span class="text-xs font-bold text-[#c97e60] font-['Plus_Jakarta_Sans',sans-serif] tracking-wider">PROGRESS</span>
                        </div>
                        
                        <div class="space-y-5">
                            <div v-for="ach in (santri.achievements || [])" :key="ach.id" class="space-y-2 group">
                                <div class="flex justify-between text-[10px] font-bold uppercase tracking-[0.1em] text-[#161514]/80">
                                    <span class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#c97e60]"></div>
                                        {{ ach.materi?.nama }}
                                    </span>
                                    <span class="text-[#c97e60]">{{ ach.nilai }}%</span>
                                </div>
                                <div class="relative bg-white border border-[#e1d8c9] rounded-full overflow-hidden h-2.5 shadow-inner">
                                    <div class="absolute top-0 left-0 h-full bg-[#c97e60] rounded-full transition-all duration-1000 group-hover:brightness-110" :style="`width: ${ach.nilai}%`"></div>
                                </div>
                            </div>

                            <div v-if="(santri.achievements || []).length === 0" class="text-[10px] text-[#a8a196] font-bold uppercase tracking-[0.2em] opacity-50 italic py-4">
                                No achievement records.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 4: Experience -->
            <div class="w-[calc(100vw-3rem)] md:w-80 shrink-0 flex flex-col gap-8 snap-center md:snap-align-none">
                <div class="header-group">
                    <h2 class="text-2xl font-bold text-[#161514] tracking-tight mb-2 font-['Plus_Jakarta_Sans',sans-serif]">Experience</h2>
                    <div class="w-10 h-1.5 bg-[#c97e60] rounded-sm"></div>
                </div>
                
                <div class="space-y-4">
                    <p class="text-[10px] font-bold text-[#c97e60] tracking-[0.2em] uppercase font-['Plus_Jakarta_Sans',sans-serif]">Project & Work History</p>
                    
                    <div class="relative pl-6 space-y-10 mt-8">
                        <div class="absolute left-[3px] top-3 bottom-0 w-0.5 bg-[#e1d8c9] rounded-full"></div>
                        
                        <div v-for="port in (santri.portfolios || [])" :key="port.id" class="relative group">
                            <div class="absolute left-[-26px] top-1 w-3 h-3 rounded-full bg-white border-4 border-[#c97e60] z-10 group-hover:scale-125 transition-transform shadow-sm"></div>
                            
                            <div class="space-y-1.5">
                                <p class="text-[9px] font-bold text-[#a8a196] uppercase tracking-[0.15em] opacity-70">{{ formatDate(port.tanggal) }}</p>
                                <h4 class="text-base font-bold text-[#161514] font-['Plus_Jakarta_Sans',sans-serif] tracking-tight group-hover:text-[#c97e60] transition-colors line-clamp-1">{{ port.judul }}</h4>
                                <p class="text-[11px] text-[#5a5753] leading-relaxed line-clamp-3">{{ port.deskripsi }}</p>
                                <a v-if="port.link_url && port.link_url !== '#'" :href="port.link_url" target="_blank" class="text-[9px] font-extrabold text-[#c97e60] uppercase tracking-widest inline-flex items-center gap-1 hover:underline">
                                    View Project
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            </div>
                        </div>

                        <div v-if="(santri.portfolios || []).length === 0" class="text-[10px] text-[#a8a196] font-bold uppercase tracking-[0.2em] opacity-50 italic py-8">
                            No project records.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Padding element -->
            <div class="w-10 md:w-16 shrink-0"></div>

        </div>
    </div>
</template>
