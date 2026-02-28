<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { ref } from 'vue';
import { notify } from '@/Utils/toast';

const props = defineProps({
    registrations: Object
});

const isModalOpen = ref(false);
const selectedRegistration = ref(null);

const openResponsesModal = (reg) => {
    selectedRegistration.value = reg;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => { selectedRegistration.value = null; }, 300);
};

const statusForm = useForm({
    status_seleksi: ''
});

const updateStatus = (id, status) => {
    statusForm.status_seleksi = status;
    statusForm.patch(route('admin.psb.status', id), {
        preserveScroll: true
    });
};

const copyLink = async (token) => {
    const url = window.location.origin + '/psb/lengkapi-data/' + token;
    
    try {
        // Try modern clipboard API
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(url);
            notify('Link formulir tahap 2 berhasil disalin!');
        } else {
            throw new Error('Clipboard API unavailable');
        }
    } catch (err) {
        // Fallback for non-secure contexts or older browsers
        const textArea = document.createElement("textarea");
        textArea.value = url;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            notify('Link biodata berhasil disalin (fallback mode)');
        } catch (fallbackErr) {
            notify('Gagal menyalin link secara otomatis', 'error');
        }
        document.body.removeChild(textArea);
    }
};
</script>

<template>
    <Head title="Manajemen Registrasi PSB" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Penerimaan <span class="text-[#c97e60]">Santri Baru</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Table Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50/50 dark:bg-[#1a1918]/50">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Tgl Daftar</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Calon Santri</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Wali / Kontak</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#a8a196] uppercase tracking-wider">Referensi</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Berkas</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Status Seleksi</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                            <tr v-for="reg in registrations.data" :key="reg.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                <td class="px-8 py-5">
                                    <p class="text-xs font-medium">{{ new Date(reg.created_at).toLocaleDateString('id-ID') }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <div>
                                        <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ reg.nama_calon }}</p>
                                        <p class="text-[10px] text-[#a8a196]">{{ reg.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-medium">{{ reg.nama_wali }}</p>
                                    <p class="text-xs text-[#c97e60] font-bold">{{ reg.no_hp_wali }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="reg.referrer" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#c97e60]/10 text-[#c97e60] text-xs font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                        {{ reg.referrer.name }}
                                    </div>
                                    <span v-else class="text-sm text-[#a8a196]">-</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex gap-2">
                                        <a v-if="reg.berkas_kk" :href="'/storage/' + reg.berkas_kk" target="_blank" class="w-8 h-8 rounded-lg bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-[#a8a196] hover:text-[#c97e60] transition-colors border border-transparent hover:border-[#c97e60]/20" title="KK">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </a>
                                        <a v-if="reg.berkas_akta" :href="'/storage/' + reg.berkas_akta" target="_blank" class="w-8 h-8 rounded-lg bg-[#f5f4f2] dark:bg-[#262524] flex items-center justify-center text-[#a8a196] hover:text-[#c97e60] transition-colors border border-transparent hover:border-[#c97e60]/20" title="Akta">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </a>
                                    </div>
                                </td>
                        <td class="px-8 py-5">
                            <span 
                                :class="[
                                    'px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-wider',
                                    reg.status_seleksi === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                    reg.status_seleksi === 'diterima' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                    reg.status_seleksi === 'cadangan' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                                    'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
                                ]"
                            >
                                {{ reg.status_seleksi }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2 flex-wrap max-w-[250px]">
                                <RedwoodButton 
                                    variant="outline"
                                    v-if="reg.token"
                                    @click="copyLink(reg.token)"
                                    class="!text-blue-500 hover:bg-blue-500/10 border-blue-200"
                                    size="sm"
                                    title="Salin Link Biodata (Tahap 2)"
                                >
                                    <template #icon>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                    </template>
                                    Copy Link
                                </RedwoodButton>

                                <RedwoodButton 
                                    variant="outline"
                                    v-if="reg.responses && reg.responses.length > 0"
                                    @click="openResponsesModal(reg)"
                                    class="!text-indigo-500 hover:bg-indigo-500/10 border-indigo-200"
                                    size="sm"
                                    title="Lihat Berkas Lengkap"
                                >
                                    <template #icon>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </template>
                                    Berkas
                                </RedwoodButton>

                                <RedwoodButton 
                                    variant="ghost" 
                                    @click="updateStatus(reg.id, 'diterima')"
                                    class="!text-emerald-500 hover:bg-emerald-500/10"
                                    size="sm"
                                >
                                    <template #icon>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </template>
                                    Terima
                                </RedwoodButton>
                                <RedwoodButton 
                                    variant="ghost" 
                                    @click="updateStatus(reg.id, 'ditolak')"
                                    class="!text-rose-500 hover:bg-rose-500/10"
                                    size="sm"
                                >
                                    <template #icon>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </template>
                                    Tolak
                                </RedwoodButton>
                            </div>
                        </td>
                    </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-8 py-6 border-t border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between bg-gray-50/30 dark:bg-[#1a1918]/30">
                    <p class="text-xs text-[#a8a196]">Menampilkan {{ registrations.total }} pendaftar</p>
                    <div class="flex gap-2">
                         <!-- Pagination links here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- View Responses Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" aria-hidden="true" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#161514] rounded-2xl shadow-xl shadow-black/20 sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8 border border-[#ebeae8] dark:border-[#3e3c3a]">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5] border-b border-[#ebeae8] dark:border-[#3e3c3a] pb-4 mb-6" id="modal-title">
                                Berkas Profil Lengkap: <span class="text-[#c97e60]">{{ selectedRegistration?.nama_calon }}</span>
                            </h3>
                            <div class="mt-2 space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                                <div v-for="response in selectedRegistration?.responses" :key="response.id" class="mb-4">
                                    <h4 class="text-sm font-bold text-[#a8a196] uppercase tracking-wider">{{ response.question?.question_text }}</h4>
                                    
                                    <!-- Display File Link if it's a file -->
                                    <div v-if="response.question?.type === 'file' && response.response_file_path" class="mt-1">
                                        <a :href="'/storage/' + response.response_file_path" target="_blank" class="inline-flex items-center gap-2 text-[#c97e60] hover:text-[#a8654b] font-bold text-sm bg-[#c97e60]/10 px-3 py-1.5 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            Lihat File Unduhan
                                        </a>
                                    </div>
                                    
                                    <!-- Display Text Response -->
                                    <div v-else class="mt-1 text-base font-semibold text-[#161514] dark:text-[#f2e8d5]">
                                        {{ response.response_text || '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 sm:flex justify-end gap-3 border-t border-[#ebeae8] dark:border-[#3e3c3a] pt-6">
                        <RedwoodButton @click="closeModal" class="w-full sm:w-auto">Tutup Berkas</RedwoodButton>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
