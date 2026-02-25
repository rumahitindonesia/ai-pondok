<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    registrations: Object
});

const statusForm = useForm({
    status_seleksi: ''
});

const updateStatus = (id, status) => {
    statusForm.status_seleksi = status;
    statusForm.patch(route('admin.psb.status', id), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Manajemen Registrasi PSB" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Penerimaan <span class="text-[#c97e60]">Santri Baru</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Table Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50/50 dark:bg-[#1a1918]/50">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Tgl Daftar</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Calon Santri</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Wali / Kontak</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Berkas</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Status Seleksi</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="reg in registrations.data" :key="reg.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-5">
                                    <p class="text-xs font-medium">{{ new Date(reg.created_at).toLocaleDateString('id-ID') }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <div>
                                        <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ reg.nama_calon }}</p>
                                        <p class="text-[10px] text-[#a8a196]">{{ reg.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-medium">{{ reg.nama_wali }}</p>
                                    <p class="text-xs text-[#c97e60] font-bold">{{ reg.no_hp_wali }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex gap-2">
                                        <a v-if="reg.berkas_kk" :href="'/storage/' + reg.berkas_kk" target="_blank" class="w-8 h-8 rounded-lg bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-[#a8a196] hover:text-[#c97e60] transition-colors border border-transparent hover:border-[#c97e60]/20" title="KK">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </a>
                                        <a v-if="reg.berkas_akta" :href="'/storage/' + reg.berkas_akta" target="_blank" class="w-8 h-8 rounded-lg bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-[#a8a196] hover:text-[#c97e60] transition-colors border border-transparent hover:border-[#c97e60]/20" title="Akta">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span 
                                        :class="[
                                            'px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-wider',
                                            reg.status_seleksi === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                            reg.status_seleksi === 'diterima' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                            reg.status_seleksi === 'cadangan' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                                            'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
                                        ]"
                                    >
                                        {{ reg.status_seleksi }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <RedwoodButton 
                                            variant="ghost" 
                                            @click="updateStatus(reg.id, 'diterima')"
                                            class="!text-emerald-500 hover:bg-emerald-500/10"
                                            size="sm"
                                        >
                                            <template #icon>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </template>
                                            Terima
                                        </RedwoodButton>
                                        <RedwoodButton 
                                            variant="ghost" 
                                            @click="updateStatus(reg.id, 'ditolak')"
                                            class="!text-rose-500 hover:bg-rose-500/10"
                                            size="sm"
                                        >
                                            <template #icon>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </template>
                                            Tolak
                                        </RedwoodButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-8 py-6 border-t border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between bg-gray-50/30 dark:bg-[#1a1918]/30">
                    <p class="text-xs text-[#a8a196]">Menampilkan {{ registrations.total }} pendaftar</p>
                    <div class="flex gap-2">
                         <!-- Pagination links here -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
