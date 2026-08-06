<template>
    <div>
        <label v-if="label" :class="[
            'block text-sm mb-1',
            required ? 'font-bold text-content-strong' : 'font-normal text-content-subtle',
            labelClass
        ]">
            {{ label }}
        </label>
        <select
            :value="modelValue"
            @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            v-bind="$attrs"
            :required="required"
            class="block w-full border border-border-strong rounded-md px-2 py-1 focus:ring-0 focus:border-border-strong bg-surface"
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
