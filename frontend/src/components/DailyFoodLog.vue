<template>
  <div class="bg-surface rounded-lg shadow-sm border border-border overflow-hidden mb-6 max-w-max mx-auto">
    <div class="px-6 py-4 border-b border-border bg-surface-muted" v-if="title">
      <h2 class="text-xl font-bold text-content">{{ title }}</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="text-left text-xs whitespace-nowrap">
        <thead>
          <tr class="border-b border-border text-content-muted bg-surface-muted uppercase text-sm tracking-wide">
            <th class="pl-4 pr-2 py-1.5 font-semibold text-xs uppercase w-[240px]">Lebensmittel</th>
            <th class="px-2 py-1.5 text-right font-semibold text-xs uppercase w-[70px]">Menge</th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">🔥</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Kalorien (kcal)</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">💧</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Fett</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">🧈</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">gesättigte Fette</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">🥖</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Kohlenhydrate</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">🍬</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Zucker</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">🥬</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Ballaststoffe</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">🥩</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Eiweiß</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[70px] relative group cursor-pointer">
              <span class="text-base">🧂</span>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Salz</div>
            </th>
            <th class="px-2 py-1.5 text-center font-semibold text-xs uppercase w-[60px]">Aktion</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border-muted text-content-secondary">
          <tr v-if="loading" class="animate-pulse">
            <td colspan="11" class="px-2 py-1.5 text-center text-content-subtle">Lade Einträge...</td>
          </tr>
          <tr v-else-if="logs.length === 0">
            <td colspan="11" class="px-2 py-6 text-center text-content-subtle">
              {{ emptyMessage || 'Keine Einträge vorhanden.' }}
            </td>
          </tr>
          <tr v-else v-for="log in logs" :key="log.id" class="hover:bg-surface-muted transition-colors">
            <td class="pl-4 pr-2 py-1.5 w-[240px] h-[60px]">
              <div class="flex items-center space-x-3">
                <div 
                  v-if="getPrimaryPhoto(log.food)"
                  class="relative cursor-pointer"
                  @mousemove="updateMousePos($event)"
                  @mouseenter="setHoveredImage(`http://localhost:8000/storage/${getPrimaryPhoto(log.food).file_path}`)"
                  @mouseleave="clearHoveredImage"
                >
                  <img :src="`http://localhost:8000/storage/${getPrimaryPhoto(log.food).file_path}`" class="w-10 h-10 object-cover rounded shadow-sm border border-border" />
                </div>
                <div v-else class="w-10 h-10 bg-surface-subtle rounded flex items-center justify-center text-content-faint border border-border flex-shrink-0">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div class="flex flex-col justify-center h-10 overflow-hidden">
                  <span @click="openFoodDetail(log.food)" class="truncate text-content-strong font-medium leading-tight text-xs cursor-pointer hover:text-primary transition-colors">{{ log.food.name }}</span>
                  <span v-if="log.food.variant" class="truncate text-content-muted text-[10px] leading-tight mt-0.5">{{ log.food.variant }}</span>
                  <span v-if="log.food.brand" class="truncate text-primary/80 text-[9px] uppercase font-bold leading-tight mt-0.5 tracking-wide">{{ log.food.brand.name }}</span>
                </div>
              </div>
            </td>
            <td class="px-2 py-1.5 text-right w-[70px]">
              <span @click="editAmount(log)" class="inline-flex items-center px-1.5 py-0.5 rounded border border-primary-soft-border bg-primary-soft text-primary-strong hover:bg-primary-soft-strong hover:border-primary-soft-border cursor-pointer transition-colors text-[11px]" title="Menge anpassen">
                {{ Number(log.amount) }}{{ log.food.measurement_unit }}
              </span>
            </td>
            <td class="px-2 py-1.5 text-center text-content-strong font-medium w-[70px]">{{ formatNutrient(log.food.calories_p100, log.amount, 'kcal') }}</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatNutrient(log.food.fat_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center text-content-muted w-[70px]">{{ formatNutrient(log.food.sat_fat_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatNutrient(log.food.carbs_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center text-content-muted w-[70px]">{{ formatNutrient(log.food.sugar_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatNutrient(log.food.fiber_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatNutrient(log.food.protein_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center text-content-muted w-[70px]">{{ formatNutrient(log.food.salt_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[60px]">
              <button @click="deleteLog(log.id)" class="text-danger hover:text-danger-hover p-1 rounded hover:bg-danger-soft transition-colors" title="Löschen">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
        <tfoot v-if="!loading" class="bg-surface-muted font-bold border-t-2 border-border text-content text-[11px]">
          <tr>
            <td colspan="1" class="pl-4 pr-2 py-2 text-left">
              <button @click="$emit('add-log', date)" class="text-xs bg-primary text-on-primary hover:bg-primary-hover font-medium py-1 px-3 rounded shadow-sm transition-colors inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Hinzufügen
              </button>
            </td>
            <td class="px-2 py-1.5 text-right text-xs">Summe:</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatTotalNutrient('calories_p100', 'kcal') }}</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatTotalNutrient('fat_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center text-content-muted w-[70px]">{{ formatTotalNutrient('sat_fat_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatTotalNutrient('carbs_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center text-content-muted w-[70px]">{{ formatTotalNutrient('sugar_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center font-bold text-content w-[70px]">{{ formatTotalNutrient('fiber_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[70px]">{{ formatTotalNutrient('protein_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center text-content-muted w-[70px]">{{ formatTotalNutrient('salt_p100', 'g') }}</td>
            <td class="px-2 py-1.5 w-[60px]"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Image Preview Tooltip -->
  <Teleport to="body">
    <div 
      v-if="hoveredImageSrc" 
      class="fixed z-50 pointer-events-none transform -translate-x-1/2 translate-y-4 bg-surface rounded-lg shadow-2xl border-4 border-surface overflow-hidden transition-opacity duration-150"
      :style="{ left: mouseX + 'px', top: mouseY + 'px' }"
    >
      <img :src="hoveredImageSrc" class="w-64 h-64 object-contain" />
    </div>
  </Teleport>
  <FoodDetailModal 
    :isOpen="isDetailModalOpen" 
    :food="selectedFood" 
    @close="isDetailModalOpen = false" 
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import FoodDetailModal from './FoodDetailModal.vue'

const hoveredImageSrc = ref<string | null>(null)
const mouseX = ref(0)
const mouseY = ref(0)

const setHoveredImage = (src: string) => {
  hoveredImageSrc.value = src
}

const clearHoveredImage = () => {
  hoveredImageSrc.value = null
}

const updateMousePos = (e: MouseEvent) => {
  mouseX.value = e.clientX
  mouseY.value = e.clientY
}

const props = defineProps<{
  logs: any[]
  loading?: boolean
  title?: string
  emptyMessage?: string
  date?: string
}>()

const emit = defineEmits(['update-log', 'delete-log', 'add-log'])

const isDetailModalOpen = ref(false)
const selectedFood = ref<any>(null)

const openFoodDetail = (food: any) => {
  selectedFood.value = food
  isDetailModalOpen.value = true
}

const getPrimaryPhoto = (food: any) => {
  if (!food.photos || !Array.isArray(food.photos) || food.photos.length === 0) {
    return null
  }
  return food.photos.find((p: any) => p.type === 'packaging') 
    || food.photos.find((p: any) => p.type === 'content')
    || food.photos.find((p: any) => p.type === 'other') 
    || food.photos[0]
}

const formatNutrient = (per100: number, amount: number, unit: string) => {
  if (per100 === null || per100 === undefined || amount === null) return '-'
  const value = (Number(per100) / 100) * Number(amount)
  
  // The user requested NO rounding to integer/1 decimal, and ALWAYS 2 decimal places. 
  // We use standard float math rounding to 2 decimal places to avoid precision loss.
  return (Math.round(value * 100) / 100).toFixed(2) + unit
}

const formatTotalNutrient = (nutrientKey: string, unit: string) => {
  let total = 0
  let hasValue = false
  for (const log of props.logs) {
    const per100 = log.food[nutrientKey]
    if (per100 !== null && per100 !== undefined && log.amount !== null) {
      total += (Number(per100) / 100) * Number(log.amount)
      hasValue = true
    }
  }
  
  if (!hasValue) return '-'
  return (Math.round(total * 100) / 100).toFixed(2) + unit
}

const editAmount = async (log: any) => {
  const input = prompt(`Menge für ${log.food.name} anpassen (bisher: ${Number(log.amount)} ${log.food.measurement_unit}):`, Number(log.amount).toString())
  if (input === null) return

  const parsed = parseInt(input.replace(',', '.'), 10)
  if (isNaN(parsed) || parsed <= 0) {
    alert('Bitte eine gültige Zahl über 0 eingeben.')
    return
  }

  try {
    const response = await fetch(`http://localhost:8000/api/food-logs/${log.id}`, {
      method: 'PUT',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ amount: parsed })
    })

    if (response.ok) {
      emit('update-log', { id: log.id, amount: parsed })
    } else {
      alert('Fehler beim Aktualisieren der Menge.')
    }
  } catch (error) {
    console.error('Update Error:', error)
  }
}

const deleteLog = async (id: number) => {
  if (!confirm('Eintrag wirklich löschen?')) return
  
  try {
    const response = await fetch(`http://localhost:8000/api/food-logs/${id}`, {
      method: 'DELETE',
      credentials: 'include'
    })
    if (response.ok) {
      emit('delete-log', id)
    } else {
      alert('Fehler beim Löschen.')
    }
  } catch (error) {
    console.error(error)
  }
}
</script>
