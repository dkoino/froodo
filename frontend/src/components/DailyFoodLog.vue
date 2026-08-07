<template>
  <div class="bg-surface rounded-lg shadow-sm border border-border overflow-hidden mb-6 max-w-max mx-auto">
    <div class="px-6 py-4 border-b border-border bg-surface-muted flex items-center justify-between" v-if="title">
      <h2 class="text-xl font-bold text-content">{{ title }}</h2>
      <button @click="showDetails = !showDetails" class="text-xs font-semibold text-content-muted hover:text-content-secondary transition-colors">
        {{ showDetails ? 'Details ausblenden' : 'Details anzeigen' }}
      </button>
    </div>
    <div v-else class="px-4 py-1.5 border-b border-border bg-surface-muted flex justify-end">
      <button @click="showDetails = !showDetails" class="text-xs font-semibold text-content-muted hover:text-content-secondary transition-colors">
        {{ showDetails ? 'Details ausblenden' : 'Details anzeigen' }}
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="text-left text-xs whitespace-nowrap table-fixed border-collapse">
        <thead>
          <tr class="text-content-muted bg-surface-muted uppercase text-sm tracking-wide">
            <th class="pl-4 pr-2 py-1.5 font-semibold text-xs uppercase w-[255px] border border-border border-r-0">Lebensmittel</th>
            <th class="px-2 py-1.5 text-right font-semibold text-xs uppercase w-[80px] border border-border border-l-0">Menge</th>
            <th class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Kalorien (kcal)</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3s6 6.5 6 10.5a6 6 0 11-12 0C6 9.5 12 3 12 3z" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Fett</div>
            </th>
            <th v-if="showDetails" class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3s6 6.5 6 10.5a6 6 0 11-12 0C6 9.5 12 3 12 3z" />
                <circle cx="12" cy="14" r="1.3" fill="currentColor" stroke="none" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">gesättigte Fette</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21V3M12 6l-3-2M12 6l3-2M12 10l-3-2M12 10l3-2M12 14l-3-2M12 14l3-2" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Kohlenhydrate</div>
            </th>
            <th v-if="showDetails" class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v10l-7 4-7-4V7l7-4z" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Zucker</div>
            </th>
            <th v-if="showDetails" class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 4C10 4 4 10 4 20c10 0 16-6 16-16z M9 15c3-3 6-4 9-9" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Ballaststoffe</div>
            </th>
            <th class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c4 0 7-3.5 7-8 0-5-3-10-7-10S5 8 5 13c0 4.5 3 8 7 8z" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Eiweiß</div>
            </th>
            <th v-if="showDetails" class="px-2 py-1.5 text-center w-[80px] relative group cursor-pointer border border-border">
              <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6l1 11a2 2 0 01-2 2H10a2 2 0 01-2-2L9 8z M10 8V5a2 2 0 012-2 2 2 0 012 2v3 M11 12h.01M13 12h.01M12 15h.01" />
              </svg>
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 hidden group-hover:block bg-surface-inverted text-on-inverted text-[11px] py-1 px-2 rounded whitespace-nowrap z-50 pointer-events-none normal-case tracking-normal shadow-lg">Salz</div>
            </th>
          </tr>
        </thead>
        <tbody class="text-content-secondary">
          <tr v-if="loading" class="animate-pulse">
            <td :colspan="showDetails ? 10 : 6" class="px-2 py-1.5 h-[60px] text-center text-content-subtle border border-border">Lade Einträge...</td>
          </tr>
          <tr v-else-if="logs.length === 0">
            <td :colspan="showDetails ? 10 : 6" class="px-2 py-6 h-[60px] text-center text-content-subtle border border-border">
              {{ emptyMessage || 'Keine Einträge vorhanden.' }}
            </td>
          </tr>
          <tr
            v-else
            v-for="log in logs"
            :key="log.id"
            @click="$emit('edit-log', log)"
            class="hover:bg-surface-muted transition-colors cursor-pointer"
          >
            <td class="pl-4 pr-2 py-1.5 w-[255px] h-[60px] border border-border border-r-0">
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
                  <span @click.stop="openFoodDetail(log.food)" class="truncate text-content-strong font-medium leading-tight text-xs cursor-pointer hover:text-primary transition-colors">{{ log.food.name }}</span>
                  <span v-if="log.food.variant" class="truncate text-content-muted text-[10px] leading-tight mt-0.5">{{ log.food.variant }}</span>
                  <span v-if="log.food.brand" class="truncate text-primary/80 text-[9px] uppercase font-bold leading-tight mt-0.5 tracking-wide">{{ log.food.brand.name }}</span>
                </div>
              </div>
            </td>
            <td class="px-2 py-1.5 text-right w-[80px] h-[60px] border border-border border-l-0">
              <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-primary-soft text-primary-strong font-bold text-[11px]">
                {{ Number(log.amount) }}{{ log.food.measurement_unit }}
              </span>
            </td>
            <td class="px-2 py-1.5 text-center text-content-strong font-medium w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.calories_p100, log.amount, 'kcal') }}</td>
            <td class="px-2 py-1.5 text-center w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.fat_p100, log.amount, 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center text-content-muted w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.sat_fat_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.carbs_p100, log.amount, 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center text-content-muted w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.sugar_p100, log.amount, 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.fiber_p100, log.amount, 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.protein_p100, log.amount, 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center text-content-muted w-[80px] h-[60px] border border-border">{{ formatNutrient(log.food.salt_p100, log.amount, 'g') }}</td>
          </tr>
        </tbody>
        <tfoot v-if="!loading" class="bg-surface-muted font-bold border-t-2 border-border text-content text-[11px]">
          <tr>
            <td class="pl-4 pr-2 py-2 text-left border border-border border-r-0">
              <button @click="$emit('add-log', date)" class="text-xs bg-primary text-on-primary hover:bg-primary-hover font-medium py-1 px-3 rounded shadow-sm transition-colors inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Hinzufügen
              </button>
            </td>
            <td class="px-2 py-1 text-right text-xs border border-border border-l-0"></td>
            <td class="px-2 py-1.5 text-center w-[80px] border border-border">{{ formatTotalNutrient('calories_p100', 'kcal') }}</td>
            <td class="px-2 py-1.5 text-center w-[80px] border border-border">{{ formatTotalNutrient('fat_p100', 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center text-content-muted w-[80px] border border-border">{{ formatTotalNutrient('sat_fat_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[80px] border border-border">{{ formatTotalNutrient('carbs_p100', 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center text-content-muted w-[80px] border border-border">{{ formatTotalNutrient('sugar_p100', 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center font-bold text-content w-[80px] border border-border">{{ formatTotalNutrient('fiber_p100', 'g') }}</td>
            <td class="px-2 py-1.5 text-center w-[80px] border border-border">{{ formatTotalNutrient('protein_p100', 'g') }}</td>
            <td v-if="showDetails" class="px-2 py-1.5 text-center text-content-muted w-[80px] border border-border">{{ formatTotalNutrient('salt_p100', 'g') }}</td>
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

// Sekundäre Nährwerte (gesättigte Fette, Zucker, Ballaststoffe, Salz) sind
// standardmäßig ausgeblendet, um nicht als reine Zahlenreihe zu wirken.
const showDetails = ref(false)

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

defineEmits(['edit-log', 'add-log'])

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
</script>
