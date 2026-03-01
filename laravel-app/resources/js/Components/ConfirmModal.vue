<script setup>
defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Konfirmasi Hapus'
    },
    message: {
        type: String,
        default: 'Apakah Anda yakin ingin melakukan aksi ini?'
    },
    confirmText: {
        type: String,
        default: 'Ya, Hapus'
    },
    cancelText: {
        type: String,
        default: 'Batal'
    },
    danger: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="emit('cancel')"></div>

            <!-- Modal Card -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div v-if="show" class="relative bg-white dark:bg-[#1e1d1b] w-full max-w-sm rounded-3xl shadow-2xl border border-[#ebeae8] dark:border-[#3e3c3a] overflow-hidden">
                    <!-- Red accent bar at top -->
                    <div class="h-1.5 w-full" :class="danger ? 'bg-gradient-to-r from-rose-500 to-[#d02e5c]' : 'bg-gradient-to-r from-blue-400 to-blue-600'"></div>

                    <div class="p-8">
                        <!-- Icon -->
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl mb-6 mx-auto" :class="danger ? 'bg-rose-50 dark:bg-rose-900/20' : 'bg-blue-50 dark:bg-blue-900/20'">
                            <svg v-if="danger" class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <svg v-else class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-black text-center text-[#161514] dark:text-[#f2e8d5] mb-2">{{ title }}</h3>

                        <!-- Message -->
                        <p class="text-sm text-center text-[#a8a196] leading-relaxed mb-8">{{ message }}</p>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <button
                                @click="emit('cancel')"
                                class="flex-1 px-4 py-3 rounded-2xl border border-[#ebeae8] dark:border-[#3e3c3a] bg-white dark:bg-[#2d2c2a] text-sm font-bold text-[#161514] dark:text-[#f2e8d5] hover:bg-gray-50 dark:hover:bg-[#3e3c3a] transition-colors"
                            >
                                {{ cancelText }}
                            </button>
                            <button
                                @click="emit('confirm')"
                                class="flex-1 px-4 py-3 rounded-2xl text-sm font-bold text-white transition-all shadow-lg"
                                :class="danger
                                    ? 'bg-gradient-to-r from-rose-500 to-[#d02e5c] hover:shadow-rose-500/30 hover:scale-[1.02]'
                                    : 'bg-gradient-to-r from-blue-500 to-blue-600 hover:shadow-blue-500/30 hover:scale-[1.02]'"
                            >
                                {{ confirmText }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>
