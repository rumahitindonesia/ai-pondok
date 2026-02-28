<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    sections: Array
});

const editingSection = ref(null);
const jsonText = ref('');

const form = useForm({
    title: '',
    subtitle: '',
    content: {},
    is_active: true
});

const startEdit = (section) => {
    editingSection.value = section;
    form.title = section.title;
    form.subtitle = section.subtitle;
    form.content = section.content || {};
    jsonText.value = JSON.stringify(section.content || {}, null, 2);
    form.is_active = section.is_active;
};

const submit = () => {
    form.put(route('admin.homepage.update', editingSection.value.id), {
        onSuccess: () => {
            editingSection.value = null;
        }
    });
};
</script>

<template>
    <Head title="Manage Homepage" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Manajemen <span class="text-[#d02e5c]">Homepage</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Section List -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <div v-for="section in sections" :key="section.id" 
                        class="bg-white dark:bg-[#161514] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] p-6 lg:p-10 border border-[#ebeae8] dark:border-[#3e3c3a] relative group hover:border-[#d02e5c] transition-all duration-500 lg:hover:-translate-y-1 cursor-pointer"
                        @click="startEdit(section)">
                        
                        <!-- Decorative Abstract Shape -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-rose/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none transition-transform group-hover:scale-110"></div>
                        
                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <span class="text-[10px] uppercase tracking-widest font-black text-brand-rose bg-brand-rose/10 px-3 py-1.5 rounded-xl border border-brand-rose/20">
                                {{ section.section_key }}
                            </span>
                            <span :class="section.is_active ? 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20' : 'text-[#a8a196] bg-[#ebeae8] dark:bg-[#3e3c3a] border-[#ebeae8] dark:border-[#3e3c3a]'" class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl border">
                                {{ section.is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </div>
                        <h3 class="text-xl lg:text-2xl font-black text-[#161514] dark:text-[#f2e8d5] mb-3 tracking-tight relative z-10">{{ section.title }}</h3>
                        <p class="text-sm text-[#4e4b47] dark:text-[#a8a196] line-clamp-2 relative z-10 leading-relaxed">{{ section.subtitle || 'No subtitle configured.' }}</p>
                        
                        <div class="mt-8 pt-6 border-t border-[#ebeae8] dark:border-[#3e3c3a] relative z-10">
                            <RedwoodButton variant="outline" size="sm" class="w-full">
                                Edit Content
                            </RedwoodButton>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal / Form -->
                <div v-if="editingSection" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white dark:bg-[#161514] w-full max-w-2xl max-h-[90vh] flex flex-col rounded-[32px] shadow-2xl animate-fade-in-up">
                        <!-- Modal Header -->
                        <div class="p-6 lg:p-8 border-b border-[#ebeae8] dark:border-[#383736] shrink-0">
                            <div class="flex justify-between items-center">
                                <h3 class="text-2xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                                    Edit Section: <span class="text-brand-rose uppercase">{{ editingSection.section_key }}</span>
                                </h3>
                                <button @click="editingSection = null" class="text-gray-400 hover:text-brand-rose transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body (Scrollable) -->
                        <div class="p-6 lg:p-8 overflow-y-auto custom-scrollbar flex-1">
                            <form @submit.prevent="submit" class="space-y-6" id="edit-section-form">
                                <div>
                                    <InputLabel for="title" value="Section Title" />
                                    <TextInput id="title" v-model="form.title" class="mt-1 block w-full" />
                                </div>

                                <div>
                                    <InputLabel for="subtitle" value="Section Subtitle" />
                                    <TextInput id="subtitle" v-model="form.subtitle" class="mt-1 block w-full" />
                                </div>

                                <!-- Dynamic Content Editors Based on Section Key -->
                                <div v-if="editingSection.section_key === 'hero'" class="space-y-4 border-t border-[#ebeae8] dark:border-[#383736] pt-4">
                                    <h4 class="font-bold text-[#161514] dark:text-[#f2e8d5]">Pengaturan Konten Hero</h4>
                                    <div>
                                        <InputLabel value="Deskripsi Utama" />
                                        <textarea v-model="form.content.description" rows="3" class="mt-1 block w-full border-[#ebeae8] rounded-xl shadow-sm focus:border-[#c97e60] focus:ring focus:ring-[#c97e60]/20 dark:bg-[#0a0a0a] dark:border-[#383736] dark:text-[#f2e8d5]"></textarea>
                                    </div>
                                    <div>
                                        <InputLabel value="Teks Tombol (Call to Action)" />
                                        <TextInput v-model="form.content.cta_text" class="mt-1 block w-full" />
                                    </div>
                                </div>

                                <div v-else v-for="(categoryData, categoryKey) in form.content" :key="categoryKey" class="space-y-4 border-t border-[#ebeae8] dark:border-[#383736] pt-4">
                                    <!-- Works for both Programs and Tracks generically -->
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-[#161514] dark:text-[#f2e8d5] capitalize">{{ categoryData.title || categoryData.name }}</h4>
                                    </div>
                                    
                                    <div>
                                        <InputLabel :value="editingSection.section_key === 'programs' ? 'Nama Kategori' : 'Nama Jurusan'" />
                                        <TextInput v-if="'title' in categoryData" v-model="categoryData.title" class="mt-1 block w-full" />
                                        <TextInput v-if="'name' in categoryData" v-model="categoryData.name" class="mt-1 block w-full" />
                                    </div>

                                    <div>
                                        <InputLabel :value="editingSection.section_key === 'programs' ? 'Daftar Jurusan (Pisahkan dengan koma)' : 'Materi / Spesialisasi (Pisahkan dengan koma)'" />
                                        <textarea 
                                            :value="(categoryData.tracks || categoryData.specializations || []).join(', ')"
                                            @input="e => { 
                                                const val = e.target.value.split(',').map(s => s.trim()).filter(s => s);
                                                if ('tracks' in categoryData) categoryData.tracks = val;
                                                if ('specializations' in categoryData) categoryData.specializations = val;
                                            }"
                                            rows="3" 
                                            class="mt-1 block w-full border-[#ebeae8] rounded-xl shadow-sm focus:border-[#c97e60] focus:ring focus:ring-[#c97e60]/20 dark:bg-[#0a0a0a] dark:border-[#383736] dark:text-[#f2e8d5]"
                                            placeholder="Contoh: Programmer, Bisnis Digital"
                                        ></textarea>
                                        <p class="mt-1 text-[10px] text-gray-400">Pisahkan setiap item dengan tanda koma (,).</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 border-t border-[#ebeae8] dark:border-[#383736] pt-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" v-model="form.is_active" class="rounded-lg text-brand-rose focus:ring-brand-rose" />
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Tampilkan di Homepage</span>
                                    </label>
                                </div>
                            </form>
                        </div>

                        <!-- Modal Footer (Fixed) -->
                        <div class="p-6 lg:p-8 border-t border-[#ebeae8] dark:border-[#383736] shrink-0 bg-gray-50 dark:bg-[#0a0a0a] rounded-b-[32px] flex justify-end gap-3 rounded-b-3xl">
                            <RedwoodButton type="button" variant="outline" @click="editingSection = null">
                                Batal
                            </RedwoodButton>
                            <RedwoodButton type="submit" form="edit-section-form" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Simpan Perubahan
                            </RedwoodButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
