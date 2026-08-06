<template>
    <div>
        <label v-if="label" :class="[
            'block text-sm mb-1',
            required ? 'font-bold text-content-strong' : 'font-normal text-content-subtle',
            labelClass
        ]">
            {{ label }}
        </label>
        <input
            ref="inputRef"
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            v-bind="$attrs"
            :required="required"
            class="block w-full border border-border-strong rounded-md px-2 py-1 focus:ring-0 focus:border-border-strong bg-surface disabled:bg-surface-subtle disabled:text-content-muted placeholder:text-content-faint"
        >
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

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

const inputRef = ref<HTMLInputElement | null>(null);

defineExpose({
    focusEnd: () => {
        const el = inputRef.value;
        if (!el) return;
        el.focus();
        el.setSelectionRange(el.value.length, el.value.length);
    }
});
</script>
