<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="relative group">
        <select
            class="w-full bg-[#fcf8f5] border-[#ebeae8] rounded-2xl p-4 pr-10 text-[#161514] focus:ring-4 focus:ring-[#d02e5c]/10 focus:border-[#d02e5c] transition-all outline-none shadow-sm shadow-black/5 dark:bg-[#161514] dark:border-[#383736] dark:text-[#f2e8d5] appearance-none cursor-pointer font-bold text-sm"
            v-model="model"
            ref="input"
        >
            <slot />
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#a8a196] group-hover:text-[#d02e5c] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</template>
