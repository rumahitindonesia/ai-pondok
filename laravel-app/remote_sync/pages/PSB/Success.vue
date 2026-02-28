<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Pendaftaran Berhasil" />

    <div class="min-h-screen bg-[#f5f4f2] flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white shadow-[0_40px_80px_rgba(0,0,0,0.1)] rounded-[48px] p-12 text-center border border-[#ebeae8]">
            <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-10 shadow-lg shadow-emerald-500/20">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h1 class="text-4xl font-black tracking-tight mb-4">Pendaftaran <span class="text-emerald-500">Berhasil!</span></h1>
            <p class="text-[#a8a196] font-medium leading-relaxed mb-10">Alhamdulillah, data pendaftaran pendaftaran calon santri telah kami terima. Kami akan segera menghubungi Anda melalui WhatsApp untuk tahap seleksi selanjutnya.</p>
            
            <Link 
                :href="route('psb.register')" 
                class="inline-block px-10 py-4 bg-[#161514] text-white font-black rounded-full hover:bg-black transition-all shadow-xl active:scale-95"
            >
                Kembali ke Beranda
            </Link>
        </div>
    </div>
</template>
