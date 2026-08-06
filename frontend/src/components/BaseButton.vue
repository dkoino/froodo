<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex items-center justify-center transition-all focus:outline-none hover:brightness-90 disabled:hover:brightness-100',
            variantClasses[variant] || variantClasses.none,
            sizeClasses[size] || sizeClasses.md,
            disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
        ]"
    >
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
type Variant =
    | 'primary'
    | 'secondary'
    | 'neutral'
    | 'danger'
    | 'danger-soft'
    | 'warning'
    | 'inverted'
    | 'ghost'
    | 'none';

withDefaults(defineProps<{
    type?: 'button' | 'submit' | 'reset';
    size?: 'sm' | 'md' | 'lg' | 'none';
    variant?: Variant;
    disabled?: boolean;
}>(), {
    type: 'button',
    size: 'md',
    variant: 'none',
    disabled: false
});

const sizeClasses: Record<string, string> = {
    sm: 'px-3 py-1 text-xs',
    md: 'px-4 py-1.5 text-sm font-medium',
    lg: 'px-4 py-2 text-base font-semibold',
    none: ''
};

const variantClasses: Record<string, string> = {
    primary: 'bg-primary text-on-primary hover:bg-primary-hover border border-transparent shadow-sm',
    secondary: 'bg-surface text-content-secondary border border-border-strong hover:bg-surface-muted shadow-sm',
    neutral: 'bg-surface-subtle text-content-secondary border border-border-strong shadow-sm',
    danger: 'bg-danger text-on-danger hover:bg-danger-hover border border-transparent shadow-sm',
    'danger-soft': 'bg-danger-soft text-danger border border-danger-soft-border hover:bg-danger-soft-hover shadow-sm',
    warning: 'bg-warning text-on-warning border border-transparent shadow-sm',
    inverted: 'bg-surface-inverted text-on-inverted hover:bg-surface-inverted-hover border border-transparent shadow-sm',
    ghost: 'bg-transparent text-content-muted hover:text-content-secondary border border-transparent',
    none: ''
};
</script>
