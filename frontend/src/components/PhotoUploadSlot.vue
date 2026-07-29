<template>
  <div class="relative border border-dashed rounded p-3 flex flex-col items-center group aspect-square justify-center transition-colors"
       :class="photo ? 'bg-blue-50/30 border-blue-200' : 'bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400'">
    
    <template v-if="photo">
      <span v-if="photo.id" class="absolute top-1 left-1 bg-blue-100 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Gespeichert</span>
      <span v-else class="absolute top-1 left-1 bg-green-100 text-green-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Neu</span>
      
      <button type="button" class="absolute top-1 right-1 rounded-full w-5 h-5 flex items-center justify-center text-xs bg-red-100 text-red-600 hover:bg-red-200 transition-colors" @click.prevent="$emit('remove')" title="Bild löschen">
        &times;
      </button>
      
      <img :src="photo.src" class="flex-1 w-full h-0 object-contain rounded mt-4">
      <div class="mt-2 text-xs font-semibold text-gray-700 w-full text-center">{{ label }}</div>
      
      <!-- Datumsfeld -->
      <div class="w-full mt-2 flex items-center space-x-1 relative">
        <input type="text" v-model="displayDate" @blur="validateAndSetDate" @keyup.enter="validateAndSetDate" placeholder="TT.MM.JJJJ" 
               class="flex-1 min-w-0 text-xs border rounded px-1.5 py-1 focus:outline-none text-center transition-colors"
               :class="photo.is_date_valid === false ? 'border-red-500 text-red-700 focus:border-red-600 bg-red-50' : 'border-gray-300 text-gray-700 focus:border-blue-400 bg-white'" 
               title="Aufnahmedatum (TT.MM.JJJJ)" />
        <div class="relative w-6 h-6 flex-shrink-0 flex items-center justify-center bg-gray-100 border border-gray-300 rounded hover:bg-gray-200 overflow-hidden cursor-pointer">
          <svg class="w-4 h-4 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <input type="date" :value="photo.recorded_at" :max="today" @change="onNativeDateChange" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" title="Kalender öffnen" />
        </div>
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
import { ref, watch, computed } from 'vue';

const props = defineProps<{
  photo: any;
  label: string;
}>();

defineEmits(['upload', 'remove']);

const displayDate = ref('');
const today = computed(() => new Date().toISOString().split('T')[0]!);

const toDisplay = (isoStr?: string) => {
  if (!isoStr) return '';
  const parts = isoStr.split('-');
  if (parts.length === 3) {
    return `${parts[2]}.${parts[1]}.${parts[0]}`;
  }
  return isoStr;
};

watch(() => props.photo?.recorded_at, (newVal) => {
  if (newVal) {
    displayDate.value = toDisplay(newVal);
  }
}, { immediate: true });

const validateAndSetDate = () => {
  if (!props.photo) return;
  
  let d = displayDate.value.trim();
  
  if (!d) {
    props.photo.recorded_at = '';
    props.photo.is_date_valid = false;
    return;
  }
  
  const parts = d.split('.');
  if (parts.length === 3) {
    const isDigits = /^\d+$/.test(parts[0]!) && /^\d+$/.test(parts[1]!) && /^\d+$/.test(parts[2]!);
    if (isDigits) {
        const day = parseInt(parts[0]!, 10);
        const month = parseInt(parts[1]!, 10);
        let year = parseInt(parts[2]!, 10);
        
        if (year < 100) year += 2000;
        
        const inputDate = new Date(year, month - 1, day);
        const todayDate = new Date();
        todayDate.setHours(0,0,0,0);
        
        const isStrictDay = inputDate.getDate() === day && inputDate.getMonth() === month - 1 && inputDate.getFullYear() === year;
        
        if (!isNaN(inputDate.getTime()) && isStrictDay && inputDate <= todayDate && month >= 1 && month <= 12 && day >= 1 && day <= 31) {
            const iso = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            props.photo.recorded_at = iso;
            props.photo.is_date_valid = true;
            displayDate.value = toDisplay(iso);
            return;
        }
    }
  }
  
  if (d.includes('-') && !isNaN(new Date(d).getTime())) {
      const inputDate = new Date(d);
      const todayDate = new Date();
      todayDate.setHours(0,0,0,0);
      if (inputDate <= todayDate) {
          props.photo.recorded_at = d;
          props.photo.is_date_valid = true;
          displayDate.value = toDisplay(d);
          return;
      }
  }
  
  // Invalid
  props.photo.recorded_at = d;
  props.photo.is_date_valid = false;
};

const onNativeDateChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (!props.photo) return;
  
  if (target.value) {
    const inputDate = new Date(target.value);
    const currentDate = new Date(today.value);
    
    if (isNaN(inputDate.getTime()) || inputDate > currentDate) {
      props.photo.recorded_at = target.value;
      props.photo.is_date_valid = false;
    } else {
      props.photo.recorded_at = target.value;
      props.photo.is_date_valid = true;
    }
  } else {
    props.photo.recorded_at = '';
    props.photo.is_date_valid = false;
  }
  displayDate.value = toDisplay(props.photo.recorded_at);
};
</script>
