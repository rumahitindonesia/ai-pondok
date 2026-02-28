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
            <p class="text-[#a8a196] font-medium leading-relaxed mb-8">Alhamdulillah, data pendaftaran telah kami terima. Untuk melanjutkan ke tahap seleksi, silakan segera melunasi biaya pendaftaran.</p>
            
            <div class="bg-[#fcf8f5] border border-[#ebeae8] rounded-3xl p-6 mb-10 text-left">
                <div class="flex items-center gap-4 mb-5 pb-5 border-b border-[#ebeae8]">
                    <div class="w-12 h-12 bg-[#c97e60]/10 rounded-xl flex items-center justify-center text-[#c97e60] shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-[#a8a196]">Total Tagihan</p>
                        <p class="text-2xl font-black text-[#161514]">Rp250.000</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-bold text-[#161514] mb-2">📌 Pembayaran dapat dilakukan melalui:</p>
                    <p class="text-sm text-[#a8a196]"><span class="font-bold text-[#161514]">Bank Syariah Indonesia (BSI)</span></p>
                    <p class="text-sm text-[#a8a196]">No. Rekening: <span class="font-bold text-[#c97e60] select-all tracking-wide">7830030012</span></p>
                    <p class="text-sm text-[#a8a196]">Atas Nama: <span class="font-bold text-[#161514]">Pondok IT Entrepreneur</span></p>
                </div>
            </div>
            
            <Link 
                :href="route('psb.register')" 
                class="inline-block px-10 py-4 bg-[#161514] text-white font-black rounded-full hover:bg-black transition-all shadow-xl active:scale-95"
            >
                Kembali ke Beranda
            </Link>
        </div>
    </div>
</template>
