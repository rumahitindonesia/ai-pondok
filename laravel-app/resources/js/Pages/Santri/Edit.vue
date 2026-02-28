<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    santri: Object,
    walis: Array,
    kelas: Array,
    kamars: Array
});

const form = useForm({
    nama: props.santri.nama,
    nis: props.santri.nis,
    jenis_kelamin: props.santri.jenis_kelamin,
    wali_id: props.santri.wali_id,
    status: props.santri.status,
    kelas: props.santri.kelas,
    kamar: props.santri.kamar,
    entitas: props.santri.entitas,
    angkatan: props.santri.angkatan,
    bio: props.santri.bio || '',
    is_portfolio_featured: props.santri.is_portfolio_featured || false,
    foto: null,
});

const photoPreview = ref(props.santri.foto ? `/storage/${props.santri.foto}` : null);

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.foto = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    // Inertia PUT doesn't support files well in some versions/configs because it uses X-HTTP-Method-Override
    // but the most reliable way for multipart updates in Laravel is POST with _method spoofing.
    router.post(route('santri.update', props.santri.id), {
        _method: 'put',
        ...form.data()
    }, {
        onSuccess: () => {
            // Success handling
        }
    });
};
</script>

<template>
    <Head title="Edit Santri" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Edit <span class="text-[#d02e5c]">Santri</span>
            </h2>
        </template>

        <div class="max-w-4xl mx-auto">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Data Pribadi -->
                <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-[#d02e5c]/10 rounded-2xl flex items-center justify-center text-[#d02e5c]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight">Data Pribadi & Profil</h3>
                    </div>

                    <!-- Photo Upload -->
                    <div class="flex flex-col items-center mb-10">
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-full border-4 border-[#fcf8f5] dark:border-[#1a1918] overflow-hidden bg-gray-100 dark:bg-[#262524] shadow-xl transition-all duration-500 group-hover:scale-105 group-hover:rotate-2">
                                <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center text-[#a8a196]">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                            </div>
                            <label class="absolute bottom-0 right-0 bg-[#d02e5c] text-white p-2.5 rounded-2xl shadow-lg cursor-pointer hover:scale-110 active:scale-95 transition-all duration-300">
                                <input type="file" class="hidden" @change="handlePhotoChange" accept="image/*">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </label>
                        </div>
                        <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Ubah Foto Profil</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Nama Lengkap</label>
                            <input v-model="form.nama" type="text" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all" required>
                            <div v-if="form.errors.nama" class="text-xs text-rose-500 font-medium">{{ form.errors.nama }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">NIS (Nomor Induk Santri)</label>
                            <input v-model="form.nis" type="text" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all">
                            <div v-if="form.errors.nis" class="text-xs text-rose-500 font-medium">{{ form.errors.nis }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Jenis Kelamin</label>
                            <select v-model="form.jenis_kelamin" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all" required>
                                <option value="" disabled>Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <div v-if="form.errors.jenis_kelamin" class="text-xs text-rose-500 font-medium">{{ form.errors.jenis_kelamin }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Pilih Wali Santri</label>
                            <select v-model="form.wali_id" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all">
                                <option value="" disabled>Pilih Wali</option>
                                <option v-for="wali in walis" :key="wali.id" :value="wali.id">{{ wali.nama }} ({{ wali.hubungan }})</option>
                            </select>
                            <div v-if="form.errors.wali_id" class="text-xs text-rose-500 font-medium">{{ form.errors.wali_id }}</div>
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">About Me (Biografi)</label>
                            <textarea 
                                v-model="form.bio" 
                                rows="4" 
                                class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all resize-none font-medium text-sm"
                                placeholder="Ceritakan sedikit tentang santri ini..."
                            ></textarea>
                            <div v-if="form.errors.bio" class="text-xs text-rose-500 font-medium">{{ form.errors.bio }}</div>
                        </div>
                    </div>
                </div>

                <!-- Penempatan & Status -->
                <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-[#d02e5c]/10 rounded-2xl flex items-center justify-center text-[#d02e5c]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight">Penempatan & Status</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Status Aktif</label>
                            <select v-model="form.status" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all" required>
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
                            <select v-model="form.kelas" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all font-bold">
                                <option value="">Pilih Kelas</option>
                                <option v-for="k in kelas" :key="k.id" :value="k.nama">{{ k.nama }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Kamar</label>
                            <select v-model="form.kamar" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all font-bold">
                                <option value="">Pilih Kamar</option>
                                <option v-for="k in kamars" :key="k.id" :value="k.nama">{{ k.nama }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Entitas (Contoh: PIT, SIT)</label>
                            <input v-model="form.entitas" type="text" list="entitas_options" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all font-bold" placeholder="Ketik atau pilih...">
                            <datalist id="entitas_options">
                                <option value="PIT"></option>
                                <option value="RIT"></option>
                                <option value="SIT"></option>
                            </datalist>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Angkatan (Otomatis jika kosong)</label>
                            <input v-model="form.angkatan" type="number" class="w-full bg-[#fcf8f5] dark:bg-[#1a1918] border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-4 focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all font-bold" placeholder="Contoh: 23">
                        </div>

                        <div class="md:col-span-3 pt-6 border-t border-[#ebeae8] dark:border-[#3e3c3a]">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" v-model="form.is_portfolio_featured" class="sr-only">
                                    <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 rounded-full transition-colors duration-200 ease-in-out group-hover:bg-gray-300 dark:group-hover:bg-gray-600 peer-focus:ring-2 peer-focus:ring-[#d02e5c] peer-focus:ring-offset-2" :class="{'!bg-[#d02e5c]': form.is_portfolio_featured}"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 ease-in-out shadow-sm" :class="{'translate-x-5': form.is_portfolio_featured}"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5]">Tampilkan di Homepage (Portofolio Sukses)</p>
                                    <p class="text-xs text-[#a8a196]">Santri ini akan muncul di bagian Portofolio Santri di halaman depan.</p>
                                </div>
                            </label>
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
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </template>
                        {{ form.processing ? 'Menyimpan...' : 'Perbarui Data' }}
                    </RedwoodButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
