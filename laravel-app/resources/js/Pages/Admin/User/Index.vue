<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import RedwoodButton from '@/Components/RedwoodButton.vue';
import RedwoodSelect from '@/Components/RedwoodSelect.vue';

const props = defineProps({
    users: Array,
    roles: Array,
    santris: Array, // Added for linking users to santris
});

const showingModal = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    santri_id: '', // Added for linking
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.password_confirmation = '';
    form.role = user.roles[0]?.name || '';
    form.santri_id = user.santri_id || ''; // Load linked santri if any
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (editingUser.value) {
        form.patch(route('admin.users.update', editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
};

const deleteUser = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        form.delete(route('admin.users.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black tracking-tight text-[#161514] dark:text-[#f2e8d5]">
                Manajemen <span class="text-[#d02e5c]">User</span>
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Table Header Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <!-- Space for potential search/filter in future -->
                </div>
                
                <RedwoodButton 
                    v-if="$page.props.auth.user.permissions.includes('create users')"
                    @click="openCreateModal"
                >
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Tambah User
                </RedwoodButton>
            </div>

            <!-- User Table Card -->
            <div class="bg-white dark:bg-[#161514] shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[32px] border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50/50 dark:bg-[#1a1918]/50">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Nama / Email</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Role</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ebeae8] dark:divide-[#3e3c3a]">
                        <tr v-for="user in users" :key="user.id" class="group hover:bg-[#fcf8f5] dark:hover:bg-[#1d1c1b] transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-[#f5f4f2] dark:bg-[#262524] border border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-center text-[#d02e5c] font-bold">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#161514] dark:text-[#f2e8d5] truncate">{{ user.name }}</p>
                                        <p class="text-[10px] text-[#a8a196] truncate">{{ user.email }}</p>
                                        <div v-if="user.santri" class="flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3 text-[#d02e5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                            <span class="text-[9px] font-bold text-[#d02e5c] uppercase tracking-wider truncate">Terkait: {{ user.santri.nama }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span v-for="role in user.roles" :key="role.id" 
                                      class="px-3 py-1 bg-[#d02e5c]/10 text-[#d02e5c] text-[10px] font-black uppercase tracking-wider rounded-lg border border-[#d02e5c]/20">
                                    {{ role.name }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button 
                                        v-if="$page.props.auth.user.permissions.includes('update users')"
                                        @click="openEditModal(user)" 
                                        class="p-2 text-[#a8a196] hover:text-[#d02e5c] transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button 
                                        v-if="user.id !== $page.props.auth.user.id && $page.props.auth.user.permissions.includes('delete users')" 
                                        @click="deleteUser(user.id)" 
                                        class="p-2 text-[#a8a196] hover:text-rose-500 transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showingModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
            <div class="relative bg-white dark:bg-[#161514] w-full max-w-xl rounded-[40px] shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="p-8 lg:p-12">
                    <h3 class="text-2xl font-black text-[#161514] dark:text-[#f2e8d5] mb-8">
                        {{ editingUser ? 'Edit User' : 'Tambah User Baru' }}
                    </h3>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" placeholder="Masukkan nama..." required />
                            <p v-if="form.errors.name" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Email</label>
                            <input v-model="form.email" type="email" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" placeholder="email@example.com" required />
                            <p v-if="form.errors.email" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.email }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Role / Akses</label>
                                <RedwoodSelect v-model="form.role" required>
                                    <option value="" disabled>Pilih Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                                </RedwoodSelect>
                                <p v-if="form.errors.role" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.role }}</p>
                            </div>
                            
                            <!-- Link to Santri Dropdown -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Hubungkan ke Santri (Opsional)</label>
                                <RedwoodSelect v-model="form.santri_id">
                                    <option value="">-- Tidak Dihubungkan --</option>
                                    <option v-for="santri in santris" :key="santri.id" :value="santri.id">
                                        {{ santri.nama }} ({{ santri.nis }})
                                    </option>
                                </RedwoodSelect>
                                <p v-if="form.errors.santri_id" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.santri_id }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Password {{ editingUser ? '(Optional)' : '' }}</label>
                                <input v-model="form.password" type="password" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" placeholder="********" :required="!editingUser" />
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a196]">Konfirmasi Password</label>
                                <input v-model="form.password_confirmation" type="password" class="w-full bg-[#f5f4f2] dark:bg-[#262524] border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#d02e5c] transition-all" placeholder="********" :required="!editingUser" />
                            </div>
                            <p v-if="form.errors.password" class="col-span-2 text-xs text-rose-500 font-bold mt-1">{{ form.errors.password }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-12">
                            <button type="button" @click="closeModal" class="text-sm font-black uppercase tracking-widest text-[#a8a196] hover:text-[#161514] dark:hover:text-[#f2e8d5] transition-colors">Batal</button>
                            <RedwoodButton type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                                {{ editingUser ? 'Simpan Perubahan' : 'Buat User' }}
                            </RedwoodButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
