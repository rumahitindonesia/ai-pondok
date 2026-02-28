<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
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
    <input
        class="w-full bg-[#fcf8f5] border-[#ebeae8] rounded-2xl p-4 text-[#161514] focus:ring-4 focus:ring-[#c97e60]/10 focus:border-[#c97e60] transition-all outline-none shadow-sm shadow-black/5 dark:bg-[#161514] dark:border-[#383736] dark:text-[#f2e8d5]"
        v-model="model"
        ref="input"
    />
</template>
