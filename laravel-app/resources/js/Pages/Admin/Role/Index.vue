<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';

const props = defineProps({
    roles: Array,
    permissions: Array,
});

const activeRoleId = ref(props.roles[0]?.id || null);
const activeRole = computed(() => props.roles.find(r => r.id === activeRoleId.value));

const modules = [
    { title: 'Data Santri', key: 'santri' },
    { title: 'Keuangan', key: 'finance' },
    { title: 'Registrasi PSB', key: 'psb' },
    { title: 'Kurikulum', key: 'materi' },
    { title: 'Absensi', key: 'attendance' },
    { title: 'Kedisiplinan', key: 'discipline' },
    { title: 'Manajemen User', key: 'users' },
];

const actions = [
    { label: 'View', key: 'view' },
    { label: 'Create', key: 'create' },
    { label: 'Update', key: 'update' },
    { label: 'Delete', key: 'delete' },
];

const form = useForm({
    permissions: [],
});

const selectRole = (role) => {
    activeRoleId.value = role.id;
    form.permissions = role.permissions.map(p => p.name);
};

const togglePermission = (permName) => {
    if (activeRole.value?.name === 'Super Admin') return; // Cannot edit Super Admin

    const index = form.permissions.indexOf(permName);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permName);
    }
};

const savePermissions = () => {
    form.patch(route('admin.roles.update', activeRoleId.value), {
        preserveScroll: true,
    });
};

const isPermitted = (action, module) => {
    return form.permissions.includes(`${action} ${module}`);
};
</script>

<template>
    <Head title="Hak Akses (Roles)" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Hak Akses <span class="text-[#c97e60]">& Roles</span>
            </h2>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar: Roles List -->
            <div class="lg:col-span-1 space-y-4">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] px-2 mb-4">Daftar Role</p>
                <button v-for="role in roles" :key="role.id" 
                        @click="selectRole(role)"
                        :class="[
                            'w-full text-left p-6 rounded-[24px] border transition-all flex items-center justify-between group',
                            activeRoleId === role.id 
                                ? 'bg-[#c97e60] border-[#c97e60] text-white shadow-xl shadow-[#c97e60]/20' 
                                : 'bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] text-[#161514] dark:text-[#f2e8d5] hover:border-[#c97e60]'
                        ]"
                >
                    <div class="min-w-0">
                        <p class="font-bold underline-offset-4" :class="activeRoleId === role.id ? 'underline' : ''">{{ role.name }}</p>
                        <p class="text-[10px] opacity-70 font-medium truncate mt-1">
                            {{ role.permissions.length }} Hak Akses Aktif
                        </p>
                    </div>
                    <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-all" :class="activeRoleId === role.id ? 'opacity-100' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>

            <!-- Permission Matrix -->
            <div class="lg:col-span-3">
                <div v-if="activeRole" class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                    <div class="p-8 lg:p-12 border-b border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-between bg-gray-50/50 dark:bg-[#1a1918]/50">
                        <div>
                            <h3 class="text-xl font-bold text-[#161514] dark:text-[#f2e8d5]">Konfigurasi Izin: {{ activeRole.name }}</h3>
                            <p class="text-xs text-[#a8a196] mt-1 italic" v-if="activeRole.name === 'Super Admin'">* Super Admin selalu memiliki semua akses.</p>
                            <p class="text-xs text-[#a8a196] mt-1" v-else>Centang kolom di bawah untuk mengaktifkan izin modul.</p>
                        </div>
                        <RedwoodButton @click="savePermissions" :disabled="activeRole.name === 'Super Admin' || form.processing" :class="{ 'opacity-50': activeRole.name === 'Super Admin' }">
                            Simpan Perubahan
                        </RedwoodButton>
                    </div>

                    <div class="p-0">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/30 dark:bg-[#1a1918]/30">
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Modul / Fitur</th>
                                    <th v-for="action in actions" :key="action.key" class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-[#a8a196] text-center">
                                        {{ action.label }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                                <tr v-for="module in modules" :key="module.key" class="hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                                    <td class="px-8 py-6">
                                        <p class="font-bold text-[#161514] dark:text-[#f2e8d5]">{{ module.title }}</p>
                                        <p class="text-[10px] text-[#a8a196] tracking-tight">Akses ke modul {{ module.key }}</p>
                                    </td>
                                    <td v-for="action in actions" :key="action.key" class="px-8 py-6 text-center">
                                        <button 
                                            @click="togglePermission(`${action.key} ${module.key}`)"
                                            :disabled="activeRole.name === 'Super Admin'"
                                            :class="[
                                                'w-8 h-8 rounded-full border-2 transition-all flex items-center justify-center mx-auto',
                                                isPermitted(action.key, module.key) 
                                                    ? 'bg-[#c97e60] border-[#c97e60] text-white' 
                                                    : 'border-[#ebeae8] dark:border-[#3e3c3a] text-transparent hover:border-[#c97e60]/50'
                                            ]"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
