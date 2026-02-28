<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import RedwoodToast from '@/Components/RedwoodToast.vue';
import { notifications, removeNotification } from '@/Utils/toast';
import { Link } from '@inertiajs/vue3';

const isSidebarOpen = ref(true);
const isMobileMenuOpen = ref(false);
const isDark = ref(true);

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

// Initialize theme state (class is handled by app.blade.php script)
if (typeof window !== 'undefined') {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        isDark.value = false;
    }
}
</script>

<template>
    <div class="flex h-screen bg-[#f5f4f2] dark:bg-[#0a0a0a] text-[#161514] dark:text-[#f2e8d5] overflow-hidden transition-colors duration-500">
        <!-- Overlay for mobile -->
        <div 
            v-if="isMobileMenuOpen" 
            @click="isMobileMenuOpen = false"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] lg:hidden transition-opacity"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                isSidebarOpen ? 'lg:w-72' : 'lg:w-20',
                isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            class="fixed lg:relative inset-y-0 left-0 w-72 lg:w-auto bg-white dark:bg-[#161514] flex flex-col transition-all duration-300 z-[70] lg:z-50 border-r border-[#ebeae8] dark:border-[#3e3c3a] shadow-2xl lg:shadow-xl"
        >
            <!-- Organic Pattern Overlay -->
            <div class="absolute top-0 left-0 w-full h-64 opacity-[0.03] dark:opacity-5 pointer-events-none overflow-hidden text-[#c97e60]">
                <svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" class="w-full h-full fill-current">
                    <path d="M0,0 Q200,100 400,0 V400 H0 Z" />
                </svg>
            </div>

            <!-- Logo Area -->
            <div class="h-20 flex items-center justify-between px-6 relative z-10 transition-all">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                         <ApplicationLogo class="w-10 h-10 object-contain" />
                    </div>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-black text-base tracking-tighter transition-colors uppercase">Rumah IT <span class="text-[#c97e60]">Indonesia</span></span>
                </Link>
                
                <!-- Close Button Mobile -->
                <button @click="isMobileMenuOpen = false" class="lg:hidden text-[#a8a196] hover:text-[#c97e60]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-2 relative z-10 overflow-y-auto custom-scrollbar">
                <Link 
                    :href="route('dashboard')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('dashboard') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('dashboard') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Dashboard</span>
                </Link>

                <div v-if="isSidebarOpen || isMobileMenuOpen" class="px-4 py-6">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#a8a196] opacity-50">Manajemen</p>
                </div>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view santri')"
                    :href="route('santri.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('santri.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('santri.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Data Santri</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view organisasi')"
                    :href="route('organisasi.jabatan.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('organisasi.jabatan.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('organisasi.jabatan.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Struktur Jabatan</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view organisasi')"
                    :href="route('organisasi.pengurus.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('organisasi.pengurus.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('organisasi.pengurus.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Data Pengurus</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view psb')"
                    :href="route('admin.psb.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('admin.psb.index') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('admin.psb.index') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Registrasi PSB</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view psb')"
                    :href="route('admin.psb.form-builder')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('admin.psb.form-builder') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('admin.psb.form-builder') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Form Builder PSB</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view finance')"
                    :href="route('finance.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('finance.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('finance.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Keuangan</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view attendance')"
                    :href="route('records.attendance')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('records.attendance') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('records.attendance') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Absensi</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view materi')"
                    :href="route('materi.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('materi.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('materi.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Kurikulum</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view discipline')"
                    :href="route('records.discipline')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('records.discipline') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('records.discipline') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Log Kedisiplinan</span>
                </Link>

                <div v-if="(isSidebarOpen || isMobileMenuOpen) && $page.props.auth.user.permissions.includes('view users')" class="px-4 py-6">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#a8a196] opacity-50">Administrasi</p>
                </div>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view users')"
                    :href="route('admin.users.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('admin.users.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('admin.users.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Manajemen User</span>
                </Link>

                <Link 
                    v-if="$page.props.auth.user.permissions.includes('view users')"
                    :href="route('admin.roles.index')" 
                    :class="[
                        'group flex items-center rounded-xl transition-all hover:bg-[#ebeae8] dark:hover:bg-[#21201f]',
                        isSidebarOpen || isMobileMenuOpen ? 'px-4 py-3 gap-4' : 'py-3 justify-center'
                    ]"
                >
                    <svg class="w-5 h-5 transition-colors shrink-0" :class="route().current('admin.roles.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] group-hover:text-[#c97e60] dark:group-hover:text-[#f2e8d5]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span v-if="isSidebarOpen || isMobileMenuOpen" class="font-bold whitespace-nowrap transition-all duration-300" :class="route().current('admin.roles.*') ? 'text-[#c97e60]' : 'text-[#4e4b47] dark:text-[#a8a196]'">Hak Akses</span>
                </Link>
            </nav>

            <!-- User Area -->
            <div class="p-4 border-t border-[#ebeae8] dark:border-[#3e3c3a] bg-gray-50 dark:bg-[#1a1918] transition-colors">
                <Dropdown align="top" width="48">
                    <template #trigger>
                        <button 
                            :class="[
                                'w-full flex items-center rounded-xl hover:bg-[#ebeae8] dark:hover:bg-[#262524] transition-all text-left group',
                                isSidebarOpen || isMobileMenuOpen ? 'p-2 gap-3' : 'py-2 justify-center'
                            ]"
                        >
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-[#161514] border border-[#ebeae8] dark:border-[#3e3c3a] flex items-center justify-center overflow-hidden shadow-sm shrink-0">
                                <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar" class="w-full h-full object-cover" />
                                <span v-else class="text-[#c97e60] font-bold">{{ $page.props.auth.user.name.charAt(0) }}</span>
                            </div>
                            <div v-if="isSidebarOpen || isMobileMenuOpen" class="flex-1 overflow-hidden transition-all duration-300">
                                <p class="text-sm font-bold truncate">{{ $page.props.auth.user.name }}</p>
                                <p class="text-[10px] text-[#a8a196] truncate font-medium">{{ $page.props.auth.user.roles?.[0] || 'Member' }}</p>
                            </div>
                            <svg v-if="isSidebarOpen || isMobileMenuOpen" class="w-4 h-4 text-[#a8a196] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                    </template>
                </Dropdown>
            </div>

            <!-- Desktop Toggle Toggle Button -->
            <button 
                @click="isSidebarOpen = !isSidebarOpen"
                class="hidden lg:flex absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white dark:bg-[#3e3c3a] text-gray-400 hover:text-[#c97e60] dark:text-gray-500 dark:hover:text-white rounded-full items-center justify-center transition-all z-50 border-4 border-[#f5f4f2] dark:border-[#0a0a0a] shadow-lg"
            >
                <svg class="w-4 h-4 transition-transform duration-300" :class="isSidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
            <!-- Header (Optional top bar) -->
            <header v-if="$slots.header" class="h-20 lg:h-24 flex items-center justify-between px-4 lg:px-12 bg-white/80 dark:bg-[#0a0a0a]/80 backdrop-blur-md border-b border-[#ebeae8] dark:border-[#3e3c3a] relative z-20 transition-all">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                    <button @click="isMobileMenuOpen = true" class="lg:hidden p-2 text-[#a8a196] hover:text-[#c97e60]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <slot name="header" />
                </div>
                
                <!-- Tools Area -->
                <div class="flex items-center gap-3 lg:gap-6">
                    <!-- Theme Toggle -->
                    <button 
                        @click="toggleTheme" 
                        class="p-2 lg:p-3 rounded-xl text-[#a8a196] hover:bg-gray-100 dark:hover:bg-[#21201f] transition-all group shadow-sm bg-white/10"
                        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                    >
                        <svg v-if="isDark" class="w-5 h-5 group-hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else class="w-5 h-5 group-hover:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- Profile Circle Desktop Only for quick view -->
                    <div class="hidden sm:flex items-center gap-3">
                         <button class="relative text-[#a8a196] hover:text-[#c97e60] dark:hover:text-[#f2e8d5] transition-colors p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-[#c97e60] rounded-full ring-2 ring-white dark:ring-[#0a0a0a]"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar p-5 lg:p-10 transition-all duration-500">
                <slot />
            </main>
        </div>

        <!-- Global Toasts -->
        <div class="fixed top-0 left-0 right-0 z-[100] pointer-events-none">
            <RedwoodToast 
                v-for="n in notifications" 
                :key="n.id"
                :message="n.message"
                :type="n.type"
                :duration="n.duration"
                @close="removeNotification(n.id)"
            />
        </div>
    </div>
</template>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #3e3c3a;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #c97e60;
}
</style>
