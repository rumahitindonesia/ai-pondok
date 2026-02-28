<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    santris: Array
});

const form = useForm({
    santri_id: '',
    judul_pelanggaran: '',
    deskripsi: '',
    poin: 10,
    tanggal: new Date().toISOString().split('T')[0],
});

const submit = () => {
    form.post(route('records.discipline.store'));
};
</script>

<template>
    <Head title="Catat Pelanggaran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <Link :href="route('records.discipline')" class="p-2 text-[#a8a196] hover:text-[#d02e5c] transition-colors bg-white dark:bg-[#161514] rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </Link>
                <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                    Input <span class="text-[#d02e5c]">Pelanggaran</span>
                </h2>
            </div>
        </template>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-[#161514] shadow-[0_30px_60px_rgba(0,0,0,0.05)] dark:shadow-[0_30px_60px_rgba(0,0,0,0.3)] rounded-[40px] border border-[#ebeae8] dark:border-[#3e3c3a] p-12">
                <form @submit.prevent="submit" class="space-y-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Pilih Santri</label>
                        <select v-model="form.santri_id" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all font-bold" required>
                            <option value="" disabled>Pilih Nama Santri...</option>
                            <option v-for="santri in santris" :key="santri.id" :value="santri.id">
                                {{ santri.nama }} ({{ santri.nis || 'No NIS' }})
                            </option>
                        </select>
                        <div v-if="form.errors.santri_id" class="text-xs text-rose-500 font-medium">{{ form.errors.santri_id }}</div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Judul Pelanggaran</label>
                        <input v-model="form.judul_pelanggaran" type="text" placeholder="Contoh: Merokok, Keluar Tanpa Ijin" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Deskripsi Kejadian</label>
                        <textarea v-model="form.deskripsi" rows="3" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Poin Hukuman</label>
                            <input v-model="form.poin" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all font-bold text-rose-500" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Tanggal Kejadian</label>
                            <input v-model="form.tanggal" type="date" class="w-full bg-[#fcf8f5] dark:bg-[#21201f] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5 outline-none focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all font-bold" required>
                        </div>
                    </div>

                    <div class="pt-6">
                        <RedwoodButton 
                            type="submit" 
                            variant="primary"
                            size="lg"
                            :disabled="form.processing"
                            class="w-full"
                        >
                            <template #icon>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </template>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pelanggaran' }}
                        </RedwoodButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
