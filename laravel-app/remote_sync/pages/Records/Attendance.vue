<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    santris: Array,
    existingAbsensi: Object,
    filters: Object,
    options: Object,
    activities: Array
});

const form = useForm({
    tanggal: props.filters.tanggal,
    jenis: props.filters.jenis,
    kelas: props.filters.kelas || '',
    kamar: props.filters.kamar || '',
    records: props.santris.map(s => ({
        santri_id: s.id,
        nama: s.nama,
        status: props.existingAbsensi[s.id]?.status || 'hadir',
        keterangan: props.existingAbsensi[s.id]?.keterangan || ''
    }))
});

watch(() => form.tanggal, (val) => {
    router.get(route('records.attendance'), { ...props.filters, tanggal: val }, { preserveState: true });
});

watch(() => form.jenis, (val) => {
    router.get(route('records.attendance'), { ...props.filters, jenis: val }, { preserveState: true });
});

watch(() => form.kelas, (val) => {
    router.get(route('records.attendance'), { ...props.filters, kelas: val }, { preserveState: true });
});

watch(() => form.kamar, (val) => {
    router.get(route('records.attendance'), { ...props.filters, kamar: val }, { preserveState: true });
});

watch(() => [props.santris, props.existingAbsensi], ([newSantris, newAbsensi]) => {
    form.records = newSantris.map(s => ({
        santri_id: s.id,
        nama: s.nama,
        status: newAbsensi[s.id]?.status || 'hadir',
        keterangan: newAbsensi[s.id]?.keterangan || ''
    }));
}, { deep: true });

const submit = () => {
    form.post(route('records.attendance.store'), {
        preserveScroll: true
    });
};

const statusOptions = [
    { value: 'hadir', label: 'Hadir', color: 'emerald' },
    { value: 'sakit', label: 'Sakit', color: 'amber' },
    { value: 'izin', label: 'Izin', color: 'blue' },
    { value: 'alfa', label: 'Alfa', color: 'rose' },
];
</script>

<template>
    <Head title="Absensi Harian" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Absensi <span class="text-[#c97e60]">Harian</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Action Bar -->
            <div class="flex flex-wrap justify-end items-center gap-4">
                <RedwoodButton 
                    as="Link"
                    :href="route('records.attendance.settings')"
                    variant="outline"
                    class="group"
                >
                    <template #icon>
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </template>
                </RedwoodButton>

                <!-- Class Filter -->
                <select 
                    v-model="form.kelas" 
                    class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm"
                >
                    <option value="">Semua Kelas</option>
                    <option v-for="k in options.kelas" :key="k" :value="k">{{ k }}</option>
                </select>

                <!-- Room Filter -->
                <select 
                    v-model="form.kamar" 
                    class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm"
                >
                    <option value="">Semua Kamar</option>
                    <option v-for="k in options.kamar" :key="k" :value="k">{{ k }}</option>
                </select>

                <input 
                    v-model="form.tanggal" 
                    type="date" 
                    class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm"
                >
                <select 
                    v-model="form.jenis" 
                    class="bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-[#c97e60] shadow-sm"
                >
                    <option v-for="act in activities" :key="act.id" :value="act.nama">{{ act.nama }}</option>
                </select>
            </div>
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50 border-b border-[#ebeae8] dark:border-[#3e3c3a]">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Santri</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Status Kehadiran</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="(record, index) in form.records" :key="record.santri_id" class="hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-5">
                                    <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ record.nama }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex gap-2">
                                        <button 
                                            v-for="opt in statusOptions" 
                                            :key="opt.value"
                                            type="button"
                                            @click="record.status = opt.value"
                                            :class="[
                                                'px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border-2',
                                                record.status === opt.value 
                                                    ? `bg-${opt.color}-100 text-${opt.color}-700 border-${opt.color}-200 dark:bg-${opt.color}-900/30 dark:text-${opt.color}-400 dark:border-${opt.color}-800/50 shadow-sm` 
                                                    : 'bg-transparent text-[#a8a196] border-transparent hover:bg-gray-100 dark:hover:bg-[#262524]'
                                            ]"
                                        >
                                            {{ opt.label }}
                                        </button>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <input 
                                        v-model="record.keterangan" 
                                        type="text" 
                                        placeholder="Catatan..."
                                        class="w-full bg-transparent border-none p-0 text-sm focus:ring-0 placeholder-[#a8a196]"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end p-4">
                <RedwoodButton 
                    type="button" 
                    variant="primary" 
                    size="lg"
                    :disabled="form.processing"
                    @click="submit"
                >
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    </template>
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Absensi' }}
                </RedwoodButton>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
