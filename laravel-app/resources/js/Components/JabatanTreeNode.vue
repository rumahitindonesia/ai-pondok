<script>
// This component must use Options API style to enable recursive self-reference by name.
// In <script setup>, there's no way to reference the component itself easily.
export default {
    name: 'JabatanTreeNode',
}
</script>

<script setup>
import { defineProps, defineEmits } from 'vue';
import JabatanTreeNode from '@/Components/JabatanTreeNode.vue';

const props = defineProps({
    jabatan: Object,
    depth: {
        type: Number,
        default: 0
    }
});

const emit = defineEmits(['edit', 'delete', 'add-child']);

// Depth-based color accents to visually distinguish levels 
const levelColors = [
    { bg: 'bg-[#d02e5c]', text: 'text-white' },
    { bg: 'bg-[#d02e5c]/15', text: 'text-[#d02e5c]' },
    { bg: 'bg-rose-100 dark:bg-rose-900/30', text: 'text-rose-600 dark:text-rose-400' },
    { bg: 'bg-rose-50 dark:bg-rose-900/20', text: 'text-rose-400 dark:text-rose-500' },
];

const getColor = (depth) => levelColors[Math.min(depth, levelColors.length - 1)];
const dotSizes = ['w-10 h-10 rounded-xl text-base', 'w-8 h-8 rounded-lg text-sm', 'w-6 h-6 rounded-md text-xs', 'w-5 h-5 rounded text-[10px]'];
const dotSize = dotSizes[Math.min(props.depth, dotSizes.length - 1)];
const cardClasses = props.depth === 0
    ? 'bg-[#f5f4f2] dark:bg-[#262524] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-2xl p-5'
    : props.depth === 1
    ? 'bg-white dark:bg-[#1a1918] border border-[#ebeae8] dark:border-[#3e3c3a] rounded-xl p-4'
    : 'bg-white/60 dark:bg-[#1d1c1b]/60 border border-[#ebeae8]/60 dark:border-[#3e3c3a]/60 rounded-lg p-3';
</script>

<template>
    <!-- Node Card -->
    <div :class="['relative flex items-center justify-between group', cardClasses]">
        <div class="flex items-center gap-3">
            <!-- Level badge -->
            <div :class="['shrink-0 flex items-center justify-center font-black', dotSize, getColor(depth).bg, getColor(depth).text]">
                {{ depth }}
            </div>
            <div>
                <h4 :class="['font-bold text-[#161514] dark:text-[#f2e8d5]', depth === 0 ? 'text-base' : depth === 1 ? 'text-sm' : 'text-xs']">
                    {{ jabatan.nama }}
                </h4>
                <p v-if="jabatan.deskripsi" class="text-[10px] text-[#a8a196] mt-0.5 leading-tight">{{ jabatan.deskripsi }}</p>
            </div>
        </div>

        <!-- Action buttons (visible on hover) -->
        <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
            <button @click="emit('add-child', jabatan.id)"
                title="Tambah Bawahan"
                class="p-1.5 text-[#a8a196] hover:text-[#d02e5c] bg-white dark:bg-[#1a1918] rounded-lg border border-[#ebeae8] dark:border-[#3e3c3a] transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            </button>
            <button @click="emit('edit', jabatan)"
                class="p-1.5 text-[#a8a196] hover:text-[#d02e5c] bg-white dark:bg-[#1a1918] rounded-lg border border-[#ebeae8] dark:border-[#3e3c3a] transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            </button>
            <button @click="emit('delete', jabatan.id)"
                class="p-1.5 text-[#a8a196] hover:text-rose-500 bg-white dark:bg-[#1a1918] rounded-lg border border-[#ebeae8] dark:border-[#3e3c3a] transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        </div>
    </div>

    <!-- Recursively render children with indent + connector line -->
    <div
        v-if="jabatan.children_recursive?.length > 0"
        class="pl-10 mt-2 mb-2 space-y-2 relative border-l-2 border-[#ebeae8] dark:border-[#3e3c3a] ml-5">
        <div v-for="child in jabatan.children_recursive" :key="child.id" class="relative before:absolute before:w-5 before:h-0.5 before:bg-[#ebeae8] dark:before:bg-[#3e3c3a] before:-left-5 before:top-[50%]">
            <JabatanTreeNode
                :jabatan="child"
                :depth="depth + 1"
                @edit="(j) => emit('edit', j)"
                @delete="(id) => emit('delete', id)"
                @add-child="(id) => emit('add-child', id)"
            />
        </div>
    </div>
</template>
