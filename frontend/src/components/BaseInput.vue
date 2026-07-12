<template>
    <div>
        <label v-if="label" :class="[
            'block text-sm mb-1',
            required ? 'font-semibold text-gray' : 'font-medium text-gray-500',
            labelClass
        ]">
            {{ label }}
        </label>
        <input
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            v-bind="$attrs"
            :required="required"
            class="block w-full border border-gray-300 rounded-md p-1.5 focus:ring-0 focus:border-gray-400 bg-white disabled:bg-gray-100 disabled:text-gray-500 placeholder:text-gray-300"
        >
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
