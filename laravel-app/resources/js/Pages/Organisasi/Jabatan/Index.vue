<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';

const props = defineProps({
    jabatansTree: Array,
    allJabatans: Array
});

const showingModal = ref(false);
const editingJabatan = ref(null);

const form = useForm({
    nama: '',
    parent_id: '',
    deskripsi: ''
});

const openCreateModal = (parentId = '') => {
    editingJabatan.value = null;
    form.reset();
    form.parent_id = parentId; // Pre-select parent if adding child specifically
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (jabatan) => {
    editingJabatan.value = jabatan;
    form.nama = jabatan.nama;
    form.parent_id = jabatan.parent_id || '';
    form.deskripsi = jabatan.deskripsi || '';
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (editingJabatan.value) {
        form.put(route('organisasi.jabatan.update', editingJabatan.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('organisasi.jabatan.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
};

const deleteJabatan = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus jabatan ini? Penghapusan akan gagal jika masih memiliki bawahan atau pengurus aktif.')) {
        form.delete(route('organisasi.jabatan.destroy', id));
    }
};

// Recursive component simulation within the same file for simplicity (Vue handles it well with named templates or v-for)
</script>

<template>
    <Head title="Struktur Jabatan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Struktur <span class="text-[#c97e60]">Organisasi</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Header Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <p class="text-[#a8a196] text-sm">Kelola hierarki jabatan pengurus Yayasan Pondok IT</p>
                <RedwoodButton @click="openCreateModal('')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Tambah Jabatan Utama
                </RedwoodButton>
            </div>

            <!-- Hierarchy Tree Display -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-8 md:p-12 overflow-x-auto">
                <div v-if="jabatansTree.length === 0" class="text-center text-[#a8a196] py-12">
                    Belum ada struktur jabatan. Silakan tambahkan jabatan utama (Level 0).
                </div>
                
                <!-- Tree Container -->
                <div class="space-y-4 min-w-[600px]">
                    <template v-for="jabatan in jabatansTree" :key="jabatan.id">
                        
                        <!-- Level 0 Item -->
                        <div class="relative bg-[#f5f4f2] dark:bg-[#262524] rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] p-5 flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-[#c97e60] text-white flex items-center justify-center font-black shadow-lg shadow-[#c97e60]/20">0</div>
                                <div>
                                    <h3 class="font-black text-lg text-[#161514] dark:text-[#f2e8d5]">{{ jabatan.nama }}</h3>
                                    <p v-if="jabatan.deskripsi" class="text-[11px] text-[#a8a196] mt-0.5">{{ jabatan.deskripsi }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openCreateModal(jabatan.id)" title="Tambah Bawahan" class="p-2 text-[#a8a196] hover:text-[#c97e60] bg-white dark:bg-[#1a1918] rounded-lg border border-[#ebeae8] dark:border-[#3e3c3a] transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                </button>
                                <button @click="openEditModal(jabatan)" class="p-2 text-[#a8a196] hover:text-[#c97e60] bg-white dark:bg-[#1a1918] rounded-lg border border-[#ebeae8] dark:border-[#3e3c3a] transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button @click="deleteJabatan(jabatan.id)" class="p-2 text-[#a8a196] hover:text-rose-500 bg-white dark:bg-[#1a1918] rounded-lg border border-[#ebeae8] dark:border-[#3e3c3a] transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Level 1 Children -->
                        <div v-if="jabatan.children?.length > 0" class="pl-14 space-y-3 mt-3 relative border-l-2 border-[#ebeae8] dark:border-[#3e3c3a] ml-5">
                            <template v-for="child1 in jabatan.children" :key="child1.id">
                                <div class="relative bg-white dark:bg-[#1a1918] rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] p-4 flex items-center justify-between group before:absolute before:w-6 before:h-0.5 before:bg-[#ebeae8] dark:before:bg-[#3e3c3a] before:-left-6 before:top-1/2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#c97e60]/10 text-[#c97e60] flex items-center justify-center font-black text-sm">1</div>
                                        <div>
                                            <h4 class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ child1.nama }}</h4>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openCreateModal(child1.id)" title="Tambah Bawahan" class="p-1.5 text-[#a8a196] hover:text-[#c97e60] bg-[#f5f4f2] dark:bg-[#262524] rounded-lg transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button>
                                        <button @click="openEditModal(child1)" class="p-1.5 text-[#a8a196] hover:text-[#c97e60] bg-[#f5f4f2] dark:bg-[#262524] rounded-lg transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                        <button @click="deleteJabatan(child1.id)" class="p-1.5 text-[#a8a196] hover:text-rose-500 bg-[#f5f4f2] dark:bg-[#262524] rounded-lg transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                    </div>
                                </div>

                                <!-- Level 2 Children -->
                                <div v-if="child1.children?.length > 0" class="pl-12 space-y-2 mt-2 relative border-l-2 border-[#ebeae8]/50 dark:border-[#3e3c3a]/50 ml-4 mb-4">
                                    <template v-for="child2 in child1.children" :key="child2.id">
                                        <div class="relative flex items-center justify-between group py-1 before:absolute before:w-6 before:h-px before:bg-[#ebeae8] dark:before:bg-[#3e3c3a] before:-left-6 before:top-1/2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-[#c97e60]/50"></div>
                                                <span class="text-sm text-[#161514] dark:text-[#a8a196]">{{ child2.nama }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click="openEditModal(child2)" class="text-[#a8a196] hover:text-[#c97e60] transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                                <button @click="deleteJabatan(child2.id)" class="text-[#a8a196] hover:text-rose-500 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div v-if="jabatan.children?.length > 0" class="h-4"></div> <!-- Spacing -->
                    </template>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showingModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
            <div class="relative bg-white dark:bg-[#161514] w-full max-w-xl rounded-[40px] shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="p-8 lg:p-12">
                    <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5] mb-8">
                        {{ editingJabatan ? 'Edit Jabatan' : 'Tambah Jabatan' }}
                    </h3>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Nama Jabatan</label>
                            <input v-model="form.nama" type="text" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#c97e60] transition-all" placeholder="Contoh: Direktur Utama" required />
                            <p v-if="form.errors.nama" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.nama }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Induk Jabatan (Parent)</label>
                            <select v-model="form.parent_id" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#c97e60] transition-all">
                                <option value="">-- Level 0 (Paling Atas) --</option>
                                <option v-for="jab in allJabatans" :key="jab.id" :value="jab.id" :disabled="jab.id === form.id">
                                    {{ jab.nama }}
                                </option>
                            </select>
                            <p v-if="form.errors.parent_id" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.parent_id }}</p>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Deskripsi / Tugas (Opsional)</label>
                            <textarea v-model="form.deskripsi" rows="3" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#c97e60] transition-all" placeholder="Tugas pokok jabatan..."></textarea>
                            <p v-if="form.errors.deskripsi" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.deskripsi }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-12">
                            <button type="button" @click="closeModal" class="text-sm font-black uppercase tracking-widest text-[#a8a196] hover:text-[#161514] dark:hover:text-[#f2e8d5] transition-colors">Batal</button>
                            <RedwoodButton type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                                {{ editingJabatan ? 'Simpan Perubahan' : 'Tambahkan' }}
                            </RedwoodButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
