<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    requester_name: props.user.name || '',
    phone_number: props.user.phone || '',
    format: '',
    deadline_date: '',
    deadline_time: '',
    size: '',
    output_format: '',
    include_logo: false,
    include_website: false,
    include_social_media: false,
    include_phone: false,
    content_description: '',
    special_notes: '',
    reference_link: '',
});

const submit = () => {
    form.post(route('content-requests.store'));
};
</script>

<template>
    <Head title="Request Konten Media" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-[#1e1d1b] overflow-hidden shadow-sm sm:rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-[#d02e5c] to-[#a32448] p-8 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <h2 class="text-3xl font-extrabold tracking-tight">Form Pengajuan Konten</h2>
                            <p class="mt-2 text-rose-100 text-lg">Platform request desain & video ke tim Media Rumah IT.</p>
                        </div>
                        <div class="absolute -right-10 -top-10 text-white/10">
                            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        
                        <!-- Identitas Pengaju -->
                        <div>
                            <h3 class="text-lg font-bold text-[#161514] dark:text-[#f2e8d5] mb-4 flex items-center">
                                <span class="bg-rose-100 dark:bg-rose-900/40 text-[#d02e5c] dark:text-rose-400 w-8 h-8 rounded-full flex items-center justify-center mr-3 font-bold text-sm">1</span>
                                Identitas Pengaju
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-[#fcfcfc] dark:bg-[#1a1918] p-6 rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                <div>
                                    <InputLabel for="requester_name" value="Nama Pengaju" class="dark:text-[#f2e8d5]" />
                                    <TextInput
                                        id="requester_name"
                                        type="text"
                                        class="mt-1 block w-full dark:bg-[#1e1d1b] dark:border-[#3e3c3a] dark:text-[#f2e8d5]"
                                        v-model="form.requester_name"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.requester_name" />
                                </div>
                                <div>
                                    <InputLabel value="Divisi / Amanah" class="dark:text-[#f2e8d5]" />
                                    <div class="mt-1 block w-full px-4 py-2 bg-gray-100 dark:bg-[#2d2c2a] border border-gray-300 dark:border-[#4e4d4a] rounded-md shadow-sm text-gray-600 dark:text-gray-400 cursor-not-allowed">
                                        {{ user.jabatan ? user.jabatan.nama : 'Santri' }}
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Terisi otomatis sesuai akun Anda.</p>
                                </div>
                                <div>
                                    <InputLabel for="phone_number" value="Nomor WA (Untuk dihubungi tim produksi)" class="dark:text-[#f2e8d5]" />
                                    <TextInput
                                        id="phone_number"
                                        type="text"
                                        class="mt-1 block w-full dark:bg-[#1e1d1b] dark:border-[#3e3c3a] dark:text-[#f2e8d5]"
                                        v-model="form.phone_number"
                                        required
                                        placeholder="081234567890"
                                    />
                                    <InputError class="mt-2" :message="form.errors.phone_number" />
                                </div>
                            </div>
                        </div>

                        <!-- Detail Permintaan -->
                        <div>
                            <h3 class="text-lg font-bold text-[#161514] dark:text-[#f2e8d5] mb-4 flex items-center">
                                <span class="bg-rose-100 dark:bg-rose-900/40 text-[#d02e5c] dark:text-rose-400 w-8 h-8 rounded-full flex items-center justify-center mr-3 font-bold text-sm">2</span>
                                Format & Tenggat Waktu
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-[#fcfcfc] dark:bg-[#1a1918] p-6 rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                <div>
                                    <InputLabel for="format" value="Format Pengajuan" class="dark:text-[#f2e8d5]" />
                                    <select 
                                        id="format" 
                                        v-model="form.format" 
                                        required
                                        class="mt-1 block w-full border-[#d1cdca] dark:border-[#4e4a46] bg-white dark:bg-[#1a1816] dark:text-[#f2e8d5] focus:border-[#d02e5c] dark:focus:border-[#e84976] focus:ring-[#d02e5c] dark:focus:ring-[#e84976] rounded-md shadow-sm"
                                    >
                                        <option value="" disabled>-- Pilih Salah Satu --</option>
                                        <option value="Video Editor">Video Editor</option>
                                        <option value="Design">Design</option>
                                        <option value="Hanya Take Video Mentahan">Hanya Take Video Mentahan</option>
                                        <option value="Foto">Foto</option>
                                        <option value="Lainnya">Yang Lain</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.format" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="deadline_date" value="Tanggal Deadline" class="dark:text-[#f2e8d5]" />
                                        <input
                                            id="deadline_date"
                                            type="date"
                                            class="mt-1 block w-full border-[#d1cdca] dark:border-[#4e4a46] bg-white dark:bg-[#1a1816] dark:text-[#f2e8d5] focus:border-[#d02e5c] rounded-md shadow-sm text-sm"
                                            v-model="form.deadline_date"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.deadline_date" />
                                    </div>
                                    <div>
                                        <InputLabel for="deadline_time" value="Jam Deadline" class="dark:text-[#f2e8d5]" />
                                        <input
                                            id="deadline_time"
                                            type="time"
                                            class="mt-1 block w-full border-[#d1cdca] dark:border-[#4e4a46] bg-white dark:bg-[#1a1816] dark:text-[#f2e8d5] focus:border-[#d02e5c] rounded-md shadow-sm text-sm"
                                            v-model="form.deadline_time"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.deadline_time" />
                                    </div>
                                </div>
                                <div>
                                    <InputLabel for="size" value="Ukuran Konten" class="dark:text-[#f2e8d5]" />
                                    <select 
                                        id="size" 
                                        v-model="form.size" 
                                        required
                                        class="mt-1 block w-full border-[#d1cdca] dark:border-[#4e4a46] bg-white dark:bg-[#1a1816] dark:text-[#f2e8d5] focus:border-[#d02e5c] rounded-md shadow-sm"
                                    >
                                        <option value="" disabled>-- Pilih Salah Satu --</option>
                                        <option value="Potrait 9:16">Potrait 9:16 (Story/Tiktok/Reels)</option>
                                        <option value="Landscape 16:9">Landscape 16:9 (YouTube)</option>
                                        <option value="Square 1:1">Square 1:1 (Feed IG)</option>
                                        <option value="Lainnya">Yang Lain</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.size" />
                                </div>
                                <div>
                                    <InputLabel for="output_format" value="Format Output" class="dark:text-[#f2e8d5]" />
                                    <select 
                                        id="output_format" 
                                        v-model="form.output_format" 
                                        required
                                        class="mt-1 block w-full border-[#d1cdca] dark:border-[#4e4a46] bg-white dark:bg-[#1a1816] dark:text-[#f2e8d5] focus:border-[#d02e5c] rounded-md shadow-sm"
                                    >
                                        <option value="" disabled>-- Pilih Salah Satu --</option>
                                        <option value="JPG/PNG (Design)">JPG / PNG (Design)</option>
                                        <option value="MP4 (Video)">MP4 (Video)</option>
                                        <option value="Lainnya">Yang Lain</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.output_format" />
                                </div>
                            </div>
                        </div>

                        <!-- Spesifikasi Konten -->
                        <div>
                            <h3 class="text-lg font-bold text-[#161514] dark:text-[#f2e8d5] mb-4 flex items-center">
                                <span class="bg-rose-100 dark:bg-rose-900/40 text-[#d02e5c] dark:text-rose-400 w-8 h-8 rounded-full flex items-center justify-center mr-3 font-bold text-sm">3</span>
                                Isi Konten & Spesifikasi
                            </h3>
                            <div class="space-y-6 bg-[#fcfcfc] dark:bg-[#1a1918] p-6 rounded-xl border border-[#ebeae8] dark:border-[#3e3c3a]">
                                
                                <div>
                                    <InputLabel value="Yang ingin dicantumkan di konten" class="dark:text-[#f2e8d5] mb-2" />
                                    <div class="flex flex-wrap gap-4">
                                        <label class="flex items-center text-sm dark:text-[#f2e8d5] cursor-pointer">
                                            <input type="checkbox" v-model="form.include_logo" class="rounded text-[#d02e5c] shadow-sm focus:ring-[#d02e5c] dark:bg-[#1e1d1b] dark:border-[#3e3c3a] mr-2"> 
                                            Logo
                                        </label>
                                        <label class="flex items-center text-sm dark:text-[#f2e8d5] cursor-pointer">
                                            <input type="checkbox" v-model="form.include_website" class="rounded text-[#d02e5c] shadow-sm focus:ring-[#d02e5c] dark:bg-[#1e1d1b] dark:border-[#3e3c3a] mr-2"> 
                                            Website
                                        </label>
                                        <label class="flex items-center text-sm dark:text-[#f2e8d5] cursor-pointer">
                                            <input type="checkbox" v-model="form.include_social_media" class="rounded text-[#d02e5c] shadow-sm focus:ring-[#d02e5c] dark:bg-[#1e1d1b] dark:border-[#3e3c3a] mr-2"> 
                                            Sosial Media
                                        </label>
                                        <label class="flex items-center text-sm dark:text-[#f2e8d5] cursor-pointer">
                                            <input type="checkbox" v-model="form.include_phone" class="rounded text-[#d02e5c] shadow-sm focus:ring-[#d02e5c] dark:bg-[#1e1d1b] dark:border-[#3e3c3a] mr-2"> 
                                            Nomor WhatsApp
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="content_description" value="Isi Konten (Wajib)" class="dark:text-[#f2e8d5]" />
                                    <textarea
                                        id="content_description"
                                        rows="4"
                                        class="mt-1 block w-full border-[#d1cdca] dark:border-[#4e4a46] bg-white dark:bg-[#1a1816] dark:text-[#f2e8d5] focus:border-[#d02e5c] rounded-md shadow-sm"
                                        v-model="form.content_description"
                                        required
                                        placeholder="Jelaskan secara detail isi konten yang Anda butuhkan..."
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.content_description" />
                                </div>

                                <div>
                                    <InputLabel for="special_notes" value="Catatan Khusus (Semisal: durasi video, dll)" class="dark:text-[#f2e8d5]" />
                                    <textarea
                                        id="special_notes"
                                        rows="2"
                                        class="mt-1 block w-full border-[#d1cdca] dark:border-[#4e4a46] bg-white dark:bg-[#1a1816] dark:text-[#f2e8d5] focus:border-[#d02e5c] rounded-md shadow-sm"
                                        v-model="form.special_notes"
                                        placeholder="Catatan tambahan (opsional)"
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.special_notes" />
                                </div>

                                <div>
                                    <InputLabel for="reference_link" value="Link Referensi Design / Video (Opsional)" class="dark:text-[#f2e8d5]" />
                                    <TextInput
                                        id="reference_link"
                                        type="url"
                                        class="mt-1 block w-full dark:bg-[#1e1d1b] dark:border-[#3e3c3a] dark:text-[#f2e8d5]"
                                        v-model="form.reference_link"
                                        placeholder="https://..."
                                    />
                                    <InputError class="mt-2" :message="form.errors.reference_link" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end border-t border-[#ebeae8] dark:border-[#3e3c3a] pt-6 mt-8">
                            <span class="text-sm text-gray-500 dark:text-gray-400 mr-4">Pastikan data yang diisi sudah benar.</span>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="inline-flex items-center px-6 py-3 bg-[#d02e5c] border border-transparent rounded-full font-bold text-sm text-white tracking-widest hover:bg-[#b0224a] focus:bg-[#b0224a] active:bg-[#911a3b] focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:ring-offset-2 dark:focus:ring-offset-[#161514] transition ease-in-out duration-150 shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Kirim Pengajuan Konten
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
