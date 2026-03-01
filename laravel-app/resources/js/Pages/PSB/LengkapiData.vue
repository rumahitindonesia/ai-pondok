<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    registration: Object,
    questions: Array
});

// Build initial form state dynamically
const initialFormState = {};
props.questions.forEach(q => {
    initialFormState['question_' + q.id] = q.type === 'checkbox' ? [] : null;
});

const form = useForm(initialFormState);

const handleFileUpload = (e, fieldName) => {
    form[fieldName] = e.target.files[0];
};

const submit = () => {
    form.post(route('psb.update_lengkapi_data', props.registration.token), {
        preserveScroll: true,
        forceFormData: true,
    });
};

const provinces = [
    'Nanggroe Aceh Darussalam', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 
    'Jambi', 'Bengkulu', 'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Lampung', 
    'Banten', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 
    'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah', 
    'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara', 'Gorontalo', 
    'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Maluku', 
    'Maluku Utara', 'Papua Barat', 'Papua', 'Papua Tengah', 'Papua Pegunungan', 'Papua Selatan', 
    'Papua Barat Daya'
];
</script>

<template>
    <Head title="Lengkapi Data Pendaftaran" />

    <div class="min-h-screen bg-[#f5f4f2] text-[#161514] font-sans selection:bg-[#d02e5c] selection:text-white pb-20">
        
        <!-- Header Section -->
        <div class="bg-white border-b border-[#ebeae8] sticky top-0 z-30 shadow-sm relative overflow-hidden">
             <!-- Abstract Pattern Background -->
             <div class="absolute inset-0 opacity-[0.03] pointer-events-none overflow-hidden text-[#d02e5c]">
                <svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" class="w-full h-full fill-current">
                    <path d="M0,0 Q200,100 400,0 V400 H0 Z" />
                </svg>
            </div>
            
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight mb-1 text-[#161514]">
                        Lengkapi Profil <span class="text-[#d02e5c]">{{ registration.nama_calon }}</span>
                    </h1>
                    <p class="text-[#a8a196] font-medium text-sm sm:text-base">Penerimaan Santri Baru Rumah IT Hub</p>
                </div>
            </div>
        </div>

        <!-- Main Form Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="bg-white rounded-3xl shadow-xl shadow-[#d02e5c]/5 border border-[#ebeae8] p-6 sm:p-10 relative overflow-hidden">
                
                <form @submit.prevent="submit" class="space-y-8 relative z-10">
                    
                    <div v-for="(question, index) in questions" :key="question.id" class="p-6 bg-[#f5f4f2] rounded-2xl border border-[#ebeae8] shadow-sm hover:shadow-md transition-shadow">
                        
                        <label class="block text-base sm:text-lg font-bold text-[#161514] mb-3 relative">
                            <span class="inline-flex w-6 h-6 items-center justify-center bg-[#d02e5c] text-white text-xs rounded-full mr-2 shadow-sm font-black">{{ index + 1 }}</span>
                            {{ question.question_text }}
                            <span v-if="question.is_required" class="text-red-500 ml-1 text-xl leading-none">*</span>
                        </label>
                        
                        <!-- Text Input / Select Dropdown for Provinsi -->
                        <div v-if="question.type === 'text'" class="mt-2">
                            <select 
                                v-if="question.question_text.toLowerCase().includes('provinsi')"
                                v-model="form['question_' + question.id]"
                                :required="question.is_required"
                                class="w-full px-4 py-3 sm:py-4 rounded-xl border border-[#ebeae8] bg-white text-[#161514] font-medium focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all shadow-sm appearance-none cursor-pointer"
                            >
                                <option value="" disabled>Pilih Provinsi</option>
                                <option v-for="prov in provinces" :key="prov" :value="prov">{{ prov }}</option>
                            </select>
                            
                            <input 
                                v-else
                                type="text" 
                                v-model="form['question_' + question.id]" 
                                :required="question.is_required"
                                class="w-full px-4 py-3 sm:py-4 rounded-xl border border-[#ebeae8] bg-white text-[#161514] font-medium focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all shadow-sm"
                                placeholder="Jawaban Anda"
                            />
                        </div>

                        <!-- Date Input -->
                        <div v-else-if="question.type === 'date'" class="mt-2 text-[#a8a196]">
                            <input 
                                type="date" 
                                v-model="form['question_' + question.id]" 
                                :required="question.is_required"
                                class="w-full sm:w-auto px-4 py-3 sm:py-4 rounded-xl border border-[#ebeae8] bg-white text-[#161514] font-medium focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all shadow-sm cursor-pointer"
                            />
                        </div>

                        <!-- Textarea -->
                        <div v-else-if="question.type === 'textarea'" class="mt-2">
                            <textarea 
                                v-model="form['question_' + question.id]" 
                                :required="question.is_required"
                                rows="3"
                                class="w-full px-4 py-3 sm:py-4 rounded-xl border border-[#ebeae8] bg-white text-[#161514] font-medium focus:outline-none focus:ring-2 focus:ring-[#d02e5c] focus:border-transparent transition-all shadow-sm resize-y"
                                placeholder="Tuliskan jawaban lengkap Anda"
                            ></textarea>
                        </div>

                        <!-- Single Choice (Radio) -->
                        <div v-else-if="question.type === 'radio'" class="mt-2 grid sm:grid-cols-2 gap-3">
                            <label v-for="(opt, oIdx) in question.options" :key="oIdx" class="flex items-center p-4 bg-white border border-[#ebeae8] rounded-xl cursor-pointer hover:bg-[#f5f4f2] transition-colors group">
                                <div class="relative flex items-center justify-center">
                                    <input 
                                        type="radio" 
                                        :name="'question_' + question.id" 
                                        :value="opt" 
                                        v-model="form['question_' + question.id]"
                                        :required="question.is_required"
                                        class="peer appearance-none w-5 h-5 border-2 border-[#a8a196] rounded-full checked:border-[#d02e5c] checked:border-[5px] transition-all"
                                    />
                                </div>
                                <span class="ml-3 font-semibold text-[#4e4b47] group-hover:text-[#161514] peer-checked:text-[#161514]">{{ opt }}</span>
                            </label>
                        </div>

                        <!-- Multiple Choice (Checkbox) -->
                        <div v-else-if="question.type === 'checkbox'" class="mt-2 grid gap-3">
                            <label v-for="(opt, oIdx) in question.options" :key="oIdx" class="flex items-center p-4 bg-white border border-[#ebeae8] rounded-xl cursor-pointer hover:bg-[#f5f4f2] transition-colors group">
                                <input 
                                    type="checkbox" 
                                    :value="opt" 
                                    v-model="form['question_' + question.id]"
                                    aria-label="opt"
                                    class="w-5 h-5 text-[#d02e5c] bg-white border-[#a8a196] rounded focus:ring-[#d02e5c] transition-all cursor-pointer"
                                />
                                <span class="ml-3 font-semibold text-[#4e4b47] group-hover:text-[#161514]">{{ opt }}</span>
                            </label>
                            <p v-if="question.is_required && form['question_' + question.id].length === 0" class="text-xs text-red-500 mt-1 font-medium">Beri centang pada minimal satu pilihan.</p>
                        </div>

                        <!-- File Upload -->
                        <div v-else-if="question.type === 'file'" class="mt-3">
                            <div class="relative items-center justify-center w-full">
                                <label class="flex flex-col w-full h-32 border-2 border-[#a8a196] border-dashed hover:border-[#d02e5c] hover:bg-white rounded-xl transition-all cursor-pointer overflow-hidden group">
                                    <div class="flex flex-col items-center justify-center pt-7 pb-6 h-full w-full">
                                        <svg class="w-8 h-8 text-[#a8a196] group-hover:text-[#d02e5c] mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="text-sm font-semibold text-[#4e4b47] group-hover:text-[#d02e5c] transition-colors">
                                            <span v-if="form['question_' + question.id]">{{ form['question_' + question.id].name }}</span>
                                            <span v-else>Klik atau *drag* file ke sini</span>
                                        </p>
                                    </div>
                                    <input 
                                        type="file" 
                                        @input="(e) => handleFileUpload(e, 'question_' + question.id)" 
                                        :required="question.is_required" 
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" 
                                        accept=".png,.jpg,.jpeg,.pdf"
                                    />
                                </label>
                            </div>
                        </div>

                        <!-- Error Message per field -->
                        <p v-if="form.errors['question_' + question.id]" class="text-red-500 text-sm font-bold mt-2">
                            {{ form.errors['question_' + question.id] }}
                        </p>

                    </div>

                    <!-- Submit Button -->
                     <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full sm:w-auto px-8 py-5 bg-[#161514] text-white rounded-xl font-bold text-lg shadow-xl shadow-black/10 hover:-translate-y-1 hover:shadow-2xl hover:bg-[#2b2927] transition-all disabled:opacity-50 disabled:hover:translate-y-0 flex items-center justify-center mx-auto group mt-8"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xml:space="preserve" viewBox="0 0 100 100" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"><path d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50" fill="currentColor"><animateTransform type="rotate" dur="1s" repeatCount="indefinite" values="0 50 50;360 50 50" attributeName="transform"/></path></svg>
                        <span v-else>Kirim Data Berkas Pribadi</span>
                    </button>

                </form>

            </div>
        </main>
    </div>
</template>
