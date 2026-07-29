<template>
    <div>
        <label v-if="label" :class="[
            'block text-sm mb-1',
            required ? 'font-bold text-gray-900' : 'font-normal text-gray-400',
            labelClass
        ]">
            {{ label }}
        </label>
        <select
            :value="modelValue"
            @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            v-bind="$attrs"
            :required="required"
            class="block w-full border border-gray-300 rounded-md px-2 py-1 focus:ring-0 focus:border-gray-400 bg-white"
        >
            <slot></slot>
        </select>
    </div>
</template>

<script setup lang="ts">
defineOptions({
  inheritAttrs: false
});

defineProps<{
    modelValue?: string | number | null;
    label?: string;
    labelClass?: string;
    required?: boolean;
}>();

defineEmits<{
    (e: 'update:modelValue', value: string): void
}>();
</script>
