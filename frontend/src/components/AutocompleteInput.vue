<template>
    <div class="relative" ref="containerRef">
        <label v-if="label" :class="[
            'block text-sm mb-1',
            required ? 'font-bold text-content-strong' : 'font-normal text-content-subtle',
            labelClass
        ]">
            {{ label }}
        </label>
        <div class="flex" :class="{ 'space-x-2': !!$slots.action }">
            <div class="relative flex-1">
                <input 
                    :value="modelValue"
                    @input="handleInput"
                    @focus="showDropdown = true"
                    v-bind="$attrs"
                    :required="required"
                    type="text"
                    class="block w-full border border-border-strong rounded-md px-2 py-1 focus:ring-0 focus:border-border-strong bg-surface placeholder:text-content-faint"
                >
                <ul v-if="showDropdown && items.length > 0"
                    class="absolute z-10 w-full mt-1 bg-surface border border-border-strong rounded-md shadow-lg max-h-48 overflow-y-auto">
                    <li v-for="item in items" :key="item.id || item.name" @click="selectItem(item)"
                        class="px-4 py-2 cursor-pointer hover:bg-surface-subtle text-content-secondary">
                        <slot name="item" :item="item">
                            {{ item.name }}
                        </slot>
                    </li>
                </ul>
            </div>
            <slot name="action"></slot>
        </div>
        <slot name="feedback"></slot>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, useSlots } from 'vue';

defineOptions({
  inheritAttrs: false
});

const props = defineProps<{
    modelValue?: string | null;
    label?: string;
    labelClass?: string;
    required?: boolean;
    items: any[];
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'select', item: any): void;
}>();

const showDropdown = ref(false);
const containerRef = ref<HTMLElement | null>(null);

const handleInput = (e: Event) => {
    emit('update:modelValue', (e.target as HTMLInputElement).value);
    showDropdown.value = true;
};

const selectItem = (item: any) => {
    emit('select', item);
    showDropdown.value = false;
};

const closeDropdown = (e: MouseEvent) => {
    if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});
</script>
