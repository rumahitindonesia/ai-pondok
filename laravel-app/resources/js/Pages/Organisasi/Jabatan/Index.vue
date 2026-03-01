<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import JabatanTreeNode from '@/Components/JabatanTreeNode.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

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
    form.parent_id = parentId;
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

const confirmState = ref({ show: false, id: null, message: '' });

const deleteJabatan = (id) => {
    confirmState.value = {
        show: true,
        id,
        message: 'Penghapusan akan gagal jika jabatan masih memiliki bawahan atau pengurus aktif.'
    };
};

const executeDelete = () => {
    form.delete(route('organisasi.jabatan.destroy', confirmState.value.id));
    confirmState.value.show = false;
};
</script>

<template>
    <Head title="Struktur Jabatan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Struktur <span class="text-[#d02e5c]">Organisasi</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Header Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-[#a8a196] text-sm">Kelola hierarki jabatan pengurus — kedalaman tidak terbatas</p>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="flex items-center gap-1.5 text-xs text-[#a8a196]">
                            <span class="inline-block w-3 h-3 rounded-sm bg-[#d02e5c]"></span> Level 0 (Paling Atas)
                        </span>
                        <span class="flex items-center gap-1.5 text-xs text-[#a8a196]">
                            <span class="inline-block w-3 h-3 rounded-sm bg-[#d02e5c]/15"></span> Sub-Level
                        </span>
                    </div>
                </div>
                <RedwoodButton @click="openCreateModal('')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Tambah Jabatan Utama
                </RedwoodButton>
            </div>

            <!-- Recursive Tree Display -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-8 md:p-12 overflow-x-auto">
                <div v-if="jabatansTree.length === 0" class="text-center text-[#a8a196] py-12">
                    Belum ada struktur jabatan. Silakan tambahkan jabatan utama (Level 0).
                </div>

                <div class="space-y-4 min-w-[500px]">
                    <div v-for="jabatan in jabatansTree" :key="jabatan.id" class="space-y-0">
                        <JabatanTreeNode
                            :jabatan="jabatan"
                            :depth="0"
                            @edit="openEditModal"
                            @delete="deleteJabatan"
                            @add-child="openCreateModal"
                        />
                    </div>
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
                            <input v-model="form.nama" type="text" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" placeholder="Contoh: Direktur Utama" required />
                            <p v-if="form.errors.nama" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.nama }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Induk Jabatan (Parent)</label>
                            <select v-model="form.parent_id" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all">
                                <option value="">-- Level 0 (Paling Atas) --</option>
                                <option v-for="jab in allJabatans" :key="jab.id" :value="jab.id" :disabled="jab.id === editingJabatan?.id">
                                    {{ jab.nama }}
                                </option>
                            </select>
                            <p v-if="form.errors.parent_id" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.parent_id }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Deskripsi / Tugas (Opsional)</label>
                            <textarea v-model="form.deskripsi" rows="3" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" placeholder="Tugas pokok jabatan..."></textarea>
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

    <!-- Confirm Delete Modal -->
    <ConfirmModal
        :show="confirmState.show"
        title="Hapus Jabatan"
        :message="confirmState.message"
        confirm-text="Ya, Hapus"
        @confirm="executeDelete"
        @cancel="confirmState.show = false"
    />
</template>
