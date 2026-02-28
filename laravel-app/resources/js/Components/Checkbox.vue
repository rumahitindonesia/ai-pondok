<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded-lg border-[#ebeae8] dark:border-[#383736] text-brand-rose shadow-sm focus:ring-brand-rose focus:ring-offset-2 dark:focus:ring-offset-[#161514] dark:bg-[#161514] transition-all cursor-pointer"
    />
</template>
