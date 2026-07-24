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
        
        <!-- Allgemein & Menge -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-2 space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2">Allgemein & Menge</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
              <div v-if="displayFood.brand" class="col-span-1 sm:col-span-2">
                <span class="block text-gray-400 text-xs font-medium uppercase">Marke & Hersteller</span>
                <span class="text-gray-800 font-semibold">
                  <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Marke', displayFood.brand.name)">{{ displayFood.brand.name }}</span>
                  <span v-if="displayFood.brand?.manufacturer" class="text-gray-500 font-normal"> &gt; <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Hersteller', displayFood.brand.manufacturer.name)">{{ displayFood.brand.manufacturer.name }}</span></span>
                </span>
              </div>
              <div v-if="displayFood.main_category">
                <span class="block text-gray-400 text-xs font-medium uppercase">Kategorie</span>
                <span class="text-gray-800 font-semibold">
                  <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Kategorie', displayFood.main_category.name)">{{ displayFood.main_category.name }}</span>
                  <span v-if="displayFood.sub_category" class="text-gray-500 font-normal"> &gt; <span class="cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Kategorie', displayFood.sub_category.name)">{{ displayFood.sub_category.name }}</span></span>
                </span>
              </div>
              <div v-if="displayFood.state">
                <span class="block text-gray-400 text-xs font-medium uppercase">Zustand</span>
                <span class="text-gray-800 font-semibold cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Zustand', displayFood.state)">{{ displayFood.state }}</span>
              </div>
              <div v-if="displayFood.meat_type && displayFood.meat_type !== 'Unbekannt'">
                <span class="block text-gray-400 text-xs font-medium uppercase">Fleischsorte</span>
                <span class="text-gray-800 font-semibold cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Fleisch', displayFood.meat_type)">{{ displayFood.meat_type }}</span>
              </div>
              <div v-if="displayFood.total_amount">
                <span class="block text-gray-400 text-xs font-medium uppercase">Gesamtmenge</span>
                <span class="text-gray-800 font-semibold">{{ displayFood.total_amount }} {{ displayFood.measurement_unit }}</span>
              </div>
              <div v-if="displayFood.portion_amount">
                <span class="block text-gray-400 text-xs font-medium uppercase">Portion ({{ displayFood.portion_label || 'Stück' }})</span>
                <span class="text-gray-800 font-semibold">{{ displayFood.portion_amount }} {{ displayFood.measurement_unit }}</span>
              </div>
            </div>
          </div>
          <div class="space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2">Bilder</h4>
            <div v-if="generalPhotos.length > 0" class="flex flex-col gap-4">
              <div v-for="photo in generalPhotos" :key="photo.id" class="border border-gray-200 rounded p-2 bg-white">
                <a :href="`http://localhost:8000/storage/${photo.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${photo.file_path}`" class="w-full h-32 object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">{{ translateType(photo.type) }}</span>
              </div>
            </div>
            <div v-else class="text-center p-6 bg-gray-50 border border-gray-100 rounded text-gray-400 text-xs">Keine Bilder vorhanden.</div>
          </div>
        </div>

        <!-- Nährwerte -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-2 space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2">
              Nährwerte pro 100 {{ displayFood?.measurement_unit || 'g' }}
            </h4>
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
                    <th class="px-4 py-1 font-normal text-amber-700/80 pl-8">davon gesättigte Fettsäuren</th>
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
                  <tr v-if="displayFood?.protein_p100 !== null && displayFood?.protein_p100 !== undefined" class="bg-green-50">
                    <th class="px-4 py-2 font-medium text-green-900">Protein</th>
                    <td class="px-4 py-2 text-right font-bold text-green-900">{{ displayFood.protein_p100 }} g</td>
                  </tr>
                  <tr v-if="displayFood?.fiber_p100 !== null && displayFood?.fiber_p100 !== undefined" class="bg-gray-50">
                    <th class="px-4 py-2 font-medium text-gray-700">Ballaststoffe</th>
                    <td class="px-4 py-2 text-right text-gray-700">{{ displayFood.fiber_p100 }} g</td>
                  </tr>
                  <tr v-if="displayFood?.salt_p100 !== null && displayFood?.salt_p100 !== undefined" class="bg-white">
                    <th class="px-4 py-2 font-medium text-gray-700">Salz</th>
                    <td class="px-4 py-2 text-right text-gray-700">{{ displayFood.salt_p100 }} g</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2">Bilder</h4>
            <div v-if="nutritionPhotos.length > 0" class="flex flex-col gap-4">
              <div v-for="photo in nutritionPhotos" :key="photo.id" class="border border-gray-200 rounded p-2 bg-white">
                <a :href="`http://localhost:8000/storage/${photo.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${photo.file_path}`" class="w-full h-32 object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">{{ translateType(photo.type) }}</span>
              </div>
            </div>
            <div v-else class="text-center p-6 bg-gray-50 border border-gray-100 rounded text-gray-400 text-xs">Keine Bilder vorhanden.</div>
          </div>
        </div>

        <!-- Zusätzliche Infos -->
        <div v-if="displayFood.price || displayFood.barcode || displayFood.source_url || displayFood.notes || extraPhotos.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-2 space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2">Zusätzliche Infos</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
              <div v-if="displayFood.price">
                <span class="block text-gray-400 text-xs font-medium uppercase">Preis</span>
                <span class="text-gray-800 font-semibold">{{ displayFood.price }} €</span>
              </div>
              <div v-if="displayFood.barcode">
                <span class="block text-gray-400 text-xs font-medium uppercase">Barcode (EAN)</span>
                <span class="text-gray-800 font-semibold cursor-pointer hover:text-blue-600 transition-colors" @click="emitSearch('Barcode', displayFood.barcode)">{{ displayFood.barcode }}</span>
              </div>
              <div v-if="displayFood.source_url" class="col-span-1 sm:col-span-2">
                <span class="block text-gray-400 text-xs font-medium uppercase">Quellen-Link</span>
                <a :href="displayFood.source_url" target="_blank" class="text-blue-600 hover:underline font-medium break-all text-xs">{{ displayFood.source_url }}</a>
              </div>
              <div v-if="displayFood.notes" class="col-span-1 sm:col-span-2">
                <span class="block text-gray-400 text-xs font-medium uppercase mb-1">Notizen</span>
                <p class="text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded border border-gray-100">{{ displayFood.notes }}</p>
              </div>
            </div>
          </div>
          <div class="space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2">Bilder</h4>
            <div v-if="extraPhotos.length > 0" class="flex flex-col gap-4">
              <div v-for="photo in extraPhotos" :key="photo.id" class="border border-gray-200 rounded p-2 bg-white">
                <a :href="`http://localhost:8000/storage/${photo.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity">
                  <img :src="`http://localhost:8000/storage/${photo.file_path}`" class="w-full h-32 object-contain rounded bg-gray-50">
                </a>
                <span class="block text-center text-[10px] font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">{{ translateType(photo.type) }}</span>
              </div>
            </div>
            <div v-else class="text-center p-6 bg-gray-50 border border-gray-100 rounded text-gray-400 text-xs">Keine Bilder vorhanden.</div>
          </div>
        </div>

        <div v-if="displayFood.user" class="text-xs text-gray-400 text-right pt-4 border-t border-gray-100 italic space-y-1">
          <div>
            Angelegt von
            <span class="cursor-pointer hover:text-gray-600 transition-colors" @click="emitSearch('Nutzer', displayFood.user.name)">
              {{ displayFood.user.name }}
            </span>
            am {{ formatDate(displayFood.created_at) }}
          </div>
          <div v-if="displayFood.updated_at">
            Zuletzt geändert von
            <span class="cursor-pointer hover:text-gray-600 transition-colors" @click="emitSearch('Nutzer', displayFood.user.name)">
              {{ displayFood.user.name }}
            </span>
            am {{ formatDate(displayFood.updated_at) }}
          </div>
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

const displayFood = computed(() => fetchedFood.value || props.food)

const generalPhotos = computed(() => displayFood.value?.photos?.filter((p: any) => p.type === 'packaging' || p.type === 'content') || [])
const nutritionPhotos = computed(() => displayFood.value?.photos?.filter((p: any) => p.type === 'nutrition' || p.type === 'ingredients') || [])
const extraPhotos = computed(() => displayFood.value?.photos?.filter((p: any) => p.type === 'barcode' || p.type === 'other') || [])

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