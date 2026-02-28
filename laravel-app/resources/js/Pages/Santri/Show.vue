<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    santri: Object,
    materis: Array
});

const activeTab = ref('profile');

// Achievement Form
const achievementForm = useForm({
    materi_id: '',
    tanggal_selesai: new Date().toISOString().split('T')[0],
    nilai: 100
});

const submitAchievement = () => {
    achievementForm.post(route('santri.achievements.store', props.santri.id), {
        onSuccess: () => {
            achievementForm.reset('materi_id', 'nilai');
        }
    });
};

const deleteAchievement = (id) => {
    if (confirm('Hapus pencapaian ini?')) {
        useForm({}).delete(route('achievements.destroy', id));
    }
};

// Portfolio Form
const isPortfolioModalOpen = ref(false);
const editingPortfolio = ref(null);
const portfolioForm = useForm({
    judul: '',
    deskripsi: '',
    link_url: '',
    tanggal: new Date().toISOString().split('T')[0],
});

const openPortfolioModal = (portfolio = null) => {
    editingPortfolio.value = portfolio;
    if (portfolio) {
        portfolioForm.judul = portfolio.judul;
        portfolioForm.deskripsi = portfolio.deskripsi;
        portfolioForm.link_url = portfolio.link_url;
        portfolioForm.tanggal = portfolio.tanggal;
    } else {
        portfolioForm.reset();
    }
    isPortfolioModalOpen.value = true;
};

const submitPortfolio = () => {
    if (editingPortfolio.value) {
        portfolioForm.put(route('portfolios.update', editingPortfolio.value.id), {
            onSuccess: () => isPortfolioModalOpen.value = false
        });
    } else {
        portfolioForm.post(route('santri.portfolios.store', props.santri.id), {
            onSuccess: () => isPortfolioModalOpen.value = false
        });
    }
};

const deletePortfolio = (id) => {
    if (confirm('Hapus project ini?')) {
        useForm({}).delete(route('portfolios.destroy', id));
    }
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<template>
    <Head :title="'Profil ' + santri.nama" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <Link :href="route('santri.index')" class="p-2 text-[#a8a196] hover:text-[#d02e5c] transition-colors bg-white dark:bg-[#161514] rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </Link>
                <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                    Manajemen <span class="text-[#d02e5c]">Santri</span>
                </h2>
            </div>
        </template>

        <div class="space-y-8 max-w-6xl mx-auto">
            <!-- Header Profile Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 opacity-[0.03] dark:opacity-10 pointer-events-none">
                    <svg viewBox="0 0 100 100" fill="#d02e5c"><circle cx="80" cy="20" r="50" /></svg>
                </div>

                <div class="flex flex-col md:flex-row items-center md:items-start gap-10 relative z-10">
                    <div class="w-40 h-40 rounded-[32px] bg-[#f5f4f2] dark:bg-[#262524] border-4 border-white dark:border-[#161514] shadow-2xl overflow-hidden flex items-center justify-center text-5xl font-black text-[#d02e5c] shrink-0">
                        <img v-if="santri.foto" :src="`/storage/${santri.foto}`" class="w-full h-full object-cover">
                        <span v-else>{{ santri.nama.charAt(0) }}</span>
                    </div>
                    
                    <div class="flex-1 text-center md:text-left space-y-4">
                        <div>
                            <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-[#d02e5c]/10 text-[#d02e5c] mb-2 inline-block">
                                {{ santri.nis || 'BELUM ADA NIS' }}
                            </span>
                            <h1 class="text-4xl font-black text-[#161514] dark:text-[#f2e8d5] tracking-tight">{{ santri.nama }}</h1>
                            <p class="text-[#a8a196] font-medium text-lg mt-1 italic">Wali: {{ santri.wali?.nama }} ({{ santri.wali?.hubungan }})</p>
                        </div>

                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 pt-4">
                            <div class="px-6 py-3 bg-[#fcf8f5] dark:bg-[#1a1918] rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                <p class="text-[9px] font-black uppercase tracking-widest text-[#a8a196] mb-1">Status</p>
                                <p class="font-bold text-[#d02e5c] uppercase">{{ santri.status }}</p>
                            </div>
                            <div class="px-6 py-3 bg-[#fcf8f5] dark:bg-[#1a1918] rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                <p class="text-[9px] font-black uppercase tracking-widest text-[#a8a196] mb-1">Public Profile</p>
                                <a :href="route('public.santri.profile', santri.nis)" target="_blank" class="font-bold text-[#161514] dark:text-[#f2e8d5] hover:text-[#d02e5c] flex items-center gap-1 transition-colors">
                                    View Live <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <Link :href="route('santri.edit', santri.id)" class="px-8 py-4 bg-[#d02e5c] text-white font-bold rounded-[24px] hover:bg-[#b06a4f] transition-all shadow-lg shadow-[#d02e5c]/20 text-center">
                            Edit Profil Dasar
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-[#ebeae8] dark:border-[#3e3c3a] gap-10">
                <button @click="activeTab = 'profile'" 
                    :class="[activeTab === 'profile' ? 'text-[#d02e5c] border-b-4 border-[#d02e5c]' : 'text-[#a8a196] hover:text-[#161514] dark:hover:text-white']"
                    class="pb-4 text-sm font-black uppercase tracking-widest transition-all"
                >
                    Ringkasan
                </button>
                <button @click="activeTab = 'academic'" 
                    :class="[activeTab === 'academic' ? 'text-[#d02e5c] border-b-4 border-[#d02e5c]' : 'text-[#a8a196] hover:text-[#161514] dark:hover:text-white']"
                    class="pb-4 text-sm font-black uppercase tracking-widest transition-all"
                >
                    Akademik & Skill
                </button>
                <button @click="activeTab = 'portfolio'" 
                    :class="[activeTab === 'portfolio' ? 'text-[#d02e5c] border-b-4 border-[#d02e5c]' : 'text-[#a8a196] hover:text-[#161514] dark:hover:text-white']"
                    class="pb-4 text-sm font-black uppercase tracking-widest transition-all"
                >
                    Portofolio
                </button>
            </div>

            <!-- Tab Content -->
            <div class="min-h-[400px]">
                
                <!-- Tab: Profile (Existing Stats) -->
                <div v-if="activeTab === 'profile'" class="space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-500">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-4">Absensi Bulan Ini</h4>
                            <div class="text-3xl font-black text-[#161514] dark:text-[#f2e8d5]">98<span class="text-sm font-medium text-[#a8a196] ml-1">%</span></div>
                        </div>
                        <div class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-4">Total Pelanggaran</h4>
                            <div class="text-3xl font-black text-rose-500">{{ santri.pelanggarans.length }}<span class="text-sm font-medium text-[#a8a196] ml-1">Poin</span></div>
                        </div>
                        <div class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-4">Status Tagihan</h4>
                            <div class="text-3xl font-black text-emerald-500">Lunas</div>
                        </div>
                    </div>

                    <!-- About Me Section -->
                    <div class="bg-white dark:bg-[#161514] p-10 rounded-[40px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 bg-[#d02e5c]/10 rounded-xl flex items-center justify-center text-[#d02e5c]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <h4 class="text-lg font-black tracking-tight">Tentang Santri (Bio)</h4>
                        </div>
                        <div class="prose dark:prose-invert max-w-none">
                            <p v-if="santri.bio" class="text-[#5a5753] dark:text-[#a8a196] leading-relaxed whitespace-pre-wrap font-medium">
                                {{ santri.bio }}
                            </p>
                            <div v-else class="flex flex-col items-center justify-center py-10 bg-[#fcf8f5] dark:bg-[#1a1918] rounded-3xl border-2 border-dashed border-[#ebeae8] dark:border-[#3e3c3a]">
                                <p class="text-[#a8a196] font-bold italic text-sm">Belum ada informasi biografi.</p>
                                <Link :href="route('santri.edit', santri.id)" class="mt-4 text-[#d02e5c] font-black text-[10px] uppercase tracking-widest hover:underline">
                                    Lengkapi Profil Sekarang →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Academic/Skill -->
                <div v-if="activeTab === 'academic'" class="space-y-10 animate-in fade-in slide-in-from-bottom-2 duration-500">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <!-- Left: List of Achievements -->
                        <div class="lg:col-span-2 space-y-6">
                            <h3 class="text-xl font-black text-[#161514] dark:text-[#f2e8d5] tracking-tight mb-6">Pencapaian Kurikulum</h3>
                            
                            <div v-for="ach in santri.achievements" :key="ach.id" 
                                class="bg-white dark:bg-[#161514] p-6 rounded-3xl border border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between shadow-sm group"
                            >
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 bg-[#d02e5c]/10 text-[#d02e5c] rounded-2xl flex items-center justify-center font-bold">
                                        {{ ach.materi?.kategori.charAt(0) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ ach.materi?.nama }}</h4>
                                        <p class="text-[10px] text-[#a8a196] uppercase tracking-widest font-bold">Lulus: {{ formatDate(ach.tanggal_selesai) }} • Nilai: {{ ach.nilai }}</p>
                                    </div>
                                </div>
                                <button @click="deleteAchievement(ach.id)" class="text-rose-500 p-2 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h.01" /></svg>
                                </button>
                            </div>

                            <div v-if="santri.achievements.length === 0" class="text-center py-20 bg-[#f5f4f2] dark:bg-[#1a1918] rounded-[32px] border-2 border-dashed border-[#ebeae8] dark:border-[#3e3c3a]">
                                <p class="text-[#a8a196] font-bold">Belum ada pencapaian materi.</p>
                            </div>
                        </div>

                        <!-- Right: Add Form -->
                        <div class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-xl h-fit sticky top-8">
                            <h3 class="text-lg font-black text-[#161514] dark:text-[#f2e8d5] mb-6 uppercase tracking-wider">Catat Pencapaian</h3>
                            <form @submit.prevent="submitAchievement" class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Pilih Materi</label>
                                    <select v-model="achievementForm.materi_id" class="w-full border-none bg-[#f5f4f2] dark:bg-[#262524] rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-[#d02e5c]">
                                        <option value="" disabled>Pilih Materi</option>
                                        <option v-for="m in materis" :key="m.id" :value="m.id">{{ m.nama }} ({{ m.kategori }})</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Tanggal Lulus</label>
                                    <input v-model="achievementForm.tanggal_selesai" type="date" class="w-full border-none bg-[#f5f4f2] dark:bg-[#262524] rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-[#d02e5c]" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Nilai (0-100)</label>
                                    <input v-model="achievementForm.nilai" type="number" class="w-full border-none bg-[#f5f4f2] dark:bg-[#262524] rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-[#d02e5c]" />
                                </div>
                                <button type="submit" class="w-full py-4 bg-[#d02e5c] text-white font-bold rounded-2xl mt-4 shadow-lg shadow-[#d02e5c]/20 hover:bg-[#b06a4f] transition-all" :disabled="achievementForm.processing">
                                    Simpan Pencapaian
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab: Portfolio -->
                <div v-if="activeTab === 'portfolio'" class="space-y-10 animate-in fade-in slide-in-from-bottom-2 duration-500">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-black text-[#161514] dark:text-[#f2e8d5] tracking-tight">Karya & Project</h3>
                        <button @click="openPortfolioModal()" class="px-6 py-3 bg-[#d02e5c]/10 text-[#d02e5c] font-bold rounded-2xl hover:bg-[#d02e5c] hover:text-white transition-all transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah Project
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div v-for="project in santri.portfolios" :key="project.id" 
                            class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm group hover:border-[#d02e5c] transition-colors relative"
                        >
                            <div class="space-y-4">
                                <p class="text-[10px] font-bold text-[#a8a196] uppercase tracking-widest">{{ formatDate(project.tanggal) }}</p>
                                <h4 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5] tracking-tight">{{ project.judul }}</h4>
                                <p class="text-[#5a5753] text-sm leading-relaxed line-clamp-3">{{ project.deskripsi }}</p>
                                <a v-if="project.link_url" :href="project.link_url" target="_blank" class="text-[10px] font-black uppercase tracking-widest text-[#d02e5c] hover:underline flex items-center gap-1">
                                    Link Project <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            </div>

                            <div class="flex items-center gap-2 mt-8 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openPortfolioModal(project)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button @click="deletePortfolio(project.id)" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h.01" /></svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="santri.portfolios.length === 0" class="md:col-span-2 text-center py-20 bg-[#f5f4f2] dark:bg-[#1a1918] rounded-[32px] border-2 border-dashed border-[#ebeae8] dark:border-[#3e3c3a]">
                            <p class="text-[#a8a196] font-bold">Santri ini belum mengunggah karya.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Portfolio Modal -->
        <div v-if="isPortfolioModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div @click="isPortfolioModalOpen = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-[#161514] w-full max-w-lg rounded-[32px] shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="p-8 space-y-6">
                    <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5]">
                        {{ editingPortfolio ? 'Edit' : 'Tambah' }} <span class="text-[#d02e5c]">Project</span>
                    </h3>
                    <form @submit.prevent="submitPortfolio" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Judul Project</label>
                            <input v-model="portfolioForm.judul" type="text" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Deskripsi</label>
                            <textarea v-model="portfolioForm.deskripsi" rows="4" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] font-bold text-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Link URL</label>
                                <input v-model="portfolioForm.link_url" type="text" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] font-bold" placeholder="https://..." />
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Tanggal</label>
                                <input v-model="portfolioForm.tanggal" type="date" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] font-bold" />
                            </div>
                        </div>
                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="isPortfolioModalOpen = false" class="flex-1 px-6 py-4 bg-[#f5f4f2] dark:bg-[#262524] text-[#a8a196] font-bold rounded-2xl hover:bg-[#ebeae8] dark:hover:bg-[#3e3c3a] transition-all">Batal</button>
                            <button type="submit" class="flex-1 px-6 py-4 bg-[#d02e5c] text-white font-bold rounded-2xl hover:bg-[#b06a4f] shadow-lg shadow-[#d02e5c]/20 transition-all">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
