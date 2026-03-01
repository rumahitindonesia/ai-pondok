<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    questions: Array
});

// Modal State
const isModalOpen = ref(false);
const modalMode = ref('add'); // 'add' or 'edit'
const optionsString = ref(''); // Local state for comma-separated options

const form = useForm({
    id: null,
    question_text: '',
    type: 'text',
    options: null, // will be an array
    is_required: true,
});

const openAddModal = () => {
    modalMode.value = 'add';
    form.reset();
    form.id = null;
    optionsString.value = '';
    isModalOpen.value = true;
};

const openEditModal = (q) => {
    modalMode.value = 'edit';
    form.id = q.id;
    form.question_text = q.question_text;
    form.type = q.type;
    form.is_required = q.is_required;
    optionsString.value = q.options ? q.options.join(', ') : '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    // Process options if type is radio, checkbox, or select
    if (form.type === 'radio' || form.type === 'checkbox' || form.type === 'select') {
        const parsedOptions = optionsString.value.split(',').map(s => s.trim()).filter(s => s !== '');
        form.options = parsedOptions.length > 0 ? parsedOptions : null;
    } else {
        form.options = null;
    }

    if (modalMode.value === 'add') {
        form.post(route('admin.psb.form-builder.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('admin.psb.form-builder.update', form.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmState = ref({ show: false, id: null });

const deleteQuestion = (id) => {
    confirmState.value = { show: true, id };
};

const executeDelete = () => {
    router.delete(route('admin.psb.form-builder.destroy', confirmState.value.id));
    confirmState.value.show = false;
};

const getOptionsList = (form) => {
    if (!form.options) return '-';
    try {
        const opts = typeof form.options === 'string' ? JSON.parse(form.options) : form.options;
        return Array.isArray(opts) ? opts.join(', ') : '-';
    } catch (e) {
        return typeof form.options === 'string' ? form.options : '-';
    }
};

// Simple Up/Down reordering
const moveUp = (index) => {
    if (index === 0) return;
    const ids = props.questions.map(q => q.id);
    const temp = ids[index];
    ids[index] = ids[index - 1];
    ids[index - 1] = temp;
    saveReorder(ids);
};

const moveDown = (index) => {
    if (index === props.questions.length - 1) return;
    const ids = props.questions.map(q => q.id);
    const temp = ids[index];
    ids[index] = ids[index + 1];
    ids[index + 1] = temp;
    saveReorder(ids);
};

const saveReorder = (ids) => {
    router.post(route('admin.psb.form-builder.reorder'), { ordered_ids: ids }, { preserveScroll: true });
};
</script>

<template>
    <Head title="Form Builder PSB" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Form Builder <span class="text-[#d02e5c]">PSB</span>
            </h2>
        </template>

        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#161514] p-6 rounded-3xl border border-[#ebeae8] dark:border-[#3e3c3a] shadow-sm">
                <div>
                    <h3 class="text-lg font-black text-[#161514] dark:text-[#f2e8d5]">Konfigurasi Pertanyaan</h3>
                    <p class="text-xs font-medium text-[#a8a196] mt-1">Atur daftar pertanyaan dinamis untuk form registrasi Tahap 2.</p>
                </div>
                <RedwoodButton 
                    v-if="$page.props.auth.user.permissions.includes('create psb')"
                    @click="openAddModal"
                >
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Tambah Pertanyaan
                </RedwoodButton>
            </div>
            
            <div v-if="questions.length === 0" class="text-center py-16 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-3xl">
                <p class="text-[#a8a196] font-medium">Belum ada pertanyaan. Silakan tambah pertanyaan baru.</p>
            </div>

            <!-- List of Questions -->
            <div 
                v-for="(q, index) in questions" 
                :key="q.id" 
                class="flex items-center justify-between p-5 bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl shadow-sm hover:shadow-md transition-all group"
            >
                <!-- Left: Order Controls & Icon -->
                <div class="flex items-center gap-4">
                    <div class="flex flex-col text-[#a8a196] opacity-0 group-hover:opacity-100 transition-opacity" v-if="$page.props.auth.user.permissions.includes('create psb')">
                        <button @click="moveUp(index)" :disabled="index === 0" class="hover:text-[#d02e5c] disabled:opacity-20"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg></button>
                        <button @click="moveDown(index)" :disabled="index === questions.length - 1" class="hover:text-[#d02e5c] disabled:opacity-20"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span v-if="q.is_required" class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Wajib</span>
                            <span class="bg-[#f5f4f2] dark:bg-[#262524] text-[#a8a196] text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider border border-[#ebeae8] dark:border-[#3e3c3a]">{{ q.type }}</span>
                        </div>
                        <h4 class="text-lg font-bold text-[#161514] dark:text-[#f2e8d5] leading-tight">
                            {{ q.question_text }}
                        </h4>
                        <p v-if="q.options && q.options.length" class="text-sm text-[#a8a196] mt-1 font-medium italic">
                            Opsi: {{ q.options.join(', ') }}
                        </p>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2">
                    <button 
                        v-if="$page.props.auth.user.permissions.includes('update psb')"
                        @click="openEditModal(q)" 
                        class="p-2 text-[#a8a196] hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-xl transition-colors" 
                        title="Edit"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    <button 
                        v-if="$page.props.auth.user.permissions.includes('delete psb')"
                        @click="deleteQuestion(q.id)" 
                        class="p-2 text-[#a8a196] hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors" 
                        title="Hapus"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" aria-hidden="true" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#161514] rounded-2xl shadow-xl shadow-black/20 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl font-black text-[#161514] dark:text-[#f2e8d5]" id="modal-title">
                                {{ modalMode === 'add' ? 'Tambah Pertanyaan' : 'Edit Pertanyaan' }}
                            </h3>
                            <div class="mt-6 space-y-4">
                                <!-- Form Fields -->
                                <div>
                                    <label class="block text-sm font-bold text-[#161514] dark:text-[#f2e8d5] mb-2">Teks Pertanyaan (Label)</label>
                                    <input v-model="form.question_text" type="text" class="w-full px-4 py-3 bg-[#f5f4f2] dark:bg-[#1a1918] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-[#161514] dark:text-[#f2e8d5] font-medium" placeholder="Misal: Nama Ibu Kandung" required>
                                    <p v-if="form.errors.question_text" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.question_text }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-[#161514] dark:text-[#f2e8d5] mb-2">Tipe Input</label>
                                        <select v-model="form.type" class="w-full px-4 py-3 bg-[#f5f4f2] dark:bg-[#1a1918] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-[#161514] dark:text-[#f2e8d5] font-medium appearance-none cursor-pointer">
                                            <option value="text">Teks Singkat</option>
                                            <option value="textarea">Paragraf Panjang</option>
                                            <option value="radio">Pilihan Ganda (Satu)</option>
                                            <option value="checkbox">Checkbox (Banyak)</option>
                                            <option value="select">Dropdown (Pilih Satu)</option>
                                            <option value="file">Upload File</option>
                                            <option value="date">Tanggal</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-[#161514] dark:text-[#f2e8d5] mb-2">Wajib Diisi?</label>
                                        <select v-model="form.is_required" class="w-full px-4 py-3 bg-[#f5f4f2] dark:bg-[#1a1918] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl text-[#161514] dark:text-[#f2e8d5] font-medium appearance-none cursor-pointer">
                                            <option :value="true">Ya, Wajib</option>
                                            <option :value="false">Tidak (Opsional)</option>
                                        </select>
                                    </div>
                                </div>

                                <div v-if="form.type === 'radio' || form.type === 'checkbox' || form.type === 'select'" class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-200 dark:border-amber-900/50">
                                    <label class="block text-sm font-bold text-amber-900 dark:text-amber-500 mb-2">Pilihan (Pisahkan dengan Koma)</label>
                                    <input v-model="optionsString" type="text" class="w-full px-4 py-3 bg-white dark:bg-[#1a1918] border border-amber-200 dark:border-amber-900/50 rounded-xl text-[#161514] dark:text-[#f2e8d5] font-medium focus:ring-amber-500 focus:border-amber-500" placeholder="SMP, SMA, SMK, Lulusan Lain">
                                    <p class="text-xs text-amber-700 mt-2">Contoh pengisian: SMP, SMA, Lulusan Pondok</p>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="mt-8 sm:mt-8 sm:flex sm:flex-row-reverse gap-3">
                        <RedwoodButton @click="submitForm" :disabled="form.processing" class="w-full sm:w-auto">
                            Simpan Pertanyaan
                        </RedwoodButton>
                        <RedwoodButton variant="outline" @click="closeModal" class="w-full sm:w-auto mt-3 sm:mt-0">
                            Batal
                        </RedwoodButton>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>

    <ConfirmModal
        :show="confirmState.show"
        title="Hapus Pertanyaan"
        message="Pertanyaan ini akan dihapus permanen beserta seluruh jawaban yang sudah ada."
        confirm-text="Ya, Hapus"
        @confirm="executeDelete"
        @cancel="confirmState.show = false"
    />
</template>
