<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    kelas: Array,
    kamars: Array
});

const kelasForm = useForm({
    nama: '',
    keterangan: ''
});

const kamarForm = useForm({
    nama: '',
    keterangan: ''
});

const submitKelas = () => {
    kelasForm.post(route('admin.kelas.store'), {
        onSuccess: () => {
            kelasForm.reset();
        }
    });
};

const submitKamar = () => {
    kamarForm.post(route('admin.kamars.store'), {
        onSuccess: () => {
            kamarForm.reset();
        }
    });
};

const deleteKelas = (id) => {
    if (confirm('Hapus data kelas ini?')) {
        router.delete(route('admin.kelas.destroy', id), {
            preserveScroll: true
        });
    }
};

const deleteKamar = (id) => {
    if (confirm('Hapus data kamar ini?')) {
        router.delete(route('admin.kamars.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Pengaturan Data Santri" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <Link :href="route('santri.index')" class="p-3 text-[#a8a196] hover:text-[#c97e60] transition-all bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl shadow-sm group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                    Pengaturan <span class="text-[#c97e60]">Kamar & Kelas</span>
                </h2>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Kelas Section -->
            <div class="space-y-8">
                <div class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                    <h3 class="text-xl font-black mb-6 text-[#161514] dark:text-[#f2e8d5]">Tambah Kelas</h3>
                    <form @submit.prevent="submitKelas" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] ml-1">Nama Kelas</label>
                            <input 
                                v-model="kelasForm.nama" 
                                type="text" 
                                placeholder="Misal: 10-A, Tahfidz B, dll"
                                class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60] transition-all font-bold"
                                required
                            >
                            <div v-if="kelasForm.errors.nama" class="text-xs text-rose-500 font-bold ml-1">{{ kelasForm.errors.nama }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] ml-1">Keterangan (Opsional)</label>
                            <textarea 
                                v-model="kelasForm.keterangan" 
                                class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60] transition-all font-bold min-h-[100px]"
                            ></textarea>
                        </div>
                        <RedwoodButton type="submit" class="w-full" :disabled="kelasForm.processing">
                            {{ kelasForm.processing ? 'Menyimpan...' : 'Tambah Kelas' }}
                        </RedwoodButton>
                    </form>
                </div>

                <div class="bg-white dark:bg-[#161514] overflow-hidden rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50 border-b border-[#ebeae8] dark:border-[#3e3c3a]">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Kelas</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="item in kelas" :key="item.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-6 py-4 font-bold text-sm">{{ item.nama }}</td>
                                <td class="px-6 py-4 text-xs text-[#a8a196]">{{ item.keterangan || '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteKelas(item.id)" class="p-2 text-[#a8a196] hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="kelas.length === 0">
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-[#a8a196] italic">Belum ada data kelas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kamar Section -->
            <div class="space-y-8">
                <div class="bg-white dark:bg-[#161514] p-8 rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                    <h3 class="text-xl font-black mb-6 text-[#161514] dark:text-[#f2e8d5]">Tambah Kamar</h3>
                    <form @submit.prevent="submitKamar" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] ml-1">Nama Kamar</label>
                            <input 
                                v-model="kamarForm.nama" 
                                type="text" 
                                placeholder="Misal: Al-Fatih, Maryam 1, dll"
                                class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60] transition-all font-bold"
                                required
                            >
                            <div v-if="kamarForm.errors.nama" class="text-xs text-rose-500 font-bold ml-1">{{ kamarForm.errors.nama }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196] ml-1">Keterangan (Opsional)</label>
                            <textarea 
                                v-model="kamarForm.keterangan" 
                                class="w-full p-4 bg-[#f8f7f5] dark:bg-[#1a1918] border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#c97e60] transition-all font-bold min-h-[100px]"
                            ></textarea>
                        </div>
                        <RedwoodButton type="submit" class="w-full" :disabled="kamarForm.processing">
                            {{ kamarForm.processing ? 'Menyimpan...' : 'Tambah Kamar' }}
                        </RedwoodButton>
                    </form>
                </div>

                <div class="bg-white dark:bg-[#161514] overflow-hidden rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-[#1a1918]/50 border-b border-[#ebeae8] dark:border-[#3e3c3a]">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Kamar</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Keterangan</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="item in kamars" :key="item.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-6 py-4 font-bold text-sm">{{ item.nama }}</td>
                                <td class="px-6 py-4 text-xs text-[#a8a196]">{{ item.keterangan || '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteKamar(item.id)" class="p-2 text-[#a8a196] hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="kamars.length === 0">
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-[#a8a196] italic">Belum ada data kamar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
