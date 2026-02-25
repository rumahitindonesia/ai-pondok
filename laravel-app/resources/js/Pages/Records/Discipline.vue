<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    pelanggarans: Array
});

const deleteRecord = (id) => {
    if (confirm('Hapus catatan pelanggaran ini?')) {
        router.delete(route('records.discipline.destroy', id));
    }
};
</script>

<template>
    <Head title="Kedisiplinan Santri" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Log <span class="text-[#c97e60]">Kedisiplinan</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Action Bar -->
            <div class="flex justify-end">
                <RedwoodButton 
                    as="Link"
                    :href="route('records.discipline.create')"
                    variant="primary"
                >
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Input Pelanggaran
                </RedwoodButton>
            </div>
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50 border-b border-[#ebeae8] dark:border-[#3e3c3a]">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Tgl</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Santri</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Pelanggaran</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-center">Poin</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="p in pelanggarans" :key="p.id" class="hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-5">
                                    <p class="text-xs font-medium">{{ new Date(p.tanggal).toLocaleDateString('id-ID') }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ p.santri.nama }}</p>
                                    <p class="text-[10px] text-[#a8a196] uppercase font-bold">{{ p.santri.nis }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5]">{{ p.judul_pelanggaran }}</p>
                                    <p class="text-[10px] text-[#a8a196] line-clamp-1">{{ p.deskripsi }}</p>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 rounded-full text-[10px] font-black">
                                        +{{ p.poin }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button @click="deleteRecord(p.id)" class="p-2 text-[#a8a196] hover:text-rose-500 hover:bg-rose-50/50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="pelanggarans.length === 0">
                                <td colspan="5" class="px-8 py-12 text-center text-[#a8a196] font-medium italic">Belum ada catatan pelanggaran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
