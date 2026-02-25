<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { ref } from 'vue';

const form = useForm({
    nama_calon: '',
    jenis_kelamin: '',
    nama_wali: '',
    no_hp_wali: '',
    berkas_kk: null,
    berkas_akta: null,
    berkas_ijazah: null,
});

const submit = () => {
    form.post(route('psb.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Penerimaan Santri Baru" />

    <div class="min-h-screen bg-[#f5f4f2] text-[#161514] selection:bg-[#c97e60]/30 py-12 px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#c97e60] rounded-2xl shadow-xl shadow-[#c97e60]/20 mb-6 group transition-all hover:rotate-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4">Penerimaan <span class="text-[#c97e60]">Santri Baru</span></h1>
                <p class="text-[#a8a196] font-medium max-w-lg mx-auto">Bergabunglah bersama kami untuk mencetak generasi Qur'ani yang cerdas dan mandiri.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-12">
                <!-- Data Calon Santri -->
                <div class="bg-white shadow-[0_30px_60px_rgba(0,0,0,0.05)] rounded-[40px] border border-[#ebeae8] p-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-12 h-12 bg-[#c97e60]/10 rounded-2xl flex items-center justify-center text-[#c97e60]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <h3 class="text-2xl font-bold tracking-tight">Data Calon Santri</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Lengkap Calon Santri</label>
                            <input v-model="form.nama_calon" type="text" placeholder="Masukkan nama sesuai akta" class="w-full bg-[#fcf8f5] border-[#ebeae8] rounded-2xl p-5 focus:ring-4 focus:ring-[#c97e60]/10 focus:border-[#c97e60] transition-all outline-none" required>
                            <div v-if="form.errors.nama_calon" class="text-xs text-rose-500 font-medium">{{ form.errors.nama_calon }}</div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jenis Kelamin</label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" v-model="form.jenis_kelamin" value="L" class="hidden peer">
                                    <div class="p-5 text-center bg-[#fcf8f5] border-2 border-transparent rounded-2xl transition-all peer-checked:border-[#c97e60] peer-checked:bg-white peer-checked:shadow-lg font-bold text-[#a8a196] peer-checked:text-[#161514]">Laki-laki</div>
                                </label>
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" v-model="form.jenis_kelamin" value="P" class="hidden peer">
                                    <div class="p-5 text-center bg-[#fcf8f5] border-2 border-transparent rounded-2xl transition-all peer-checked:border-[#c97e60] peer-checked:bg-white peer-checked:shadow-lg font-bold text-[#a8a196] peer-checked:text-[#161514]">Perempuan</div>
                                </label>
                            </div>
                            <div v-if="form.errors.jenis_kelamin" class="text-xs text-rose-500 font-medium">{{ form.errors.jenis_kelamin }}</div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Wali / Orang Tua</label>
                            <input v-model="form.nama_wali" type="text" placeholder="Nama ayah/ibu/wali" class="w-full bg-[#fcf8f5] border-[#ebeae8] rounded-2xl p-5 focus:ring-4 focus:ring-[#c97e60]/10 focus:border-[#c97e60] transition-all outline-none" required>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nomor WhatsApp Wali</label>
                            <input v-model="form.no_hp_wali" type="text" placeholder="Contoh: 0812..." class="w-full bg-[#fcf8f5] border-[#ebeae8] rounded-2xl p-5 focus:ring-4 focus:ring-[#c97e60]/10 focus:border-[#c97e60] transition-all outline-none" required>
                        </div>
                    </div>
                </div>

                <!-- Berkas Digital -->
                <div class="bg-white shadow-[0_30px_60px_rgba(0,0,0,0.05)] rounded-[40px] border border-[#ebeae8] p-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-12 h-12 bg-[#c97e60]/10 rounded-2xl flex items-center justify-center text-[#c97e60]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h1" /></svg>
                        </div>
                        <h3 class="text-2xl font-bold tracking-tight">Berkas Digital</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Kartu Keluarga (KK)</label>
                            <input type="file" @input="form.berkas_kk = $event.target.files[0]" class="block w-full text-sm text-[#a8a196] file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#c97e60]/10 file:text-[#c97e60] hover:file:bg-[#c97e60]/20 transition-all" accept=".pdf,.jpg,.jpeg,.png">
                            <p class="text-[9px] text-[#a8a196] italic">Format: PDF/JPG, Max 2MB</p>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Akta Kelahiran</label>
                            <input type="file" @input="form.berkas_akta = $event.target.files[0]" class="block w-full text-sm text-[#a8a196] file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#c97e60]/10 file:text-[#c97e60] hover:file:bg-[#c97e60]/20 transition-all" accept=".pdf,.jpg,.jpeg,.png">
                            <p class="text-[9px] text-[#a8a196] italic">Format: PDF/JPG, Max 2MB</p>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Ijazah Terakhir</label>
                            <input type="file" @input="form.berkas_ijazah = $event.target.files[0]" class="block w-full text-sm text-[#a8a196] file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#c97e60]/10 file:text-[#c97e60] hover:file:bg-[#c97e60]/20 transition-all" accept=".pdf,.jpg,.jpeg,.png">
                            <p class="text-[9px] text-[#a8a196] italic">Format: PDF/JPG, Max 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Footer / Submit -->
                <div class="text-center">
                    <RedwoodButton 
                        type="submit" 
                        variant="primary"
                        size="lg"
                        :disabled="form.processing"
                        class="px-20 py-8 rounded-full"
                    >
                        <template #icon>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </template>
                        {{ form.processing ? 'Sedang Mengirim...' : 'Kirim Pendaftaran' }}
                    </RedwoodButton>
                    <p class="mt-8 text-xs text-[#a8a196] font-medium italic">Dengan mengirimkan form ini, Anda menyetujui ketentuan pendaftaran pesantren kami.</p>
                </div>
            </form>
        </div>
    </div>
</template>
