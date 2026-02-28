<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    materis: Array
});

const isModalOpen = ref(false);
const editingMateri = ref(null);

const form = useForm({
    nama: '',
    kategori: 'IT',
    icon: '',
});

const openModal = (materi = null) => {
    editingMateri.value = materi;
    if (materi) {
        form.nama = materi.nama;
        form.kategori = materi.kategori;
        form.icon = materi.icon;
    } else {
        form.reset();
    }
    isModalOpen.value = true;
};

const submit = () => {
    if (editingMateri.value) {
        form.put(route('materi.update', editingMateri.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('materi.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            }
        });
    }
};

const deleteMateri = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus materi ini?')) {
        form.delete(route('materi.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen Kurikulum" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Manajemen <span class="text-[#d02e5c]">Kurikulum</span>
            </h2>
        </template>

        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header Controls -->
            <div class="flex justify-between items-center">
                <p class="text-[#a8a196] font-medium text-sm">Kelola daftar materi pendidikan dan skill yang tersedia.</p>
                <button 
                    @click="openModal()"
                    class="px-6 py-3 bg-[#d02e5c] text-white font-bold rounded-2xl hover:bg-[#b06a4f] transition-all shadow-lg shadow-[#d02e5c]/20 flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Materi
                </button>
            </div>

            <!-- Grid of Materis -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="materi in materis" :key="materi.id" 
                    class="bg-white dark:bg-[#161514] p-6 rounded-[28px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm hover:shadow-xl transition-all group relative overflow-hidden"
                >
                    <!-- Category Badge -->
                    <div class="absolute top-0 right-0 px-4 py-1.5 bg-[#d02e5c]/10 text-[#d02e5c] text-[9px] font-black uppercase tracking-widest rounded-bl-2xl">
                        {{ materi.kategori }}
                    </div>

                    <div class="flex items-start gap-5">
                        <div class="w-14 h-14 bg-[#f5f4f2] dark:bg-[#262524] rounded-2xl flex items-center justify-center text-[#d02e5c] group-hover:scale-110 transition-transform border border-[#ebeae8] dark:border-[#3e3c3a]">
                            <svg v-if="materi.kategori === 'IT'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                            <svg v-else-if="materi.kategori === 'Kitab'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            <svg v-else class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-[#161514] dark:text-[#f2e8d5] tracking-tight truncate">{{ materi.nama }}</h3>
                            <p v-if="materi.icon" class="text-[10px] text-[#a8a196] font-mono mt-1 opacity-70">Icon: {{ materi.icon }}</p>
                            
                            <div class="flex items-center gap-3 mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openModal(materi)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button @click="deleteMateri(materi.id)" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h.01" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="materis.length === 0" class="flex flex-col items-center justify-center py-20 text-[#a8a196] space-y-4">
                <svg class="w-20 h-20 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                <p class="font-bold text-lg">Belum ada materi terdaftar.</p>
                <button @click="openModal()" class="text-[#d02e5c] font-bold hover:underline">Klik di sini untuk menambah materi pertama</button>
            </div>

            <!-- Modal -->
            <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div @click="isModalOpen = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                
                <div class="relative bg-white dark:bg-[#161514] w-full max-w-md rounded-[32px] shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden transition-all scale-100">
                    <div class="p-8 space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5]">
                                {{ editingMateri ? 'Edit' : 'Tambah' }} <span class="text-[#d02e5c]">Materi</span>
                            </h3>
                            <button @click="isModalOpen = false" class="text-[#a8a196] hover:text-[#d02e5c]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Nama Materi</label>
                                <input v-model="form.nama" type="text" 
                                    class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] transition-all font-bold"
                                    placeholder="Contoh: Laravel Framework" required />
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Kategori</label>
                                <select v-model="form.kategori" 
                                    class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] transition-all font-bold">
                                    <option value="IT">IT (Programming/Design)</option>
                                    <option value="Kitab">Kitab (Agama)</option>
                                    <option value="Umum">Umum / Bahasa</option>
                                    <option value="Al-Quran">Al-Quran</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#a8a196] mb-2 px-1">Icon Identifier (Optional)</label>
                                <input v-model="form.icon" type="text" 
                                    class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] transition-all font-bold"
                                    placeholder="Contoh: laravel, vue, kitab" />
                            </div>

                            <div class="pt-4 flex gap-3">
                                <button type="button" @click="isModalOpen = false" 
                                    class="flex-1 px-6 py-4 bg-[#f5f4f2] dark:bg-[#262524] text-[#a8a196] font-bold rounded-2xl hover:bg-[#ebeae8] dark:hover:bg-[#3e3c3a] transition-all">
                                    Batal
                                </button>
                                <button type="submit" 
                                    class="flex-1 px-6 py-4 bg-[#d02e5c] text-white font-bold rounded-2xl hover:bg-[#b06a4f] transition-all shadow-lg shadow-[#d02e5c]/20"
                                    :disabled="form.processing"
                                >
                                    {{ editingMateri ? 'Simpan' : 'Tambah' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
