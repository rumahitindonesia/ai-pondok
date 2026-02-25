<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    as: {
        type: String,
        default: 'button'
    },
    href: {
        type: String,
        default: null
    },
    variant: {
        type: String,
        default: 'primary' // primary, secondary, outline, danger, ghost
    },
    type: {
        type: String,
        default: 'submit'
    },
    size: {
        type: String,
        default: 'md' // sm, md, lg
    }
});

const isLink = computed(() => props.as === 'Link' || !!props.href);

const baseClasses = 'inline-flex items-center justify-center gap-2 transition-all transform active:scale-95 shrink-0 font-bold';

const variantClasses = {
    primary: 'bg-[#c97e60] text-white hover:bg-[#b06a4f] shadow-lg shadow-[#c97e60]/20',
    secondary: 'bg-[#161514] text-white hover:bg-black shadow-xl',
    outline: 'bg-white dark:bg-[#161514] text-[#161514] dark:text-[#f2e8d5] border border-[#ebeae8] dark:border-[#3e3c3a] hover:bg-gray-50 dark:hover:bg-[#1d1c1b] shadow-sm',
    danger: 'bg-rose-500 text-white hover:bg-rose-600 shadow-lg shadow-rose-500/20',
    ghost: 'p-2 text-[#a8a196] hover:text-[#c97e60] hover:bg-[#c97e60]/10 rounded-lg'
};

const sizeClasses = {
    sm: 'px-6 py-2 text-xs rounded-xl',
    md: 'px-8 py-4 text-sm rounded-[24px]',
    lg: 'px-10 py-5 text-base rounded-[32px]'
};

const classes = computed(() => {
    let cls = `${baseClasses} ${variantClasses[props.variant]}`;
    if (props.variant !== 'ghost') {
        cls += ` ${sizeClasses[props.size]}`;
    }
    return cls;
});
</script>

<template>
    <component
        :is="isLink ? Link : as"
        :href="href"
        :type="!isLink ? type : null"
        :class="classes"
    >
        <div v-if="$slots.icon" class="shrink-0 flex items-center justify-center">
            <slot name="icon" />
        </div>
        <span v-if="$slots.default" class="hidden md:inline">
            <slot />
        </span>
    </component>
</template>
