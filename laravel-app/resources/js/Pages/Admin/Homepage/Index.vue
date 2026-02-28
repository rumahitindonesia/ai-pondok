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

                <!-- Edit Modal / Form (Simplified for this task) -->
                <div v-if="editingSection" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white dark:bg-[#161514] w-full max-w-2xl rounded-[32px] shadow-2xl overflow-hidden animate-fade-in-up">
                        <div class="p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-2xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                                    Edit Section: <span class="text-brand-rose uppercase">{{ editingSection.section_key }}</span>
                                </h3>
                                <button @click="editingSection = null" class="text-gray-400 hover:text-brand-rose transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submit" class="space-y-6">
                                <div>
                                    <InputLabel for="title" value="Section Title" />
                                    <TextInput id="title" v-model="form.title" class="mt-1 block w-full" />
                                </div>

                                <div>
                                    <InputLabel for="subtitle" value="Section Subtitle" />
                                    <TextInput id="subtitle" v-model="form.subtitle" class="mt-1 block w-full" />
                                </div>

                                <!-- Dynamic Content Editor (Simplified: JSON text area for now) -->
                                <div>
                                    <InputLabel value="Content (Structured JSON)" />
                                    <textarea 
                                        v-model="form.content"
                                        rows="6"
                                        class="mt-1 block w-full bg-[#fcf8f5] border-[#ebeae8] rounded-2xl p-4 text-[#161514] focus:ring-4 focus:ring-[#c97e60]/10 focus:border-[#c97e60] transition-all outline-none shadow-sm dark:bg-[#0a0a0a] dark:border-[#383736] dark:text-[#f2e8d5] font-mono text-xs"
                                        placeholder='{"key": "value"}'
                                        @input="e => { try { form.content = JSON.parse(e.target.value) } catch(err) {} }"
                                    >{{ JSON.stringify(form.content, null, 2) }}</textarea>
                                    <p class="mt-2 text-[10px] text-gray-400 font-medium">Ubah isi konten dalam format JSON untuk fleksibilitas maksimal.</p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" v-model="form.is_active" class="rounded-lg text-brand-rose focus:ring-brand-rose" />
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Tampilkan di Homepage</span>
                                    </label>
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <RedwoodButton type="button" variant="outline" @click="editingSection = null">
                                        Batal
                                    </RedwoodButton>
                                    <RedwoodButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Simpan Perubahan
                                    </RedwoodButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
