<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    walis: Array,
    kelas: Array,
    kamars: Array
});

const form = useForm({
    nama: '',
    nis: '',
    jenis_kelamin: '',
    wali_id: '',
    status: 'Santri Aktif',
    kelas: '',
    kamar: '',
    entitas: '',
    angkatan: '',
});

const submit = () => {
    form.post(route('santri.store'));
};
</script>

<template>
    <Head title="Tambah Santri" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Tambah <span class="text-[#c97e60]">Santri</span>
            </h2>
        </template>

        <div class="max-w-4xl mx-auto">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Data Pribadi -->
                <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-[#c97e60]/10 rounded-2xl flex items-center justify-center text-[#c97e60]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight">Data Pribadi</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Lengkap</label>
                            <input v-model="form.nama" type="text" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all" required>
                            <div v-if="form.errors.nama" class="text-xs text-rose-500 font-medium">{{ form.errors.nama }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">NIS (Nomor Induk Santri)</label>
                            <input v-model="form.nis" type="text" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all">
                            <div v-if="form.errors.nis" class="text-xs text-rose-500 font-medium">{{ form.errors.nis }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jenis Kelamin</label>
                            <select v-model="form.jenis_kelamin" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all" required>
                                <option value="" disabled>Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <div v-if="form.errors.jenis_kelamin" class="text-xs text-rose-500 font-medium">{{ form.errors.jenis_kelamin }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Pilih Wali Santri</label>
                            <select v-model="form.wali_id" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all">
                                <option value="" disabled>Pilih Wali</option>
                                <option v-for="wali in walis" :key="wali.id" :value="wali.id">{{ wali.nama }} ({{ wali.hubungan }})</option>
                            </select>
                            <div v-if="form.errors.wali_id" class="text-xs text-rose-500 font-medium">{{ form.errors.wali_id }}</div>
                        </div>
                    </div>
                </div>

                <!-- Penempatan & Status -->
                <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-[#c97e60]/10 rounded-2xl flex items-center justify-center text-[#c97e60]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight">Penempatan & Status</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Status Aktif</label>
                            <select v-model="form.status" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all" required>
                                <option value="Santri Aktif">Santri Aktif</option>
                                <option value="Santri Keluar">Santri Keluar</option>
                                <option value="Santri Lulus - Alumni">Santri Lulus - Alumni</option>
                                <option value="Alumni Aktif - dipondok">Alumni Aktif - dipondok</option>
                                <option value="Alumni Tidak Aktif - diluar pondok">Alumni Tidak Aktif - diluar pondok</option>
                                <option value="Santri Magang">Santri Magang</option>
                                <option value="Alumni Magang">Alumni Magang</option>
                            </select>
                            <div v-if="form.errors.status" class="text-xs text-rose-500 font-medium">{{ form.errors.status }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Kelas</label>
                            <select v-model="form.kelas" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all font-bold">
                                <option value="">Pilih Kelas</option>
                                <option v-for="k in kelas" :key="k.id" :value="k.nama">{{ k.nama }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Kamar</label>
                            <select v-model="form.kamar" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all font-bold">
                                <option value="">Pilih Kamar</option>
                                <option v-for="k in kamars" :key="k.id" :value="k.nama">{{ k.nama }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Entitas (Contoh: PIT, SIT)</label>
                            <input v-model="form.entitas" type="text" list="entitas_options" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all font-bold" placeholder="Ketik atau pilih...">
                            <datalist id="entitas_options">
                                <option value="PIT"></option>
                                <option value="RIT"></option>
                                <option value="SIT"></option>
                            </datalist>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Angkatan (Otomatis jika kosong)</label>
                            <input v-model="form.angkatan" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#c97e60] focus:border-transparent transition-all font-bold" placeholder="Contoh: 23">
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-4 p-4">
                    <RedwoodButton 
                        as="Link"
                        :href="route('santri.index')"
                        variant="outline"
                    >
                        <template #icon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        </template>
                        Batal
                    </RedwoodButton>
                    <RedwoodButton 
                        type="submit" 
                        variant="primary"
                        :disabled="form.processing"
                    >
                        <template #icon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Data Santri' }}
                    </RedwoodButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
