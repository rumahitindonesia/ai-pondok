<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-[calc(100%-16px)] mx-2 px-4 py-2.5 text-start text-sm font-bold tracking-tight rounded-xl transition-all duration-200 text-[#4e4b47] dark:text-[#a8a196] hover:bg-brand-rose/5 hover:text-brand-rose dark:hover:bg-brand-rose/10 dark:hover:text-brand-rose-vibrant focus:outline-none"
    >
        <slot />
    </Link>
</template>
