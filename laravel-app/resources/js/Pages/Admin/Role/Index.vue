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
    { title: 'Struktur & Pengurus', key: 'organisasi' },
    { title: 'Manajemen Homepage', key: 'homepage' },
    { title: 'Manajemen Planner (Content Manager)', key: 'content manager' },
    { title: 'Tugas Media (Content Staff)', key: 'content staff' },
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

const newRoleForm = useForm({
    name: '',
});

const isAddingRole = ref(false);

const addRole = () => {
    newRoleForm.post(route('admin.roles.store'), {
        onSuccess: () => {
            isAddingRole.value = false;
            newRoleForm.reset();
        },
    });
};

const deleteRole = (role) => {
    if (confirm(`Apakah Anda yakin ingin menghapus role "${role.name}"?`)) {
        router.delete(route('admin.roles.destroy', role.id), {
            onSuccess: () => {
                if (activeRoleId.value === role.id) {
                    activeRoleId.value = props.roles[0]?.id || null;
                }
            },
        });
    }
};
</script>

<template>
    <Head title="Hak Akses (Roles)" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Hak Akses <span class="text-[#d02e5c]">& Roles</span>
            </h2>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar: Roles List -->
            <div class="lg:col-span-1 space-y-4">
                <div class="flex items-center justify-between px-2 mb-4">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Daftar Role</p>
                    <button @click="isAddingRole = true" class="text-[#d02e5c] hover:text-[#b0224a] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </button>
                </div>

                <button v-for="role in roles" :key="role.id" 
                        @click="selectRole(role)"
                        :class="[
                            'w-full text-left p-6 rounded-[24px] border transition-all flex items-center justify-between group',
                            activeRoleId === role.id 
                                ? 'bg-[#d02e5c] border-[#d02e5c] text-white shadow-xl shadow-[#d02e5c]/20' 
                                : 'bg-white dark:bg-[#161514] border-[#ebeae8] dark:border-[#3e3c3a] text-[#161514] dark:text-[#f2e8d5] hover:border-[#d02e5c]'
                        ]"
                >
                    <div class="min-w-0">
                        <p class="font-bold underline-offset-4" :class="activeRoleId === role.id ? 'underline' : ''">{{ role.name }}</p>
                        <p class="text-[10px] opacity-70 font-medium truncate mt-1">
                            {{ role.permissions.length }} Hak Akses Aktif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-all" :class="activeRoleId === role.id ? 'opacity-100' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" /></svg>
                        <button 
                            v-if="role.name !== 'Super Admin'"
                            @click.stop="deleteRole(role)"
                            class="p-1 text-[#a8a196] hover:text-rose-500 transition-colors opacity-0 group-hover:opacity-100"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
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
                                                    ? 'bg-[#d02e5c] border-[#d02e5c] text-white' 
                                                    : 'border-[#ebeae8] dark:border-[#3e3c3a] text-transparent hover:border-[#d02e5c]/50'
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

        <!-- Add Role Modal -->
        <div v-if="isAddingRole" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="isAddingRole = false"></div>
            <div class="relative bg-white dark:bg-[#161514] w-full max-w-md rounded-[40px] shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden p-8 lg:p-12">
                <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5] mb-8">Tambah Role Baru</h3>
                <form @submit.prevent="addRole" class="space-y-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Nama Role</label>
                        <input v-model="newRoleForm.name" type="text" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" placeholder="Misal: Staff Keuangan" required />
                        <p v-if="newRoleForm.errors.name" class="text-xs text-rose-500 font-bold mt-1">{{ newRoleForm.errors.name }}</p>
                    </div>
                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="button" @click="isAddingRole = false" class="text-sm font-black uppercase tracking-widest text-[#a8a196] transition-colors">Batal</button>
                        <RedwoodButton type="submit" :disabled="newRoleForm.processing">Buat Role</RedwoodButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
