<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    activities: Array
});

const form = useForm({
    nama: '',
    keterangan: ''
});

const submit = () => {
    form.post(route('records.attendance.types.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};

const deleteActivity = (id) => {
    if (confirm('Hapus jenis kegiatan ini? Data absensi yang sudah ada mungkin akan terpengaruh.')) {
        router.delete(route('records.attendance.types.destroy', id));
    }
};
</script>

<template>
    <Head title="Pengaturan Absensi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <Link :href="route('records.attendance')" class="p-2 text-[#a8a196] hover:text-[#d02e5c] transition-colors bg-white dark:bg-[#161514] rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </Link>
                <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                    Pengaturan <span class="text-[#d02e5c]">Jenis Alat/Kegiatan</span>
                </h2>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Form Card -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-[#161514] shadow-[0_30px_60px_rgba(0,0,0,0.05)] dark:shadow-[0_30px_60px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-8">
                    <h3 class="text-xl font-bold mb-6">Tambah Kegiatan</h3>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Kegiatan</label>
                            <input v-model="form.nama" type="text" placeholder="Contoh: Sholat Subuh" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl p-4 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all font-bold" required>
                            <div v-if="form.errors.nama" class="text-xs text-rose-500 font-medium">{{ form.errors.nama }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan (Opsional)</label>
                            <textarea v-model="form.keterangan" rows="3" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl p-4 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all resize-none"></textarea>
                        </div>

                        <RedwoodButton 
                            type="submit" 
                            variant="primary"
                            :disabled="form.processing"
                            class="w-full"
                        >
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </template>
                            {{ form.processing ? 'Menyimpan...' : 'Tambah' }}
                        </RedwoodButton>
                    </form>
                </div>
            </div>

            <!-- List Card -->
            <div class="md:col-span-2">
                <div class="bg-white dark:bg-[#161514] shadow-[0_30px_60px_rgba(0,0,0,0.05)] dark:shadow-[0_30px_60px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50 border-b border-[#ebeae8] dark:border-[#3e3c3a]">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Kegiatan</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="act in activities" :key="act.id" class="hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-5">
                                    <p class="font-bold text-[#161514] dark:text-[#f2e8d5] uppercase tracking-tight">{{ act.nama }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-xs text-[#a8a196] font-medium">{{ act.keterangan || '-' }}</p>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button @click="deleteActivity(act.id)" class="p-2 text-[#a8a196] hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="activities.length === 0">
                                <td colspan="3" class="px-8 py-10 text-center text-[#a8a196] font-medium italic">Belum ada jenis kegiatan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
