<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex items-center justify-center transition-colors focus:outline-none',
            variantClasses[variant] || variantClasses.primary,
            sizeClasses[size] || sizeClasses.md,
            disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
            customClass
        ]"
        v-bind="$attrs"
    >
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
defineOptions({
  inheritAttrs: false
});

withDefaults(defineProps<{
    type?: 'button' | 'submit' | 'reset';
    variant?: 'primary' | 'primary-blue' | 'secondary' | 'secondary-gray' | 'danger' | 'warning' | 'text' | 'text-blue' | 'text-red' | 'icon' | 'danger-icon';
    size?: 'sm' | 'md' | 'lg' | 'none';
    disabled?: boolean;
    customClass?: string;
}>(), {
    type: 'button',
    variant: 'primary',
    size: 'md',
    disabled: false,
    customClass: ''
});

const variantClasses: Record<string, string> = {
    primary: 'bg-gray-800 hover:bg-gray-900 text-white shadow-sm rounded-md',
    'primary-blue': 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm rounded-md focus:ring-2 focus:ring-blue-500 disabled:bg-blue-400',
    secondary: 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm rounded-md',
    'secondary-gray': 'bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 rounded-md',
    danger: 'bg-red-600 hover:bg-red-700 text-white shadow-sm rounded-md',
    warning: 'bg-amber-600 hover:bg-amber-700 text-white shadow-sm rounded-md',
    text: 'text-gray-500 hover:text-gray-700 bg-transparent',
    'text-blue': 'text-blue-600 hover:text-blue-800 bg-transparent',
    'text-red': 'text-red-600 hover:text-red-800 bg-transparent',
    icon: 'text-gray-400 hover:text-gray-600 bg-transparent',
    'danger-icon': 'bg-red-100 text-red-600 hover:bg-red-200'
};

const sizeClasses: Record<string, string> = {
    sm: 'px-3 py-1.5 text-xs font-medium',
    md: 'px-4 py-2 text-sm font-medium',
    lg: 'px-4 py-2.5 text-base font-semibold',
    none: ''
};
</script>
