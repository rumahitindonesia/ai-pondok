<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success' // success, error, info
    },
    duration: {
        type: Number,
        default: 3000
    }
});

const emit = defineEmits(['close']);
const visible = ref(false);

onMounted(() => {
    visible.value = true;
    setTimeout(() => {
        visible.value = false;
        setTimeout(() => emit('close'), 300);
    }, props.duration);
});
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="-translate-y-10 opacity-0 scale-95"
        enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div v-if="visible" class="fixed top-8 left-1/2 -translate-x-1/2 z-[100] w-full max-w-sm px-4">
            <div 
                :class="[
                    'relative overflow-hidden rounded-[24px] border shadow-2xl backdrop-blur-md px-6 py-4 flex items-center gap-4 transition-all',
                    type === 'success' ? 'bg-white/90 dark:bg-[#161514]/90 border-[#c97e60]/20 shadow-[#c97e60]/10' : 
                    'bg-white/90 dark:bg-[#161514]/90 border-rose-500/20 shadow-rose-500/10'
                ]"
            >
                <!-- Accent Line -->
                <div 
                    :class="[
                        'absolute top-0 left-0 w-1 h-full',
                        type === 'success' ? 'bg-[#c97e60]' : 'bg-rose-500'
                    ]"
                ></div>

                <!-- Icon -->
                <div 
                    :class="[
                        'w-10 h-10 rounded-xl flex items-center justify-center shrink-0',
                        type === 'success' ? 'bg-[#c97e60]/10 text-[#c97e60]' : 'bg-rose-500/10 text-rose-500'
                    ]"
                >
                    <svg v-if="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>

                <!-- Content -->
                <div class="flex-1">
                    <p class="text-xs font-black uppercase tracking-widest text-[#a8a196] mb-0.5">
                        {{ type === 'success' ? 'Success' : 'Attention' }}
                    </p>
                    <p class="text-sm font-bold text-[#161514] dark:text-[#f2e8d5] leading-relaxed">
                        {{ message }}
                    </p>
                </div>

                <!-- Close Button -->
                <button @click="visible = false" class="text-[#a8a196] hover:text-[#c97e60] transition-colors p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Optional: Adding a subtle glow effect */
.border-[#c97e60]\/20 {
    box-shadow: 0 0 40px -10px rgba(201, 126, 96, 0.2);
}
</style>
