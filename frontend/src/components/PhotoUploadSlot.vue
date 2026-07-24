<template>
  <div class="relative border border-dashed rounded p-3 flex flex-col items-center group min-h-[140px] justify-center transition-colors"
       :class="photo ? 'bg-blue-50/30 border-blue-200' : 'bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400'">
    
    <template v-if="photo">
      <span v-if="photo.id" class="absolute top-1 left-1 bg-blue-100 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Gespeichert</span>
      <span v-else class="absolute top-1 left-1 bg-green-100 text-green-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Neu</span>
      
      <button type="button" class="absolute top-1 right-1 rounded-full w-5 h-5 flex items-center justify-center text-xs bg-red-100 text-red-600 hover:bg-red-200 transition-colors" @click.prevent="$emit('remove')" title="Bild löschen">
        &times;
      </button>
      
      <img :src="photo.src" class="w-full h-24 object-contain rounded mt-2">
      <div class="mt-2 text-xs font-semibold text-gray-700 w-full text-center">{{ label }}</div>
      
      <!-- Datumsfeld -->
      <div class="w-full mt-2 relative">
        <input type="date" v-model="photo.recorded_at" class="w-full text-[10px] border border-gray-300 rounded px-1 py-1 text-gray-700 focus:outline-none focus:border-blue-400" title="Aufnahmedatum" />
      </div>
    </template>
    
    <template v-else>
      <label class="flex flex-col items-center justify-center cursor-pointer w-full h-full">
        <input type="file" accept="image/*" @change="$emit('upload', $event)" class="hidden">
        <svg class="w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span class="text-xs font-medium text-gray-500 text-center">{{ label }}<br>hinzufügen</span>
      </label>
    </template>

  </div>
</template>

<script setup lang="ts">
defineProps<{
  photo: any;
  label: string;
}>();

defineEmits(['upload', 'remove']);
</script>
