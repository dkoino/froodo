<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex items-center justify-center transition-all focus:outline-none hover:brightness-90 disabled:hover:brightness-100',
            sizeClasses[size] || sizeClasses.md,
            disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
        ]"
    >
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    type?: 'button' | 'submit' | 'reset';
    size?: 'sm' | 'md' | 'lg' | 'none';
    disabled?: boolean;
}>(), {
    type: 'button',
    size: 'md',
    disabled: false
});

const sizeClasses: Record<string, string> = {
    sm: 'px-3 py-1 text-xs',
    md: 'px-4 py-1.5 text-sm font-medium',
    lg: 'px-4 py-2 text-base font-semibold',
    none: ''
};
</script>
