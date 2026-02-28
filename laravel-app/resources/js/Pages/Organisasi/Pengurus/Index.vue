<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';

const props = defineProps({
    pengurusList: Array,
    jabatans: Array,
    santris: Array
});

const showingModal = ref(false);
const form = useForm({
    santri_id: '',
    jabatan_id: '',
    status: true
});

// Search functionality
const searchQuery = ref('');
const filteredPengurus = computed(() => {
    if (!searchQuery.value) return props.pengurusList;
    const lowerQuery = searchQuery.value.toLowerCase();
    return props.pengurusList.filter(p => 
        p.santri?.nama.toLowerCase().includes(lowerQuery) ||
        p.jabatan?.nama.toLowerCase().includes(lowerQuery) ||
        p.santri?.nis.includes(lowerQuery)
    );
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    form.post(route('organisasi.pengurus.store'), {
        onSuccess: () => closeModal(),
    });
};

const toggleStatus = (pengurus) => {
    const statusForm = useForm({ status: !pengurus.status });
    statusForm.put(route('organisasi.pengurus.update', pengurus.id), {
        preserveScroll: true
    });
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
};

const deletePengurus = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data menjabat ini?')) {
        useForm({}).delete(route('organisasi.pengurus.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Data Pengurus" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Data <span class="text-[#d02e5c]">Pengurus</span>
            </h2>
        </template>

        <div class="space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 max-w-md relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#a8a196]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input v-model="searchQuery" type="text" placeholder="Cari nama pengurus atau jabatan..." class="w-full bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl pl-12 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] focus:border-[#d02e5c] transition-all shadow-sm" />
                </div>
                
                <RedwoodButton @click="openCreateModal">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Pilih Pengurus Baru
                </RedwoodButton>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50/50 dark:bg-[#1a1918]/50">
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Nama Pengurus / Santri</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Jabatan</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Status Aktif</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-if="filteredPengurus.length === 0">
                                <td colspan="4" class="px-8 py-12 text-center text-[#a8a196] font-medium">Belum ada pengurus yang ditugaskan.</td>
                            </tr>
                            <tr v-for="pengurus in filteredPengurus" :key="pengurus.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors" :class="{'opacity-60': !pengurus.status}">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-[#f5f4f2] dark:bg-[#262524] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden shrink-0 flex items-center justify-center">
                                            <img v-if="pengurus.santri?.foto" :src="`/storage/${pengurus.santri.foto}`" class="w-full h-full object-cover rounded-2xl" />
                                            <span v-else class="text-[#d02e5c] font-black text-lg">{{ pengurus.santri?.nama?.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ pengurus.santri?.nama }}</p>
                                            <p class="text-[11px] text-[#a8a196]">NIS: {{ pengurus.santri?.nis }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ pengurus.jabatan?.nama }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <button @click="toggleStatus(pengurus)" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:ring-offset-2" :class="pengurus.status ? 'bg-[#d02e5c]' : 'bg-gray-200 dark:bg-[#3e3c3a]'">
                                        <span class="sr-only">Toggle Status</span>
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="pengurus.status ? 'translate-x-6' : 'translate-x-1'" />
                                    </button>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="deletePengurus(pengurus.id)" title="Hapus Permanen" class="p-2 text-[#a8a196] hover:text-rose-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div v-if="showingModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
            <div class="relative bg-white dark:bg-[#161514] w-full max-w-xl rounded-[40px] shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="p-8 lg:p-12">
                    <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5] mb-8">
                        Tetapkan Pengurus Baru
                    </h3>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Pilih Santri</label>
                            <select v-model="form.santri_id" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" required>
                                <option value="" disabled>-- Cari/Pilih Santri --</option>
                                <option v-for="santri in santris" :key="santri.id" :value="santri.id">
                                    {{ santri.nama }} ({{ santri.nis }})
                                </option>
                            </select>
                            <p v-if="form.errors.santri_id" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.santri_id }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Jabatan Organisasi</label>
                            <select v-model="form.jabatan_id" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" required>
                                <option value="" disabled>-- Pilih Jabatan --</option>
                                <option v-for="jabatan in jabatans" :key="jabatan.id" :value="jabatan.id">
                                    {{ jabatan.nama }}
                                </option>
                            </select>
                            <p v-if="form.errors.jabatan_id" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.jabatan_id }}</p>
                        </div>
                        
                        <div class="pt-4 flex items-center justify-between border-t border-[#ebeae8] dark:border-[#3e3c3a]">
                            <div>
                                <p class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5]">Status Aktif</p>
                                <p class="text-[11px] text-[#a8a196]">Tandai hijau jika sedang menjabat saat ini.</p>
                            </div>
                            <button type="button" @click="form.status = !form.status" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:ring-offset-2" :class="form.status ? 'bg-[#d02e5c]' : 'bg-gray-200 dark:bg-[#3e3c3a]'">
                                <span class="sr-only">Toggle Status</span>
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="form.status ? 'translate-x-6' : 'translate-x-1'" />
                            </button>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-12">
                            <button type="button" @click="closeModal" class="text-sm font-black uppercase tracking-widest text-[#a8a196] hover:text-[#161514] dark:hover:text-[#f2e8d5] transition-colors">Batal</button>
                            <RedwoodButton type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                                Simpan Pengurus
                            </RedwoodButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
