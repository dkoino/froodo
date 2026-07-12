<template>
  <div v-if="isOpen && food" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
        <div>
          <h3 class="text-xl font-bold text-gray-800">{{ food.name }}</h3>
          <p v-if="food.variant" class="text-sm text-gray-500 mt-0.5">{{ food.variant }}</p>
        </div>
        <BaseButton size="none" class="text-xl font-bold text-gray-400 hover:text-gray-600" @click="$emit('close')">
          &times;
        </BaseButton>
      </div>

      <div class="p-6 space-y-6">
        
        <div class="bg-gray-50 p-4 rounded-md border border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
          <div class="col-span-2">
            <span class="block text-gray-400 text-xs font-medium uppercase">Marke & Hersteller</span>
            <span class="text-gray-800 font-semibold">
              <span v-if="food.brand" class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Marke', food.brand.name)">{{ food.brand.name }}</span>
              <span v-else>-</span>
              <span v-if="food.brand?.manufacturer" class="text-gray-500 font-normal"> &gt; <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Hersteller', food.brand.manufacturer.name)">{{ food.brand.manufacturer.name }}</span></span>
            </span>
          </div>
          <div>
            <span class="block text-gray-400 text-xs font-medium uppercase">Kategorie</span>
            <span class="text-gray-800 font-semibold">
              <span v-if="food.main_category" class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Kategorie', food.main_category.name)">{{ food.main_category.name }}</span>
              <span v-else>-</span>
              <span v-if="food.sub_category" class="text-gray-500 font-normal"> &gt; <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Kategorie', food.sub_category.name)">{{ food.sub_category.name }}</span></span>
            </span>
          </div>
          <div>
            <span class="block text-gray-400 text-xs font-medium uppercase">Zustand</span>
            <span class="text-gray-800 font-semibold" :class="{'cursor-pointer hover:text-blue-600 transition-colors': food.state}" @click="emitSearch('Zustand', food.state)">{{ food.state || '-' }}</span>
          </div>
          <div>
            <span class="block text-gray-400 text-xs font-medium uppercase">Gesamtmenge</span>
            <span class="text-gray-800 font-semibold">{{ food.total_amount }} {{ food.measurement_unit }}</span>
          </div>
          <div>
            <span class="block text-gray-400 text-xs font-medium uppercase">Fleischsorte</span>
            <span class="text-gray-800 font-semibold" :class="{'cursor-pointer hover:text-blue-600 transition-colors': food.meat_type && food.meat_type !== 'Unbekannt'}" @click="emitSearch('Fleisch', food.meat_type && food.meat_type !== 'Unbekannt' ? food.meat_type : null)">{{ food.meat_type && food.meat_type !== 'Unbekannt' ? food.meat_type : '-' }}</span>
          </div>
          <div>
            <span class="block text-gray-400 text-xs font-medium uppercase">Preis</span>
            <span class="text-gray-800 font-semibold">{{ food.price ? `${food.price} €` : '-' }}</span>
          </div>
          <div>
            <span class="block text-gray-400 text-xs font-medium uppercase">Barcode (EAN)</span>
            <span class="text-gray-800 font-semibold" :class="{'cursor-pointer hover:text-blue-600 transition-colors': food.barcode}" @click="emitSearch('Barcode', food.barcode)">{{ food.barcode || '-' }}</span>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-6">
            <div>
              <h3 class="text-sm font-bold tracking-wider text-gray-700 uppercase mb-4">
                Nährwerte pro 100 {{ food?.measurement_unit || 'g' }}
              </h3>

              <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                <table class="min-w-full text-sm text-left">
                  <tbody class="divide-y divide-gray-100">

                    <tr class="bg-blue-50">
                      <th class="px-4 py-2 font-bold text-blue-900">Kalorien</th>
                      <td class="px-4 py-2 text-right font-bold text-blue-900">{{ food?.calories_p100 ?? '-' }} kcal
                      </td>
                    </tr>

                    <tr class="bg-amber-100">
                      <th class="px-4 py-2 font-medium text-amber-900">Fett</th>
                      <td class="px-4 py-2 text-right font-bold text-amber-900">{{ food?.fat_p100 ?? '-' }} g</td>
                    </tr>
                    <tr class="bg-amber-50/50">
                      <th class="px-4 py-1 font-normal text-amber-700/80 pl-8">davon gesättigte Fettsäuren</th>
                      <td class="px-4 py-1 text-right text-amber-700/80">{{ food?.sat_fat_p100 ?? '-' }} g</td>
                    </tr>

                    <tr class="bg-purple-100">
                      <th class="px-4 py-2 font-medium text-purple-900">Kohlenhydrate</th>
                      <td class="px-4 py-2 text-right font-bold text-purple-900">{{ food?.carbs_p100 ?? '-' }} g</td>
                    </tr>
                    <tr class="bg-purple-50/50">
                      <th class="px-4 py-1 font-normal text-purple-700/80 pl-8">davon Zucker</th>
                      <td class="px-4 py-1 text-right text-purple-700/80">{{ food?.sugar_p100 ?? '-' }} g</td>
                    </tr>

                    <tr class="bg-green-50">
                      <th class="px-4 py-2 font-medium text-green-900">Protein</th>
                      <td class="px-4 py-2 text-right font-bold text-green-900">{{ food?.protein_p100 ?? '-' }} g</td>
                    </tr>

                    <tr class="bg-gray-50">
                      <th class="px-4 py-2 font-medium text-gray-700">Ballaststoffe</th>
                      <td class="px-4 py-2 text-right text-gray-700">{{ food?.fiber_p100 ?? '-' }} g</td>
                    </tr>
                    <tr class="bg-white">
                      <th class="px-4 py-2 font-medium text-gray-700">Salz</th>
                      <td class="px-4 py-2 text-right text-gray-700">{{ food?.salt_p100 ?? '-' }} g</td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Bilder-Galerie</h4>
            <div v-if="food.photos && food.photos.length > 0" class="space-y-3">
              <div v-for="photo in food.photos" :key="photo.id" class="border border-gray-200 rounded p-2 bg-white">
                <img :src="`http://localhost:8000/storage/${photo.file_path}`"
                  class="w-full h-32 object-contain rounded bg-gray-50">
                <span
                  class="block text-center text-xs font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">
                  {{ translateType(photo.type) }}
                </span>
              </div>
            </div>
            <div v-else class="text-center p-8 bg-gray-50 border border-gray-100 rounded text-gray-400 text-sm">
              Keine Bilder vorhanden.
            </div>
          </div>
        </div>

        <div v-if="food.notes" class="text-sm bg-gray-50 p-4 rounded border border-gray-100">
          <span class="block text-gray-400 text-xs font-medium uppercase mb-1">Notizen</span>
          <p class="text-gray-700 whitespace-pre-line">{{ food.notes }}</p>
        </div>
      </div>

      <div class="px-6 py-4 border-t border-gray-100 flex justify-between space-x-3 bg-gray-50 sticky bottom-0">
        <BaseButton class="bg-red-600 text-white shadow-sm rounded-md" @click="handleDelete">
          Löschen
        </BaseButton>
        <div class="flex space-x-3">
          <BaseButton class="bg-amber-600 text-white shadow-sm rounded-md" @click="goToEdit">
            Bearbeiten
          </BaseButton>
          <BaseButton class="bg-white text-gray-700 border border-gray-300 shadow-sm rounded-md" @click="$emit('close')">
            Schließen
          </BaseButton>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import BaseButton from './BaseButton.vue'

const props = defineProps<{
  isOpen: boolean
  food: any
}>()

const emit = defineEmits(['close', 'delete', 'search'])

const emitSearch = (prefix: string, value: any) => {
  if (!value || value === '-') return
  emit('search', `${prefix}:${value}`)
}

const handleDelete = () => {
  emit('delete', props.food.id)
}
const router = useRouter()

const translateType = (type: string) => {
  const mapping: Record<string, string> = {
    front: 'Vorderseite',
    product: 'Produktbild',
    nutrition: 'Nährwerte',
    ingredients: 'Zutaten',
    barcode: 'Barcode',
    general: 'Allgemein'
  }
  return mapping[type] || type
}

const goToEdit = () => {
  emit('close')
  router.push(`/foods/${props.food.id}/edit`)
}
</script>