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
            <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                Manage Homepage Sections
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Section List -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="section in sections" :key="section.id" 
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100 dark:border-gray-700 hover:border-brand-rose transition-colors cursor-pointer"
                        @click="startEdit(section)">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] uppercase tracking-widest font-black text-brand-rose bg-brand-rose/5 px-2 py-1 rounded-md">
                                {{ section.section_key }}
                            </span>
                            <span :class="section.is_active ? 'text-emerald-500' : 'text-gray-400'" class="text-xs font-bold">
                                {{ section.is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">{{ section.title }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-2">{{ section.subtitle }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700">
                            <RedwoodButton variant="outline" size="sm" class="w-full">
                                Edit Section
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
