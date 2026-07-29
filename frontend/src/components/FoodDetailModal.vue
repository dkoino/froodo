<template>
  <div v-if="isOpen && displayFood" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto relative">
      <div v-if="loading" class="absolute inset-0 bg-white/50 flex items-center justify-center z-20">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
        <div>
          <h3 class="text-xl font-bold text-gray-800">{{ displayFood.name }}</h3>
          <p v-if="displayFood.variant" class="text-sm text-gray-500 mt-0.5">{{ displayFood.variant }}</p>
        </div>
        <BaseButton size="none" class="text-xl font-bold text-gray-400 hover:text-gray-600" @click="$emit('close')">
          &times;
        </BaseButton>
      </div>

      <div class="p-6 space-y-8">
        
        <!-- 1. Allgemein -->
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Allgemein</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
              <div v-if="displayFood.brand" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm text-sm">
                <span class="block text-gray-400 text-xs font-medium uppercase">Marke & Hersteller</span>
                <span class="text-gray-800 font-semibold">
                  <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Marke', displayFood.brand.name)">{{ displayFood.brand.name }}</span>
                  <span v-if="displayFood.brand?.manufacturer" class="text-gray-500 font-normal"> &gt; <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Hersteller', displayFood.brand.manufacturer.name)">{{ displayFood.brand.manufacturer.name }}</span></span>
                </span>
              </div>
            </div>
            <div class="flex items-start justify-center pt-2">
              <div v-if="packagingPhoto" class="border border-gray-200 rounded p-2 bg-white w-40 max-w-full">
                <a :href="`http://localhost:8000/storage/${packagingPhoto.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${packagingPhoto.file_path}`" class="w-full aspect-square object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">Verpackung</span>
              </div>
              <div v-else class="w-40 max-w-full aspect-square border border-dashed border-gray-300 rounded flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Kategorisierung & Zutaten -->
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Kategorisierung & Zutaten</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3 text-sm">
              <div v-if="displayFood.main_category" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                <span class="block text-gray-400 text-xs font-medium uppercase">Kategorie</span>
                <span class="text-gray-800 font-semibold">
                  <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Kategorie', displayFood.main_category.name)">{{ displayFood.main_category.name }}</span>
                  <span v-if="displayFood.sub_category" class="text-gray-500 font-normal"> &gt; <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Kategorie', displayFood.sub_category.name)">{{ displayFood.sub_category.name }}</span></span>
                </span>
              </div>
              <div v-if="displayFood.state || (displayFood.meat_type && displayFood.meat_type !== 'Unbekannt')" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm space-y-2">
                <div v-if="displayFood.meat_type && displayFood.meat_type !== 'Unbekannt'">
                  <span class="block text-gray-400 text-xs font-medium uppercase">Fleischsorte</span>
                  <span class="text-gray-800 font-semibold cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Fleisch', displayFood.meat_type)">{{ displayFood.meat_type }}</span>
                </div>
                <div v-if="displayFood.state">
                  <span class="block text-gray-400 text-xs font-medium uppercase">Zustand</span>
                  <span class="text-gray-800 font-semibold cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Zustand', displayFood.state)">{{ displayFood.state }}</span>
                </div>
              </div>
            </div>
            <div class="flex items-start justify-center pt-2">
              <div v-if="ingredientsPhoto" class="border border-gray-200 rounded p-2 bg-white w-40 max-w-full">
                <a :href="`http://localhost:8000/storage/${ingredientsPhoto.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${ingredientsPhoto.file_path}`" class="w-full aspect-square object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">Zutatenliste</span>
              </div>
              <div v-else class="w-40 max-w-full aspect-square border border-dashed border-gray-300 rounded flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Menge & Portion -->
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Menge & Portion</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3 text-sm">
              <div v-if="displayFood.total_amount" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                <span class="block text-gray-400 text-xs font-medium uppercase">Gesamtmenge</span>
                <span class="text-gray-800 font-semibold">{{ displayFood.total_amount }} {{ displayFood.measurement_unit }}</span>
              </div>
              <div v-if="displayFood.portion_amount" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                <span class="block text-gray-400 text-xs font-medium uppercase">Portion ({{ displayFood.portion_label || 'Stück' }})</span>
                <span class="text-gray-800 font-semibold">{{ displayFood.portion_amount }} {{ displayFood.measurement_unit }}</span>
              </div>
            </div>
            <div class="flex items-start justify-center pt-2">
              <div v-if="contentPhoto" class="border border-gray-200 rounded p-2 bg-white w-40 max-w-full">
                <a :href="`http://localhost:8000/storage/${contentPhoto.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${contentPhoto.file_path}`" class="w-full aspect-square object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">Essen selbst</span>
              </div>
              <div v-else class="w-40 max-w-full aspect-square border border-dashed border-gray-300 rounded flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. Nährwerte pro 100g -->
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Nährwerte pro 100{{ displayFood?.measurement_unit || 'g' }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
              <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                <table class="min-w-full text-sm text-left">
                  <tbody class="divide-y divide-gray-100">
                    <tr v-if="displayFood?.calories_p100 !== null && displayFood?.calories_p100 !== undefined" class="bg-blue-50">
                      <th class="px-4 py-2 font-bold text-blue-900">Kalorien</th>
                      <td class="px-4 py-2 text-right font-bold text-blue-900">{{ displayFood.calories_p100 }} kcal</td>
                    </tr>
                    <tr v-if="displayFood?.fat_p100 !== null && displayFood?.fat_p100 !== undefined" class="bg-amber-100">
                      <th class="px-4 py-2 font-medium text-amber-900">Fett</th>
                      <td class="px-4 py-2 text-right font-bold text-amber-900">{{ displayFood.fat_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.sat_fat_p100 !== null && displayFood?.sat_fat_p100 !== undefined" class="bg-amber-50/50">
                      <th class="px-4 py-1 font-normal text-amber-700/80 pl-8">davon gesättigt</th>
                      <td class="px-4 py-1 text-right text-amber-700/80">{{ displayFood.sat_fat_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.carbs_p100 !== null && displayFood?.carbs_p100 !== undefined" class="bg-purple-100">
                      <th class="px-4 py-2 font-medium text-purple-900">Kohlenhydrate</th>
                      <td class="px-4 py-2 text-right font-bold text-purple-900">{{ displayFood.carbs_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.sugar_p100 !== null && displayFood?.sugar_p100 !== undefined" class="bg-purple-50/50">
                      <th class="px-4 py-1 font-normal text-purple-700/80 pl-8">davon Zucker</th>
                      <td class="px-4 py-1 text-right text-purple-700/80">{{ displayFood.sugar_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.fiber_p100 !== null && displayFood?.fiber_p100 !== undefined" class="bg-gray-50">
                      <th class="px-4 py-2 font-medium text-gray-700">Ballaststoffe</th>
                      <td class="px-4 py-2 text-right text-gray-700">{{ displayFood.fiber_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.protein_p100 !== null && displayFood?.protein_p100 !== undefined" class="bg-green-50">
                      <th class="px-4 py-2 font-medium text-green-900">Protein</th>
                      <td class="px-4 py-2 text-right font-bold text-green-900">{{ displayFood.protein_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.salt_p100 !== null && displayFood?.salt_p100 !== undefined" class="bg-white">
                      <th class="px-4 py-2 font-medium text-gray-700">Salz</th>
                      <td class="px-4 py-2 text-right text-gray-700">{{ displayFood.salt_p100 }} g</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="flex items-start justify-center pt-2">
              <div v-if="nutritionPhoto" class="border border-gray-200 rounded p-2 bg-white w-40 max-w-full">
                <a :href="`http://localhost:8000/storage/${nutritionPhoto.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${nutritionPhoto.file_path}`" class="w-full aspect-square object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">Nährwert-Tabelle</span>
              </div>
              <div v-else class="w-40 max-w-full aspect-square border border-dashed border-gray-300 rounded flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 5. Zusätzliche Informationen -->
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Zusätzliche Informationen</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3 text-sm">
              <div v-if="displayFood.barcode" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                <span class="block text-gray-400 text-xs font-medium uppercase">Barcode (EAN)</span>
                <span class="text-gray-800 font-semibold cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Barcode', displayFood.barcode)">{{ displayFood.barcode }}</span>
              </div>
              
              <div v-if="displayFood.price" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                <span class="block text-gray-400 text-xs font-medium uppercase">Preis</span>
                <span class="text-gray-800 font-semibold">{{ displayFood.price }} €</span>
              </div>

              <div v-if="displayFood.source_url || displayFood.source_type" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm space-y-2">
                <div v-if="displayFood.source_type">
                  <span class="block text-gray-400 text-xs font-medium uppercase">Datenquelle</span>
                  <span class="text-gray-800 font-semibold">{{ displayFood.source_type }}</span>
                </div>
                <div v-if="displayFood.source_url">
                  <span class="block text-gray-400 text-xs font-medium uppercase">Quellen-Link</span>
                  <a :href="displayFood.source_url" target="_blank" class="text-blue-600 hover:underline font-medium break-all text-xs">{{ displayFood.source_url }}</a>
                </div>
              </div>

              <div v-if="displayFood.notes || otherPhotos.length > 0" class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                <div v-if="displayFood.notes" class="mb-4">
                  <span class="block text-gray-400 text-xs font-medium uppercase mb-1">Notizen</span>
                  <p class="text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded border border-gray-100">{{ displayFood.notes }}</p>
                </div>
                <div v-if="otherPhotos.length > 0">
                  <span class="block text-gray-400 text-xs font-medium uppercase mb-2">Weitere Fotos</span>
                  <div class="flex gap-2 flex-wrap">
                    <div v-for="photo in otherPhotos" :key="photo.id" class="border border-gray-200 rounded p-1 bg-white w-20 aspect-square">
                      <a :href="`http://localhost:8000/storage/${photo.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity w-full h-full">
                        <img :src="`http://localhost:8000/storage/${photo.file_path}`" class="w-full h-full object-cover rounded bg-gray-50">
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-start justify-center pt-2">
              <div v-if="barcodePhoto" class="border border-gray-200 rounded p-2 bg-white w-40 max-w-full">
                <a :href="`http://localhost:8000/storage/${barcodePhoto.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${barcodePhoto.file_path}`" class="w-full aspect-square object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">Barcode</span>
              </div>
              <div v-else class="w-40 max-w-full aspect-square border border-dashed border-gray-300 rounded flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="displayFood.creator" class="text-xs text-gray-400 text-right pt-4 border-t border-gray-100 italic space-y-1">
          <div>
            Angelegt von
            <span class="cursor-pointer hover:text-gray-600 transition-colors" @click="emitSearch('Nutzer', displayFood.creator.name)">
              {{ displayFood.creator.name }}
            </span>
            am {{ formatDate(displayFood.created_at) }}
          </div>
          <div v-if="displayFood.updated_at && displayFood.updater">
            Zuletzt geändert von
            <span class="cursor-pointer hover:text-gray-600 transition-colors" @click="emitSearch('Nutzer', displayFood.updater.name)">
              {{ displayFood.updater.name }}
            </span>
            am {{ formatDate(displayFood.updated_at) }}
          </div>
        </div>
      </div>

      <div class="px-6 py-4 border-t border-gray-100 flex justify-between space-x-3 bg-gray-50 sticky bottom-0">
        <div>
          <BaseButton v-if="canEditOrDelete" class="bg-red-600 text-white shadow-sm rounded-md" @click="handleDelete">
            Löschen
          </BaseButton>
        </div>
        <div class="flex space-x-3">
          <BaseButton v-if="canEditOrDelete" class="bg-amber-600 text-white shadow-sm rounded-md" @click="goToEdit">
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
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from './BaseButton.vue'

const props = defineProps<{
  isOpen: boolean
  food: any
}>()

const emit = defineEmits(['close', 'delete', 'search'])

const fetchedFood = ref<any>(null)
const loading = ref(false)
const currentUser = ref<any>(null)

const fetchCurrentUser = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/me', {
      credentials: 'include',
      headers: { 'Accept': 'application/json' }
    })
    if (response.ok) {
      currentUser.value = await response.json()
    } else {
      currentUser.value = null
    }
  } catch (e) {
    currentUser.value = null
  }
}

const displayFood = computed(() => fetchedFood.value || props.food)

const packagingPhoto = computed(() => displayFood.value?.photos?.find((p: any) => p.type === 'packaging') || null)
const ingredientsPhoto = computed(() => displayFood.value?.photos?.find((p: any) => p.type === 'ingredients') || null)
const contentPhoto = computed(() => displayFood.value?.photos?.find((p: any) => p.type === 'content') || null)
const nutritionPhoto = computed(() => displayFood.value?.photos?.find((p: any) => p.type === 'nutrition') || null)
const barcodePhoto = computed(() => displayFood.value?.photos?.find((p: any) => p.type === 'barcode') || null)
const otherPhotos = computed(() => displayFood.value?.photos?.filter((p: any) => p.type === 'other') || [])

const canEditOrDelete = computed(() => {
  if (!currentUser.value) return false;
  if (currentUser.value.is_admin) return true;
  return currentUser.value.id === displayFood.value?.created_by;
})

watch(() => props.isOpen, async (newVal) => {
  if (newVal && props.food?.id) {
    loading.value = true
    try {
      const response = await fetch(`http://localhost:8000/api/foods/${props.food.id}`, {
        credentials: 'include'
      })
      if (response.ok) {
        fetchedFood.value = await response.json()
      } else {
        fetchedFood.value = null
      }
    } catch (e) {
      console.error(e)
      fetchedFood.value = null
    } finally {
      loading.value = false
    }
    fetchCurrentUser()
  } else {
    fetchedFood.value = null
  }
})

const emitSearch = (prefix: string, value: any) => {
  if (!value || value === '-') return
  emit('search', `${prefix}:${value}`)
}

const handleDelete = () => {
  emit('delete', props.food.id)
}
const router = useRouter()

const formatDate = (dateString: string) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) + ' Uhr'
}

const translateType = (type: string) => {
  const typeLabels: Record<string, string> = {
    packaging: 'Verpackung',
    content: 'Inhalt (Essen selbst)',
    nutrition: 'Nährwerte',
    ingredients: 'Zutaten',
    barcode: 'Barcode',
    other: 'Weiteres'
  }
  return typeLabels[type] || type
}

const goToEdit = () => {
  emit('close')
  router.push(`/foods/${props.food.id}/edit`)
}
</script>