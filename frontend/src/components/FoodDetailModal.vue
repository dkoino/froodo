<template>
  <div v-if="isOpen && displayFood" class="fixed inset-0 bg-overlay flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="bg-surface rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
      <div v-if="loading" class="absolute inset-0 bg-surface/50 flex items-center justify-center z-20">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
      <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-muted sticky top-0 z-10">
        <div>
          <h3 class="text-xl font-bold text-content">Lebensmittel-Informationen</h3>
        </div>
        <BaseButton size="none" variant="ghost" class="text-xl font-bold" @click="$emit('close')">
          &times;
        </BaseButton>
      </div>

      <div class="p-6 space-y-8">
        
        <!-- 1. Allgemein -->
        <div class="bg-surface-muted p-5 rounded-md border border-border-muted">
          <h3 class="text-lg font-semibold text-content-secondary mb-4 border-b border-border pb-2">Allgemein</h3>
          <div class="flex flex-col md:flex-row justify-between gap-6">
            <div class="space-y-3 flex-1">
              <div class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Name</span>
                <span class="text-content font-semibold">{{ displayFood.name }}</span>
              </div>
              <div v-if="displayFood.variant" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Variante</span>
                <span class="text-content font-semibold">{{ displayFood.variant }}</span>
              </div>
              <div v-if="displayFood.brand" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm text-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Marke & Hersteller</span>
                <span class="text-content font-semibold">
                  <span class="cursor-pointer hover:text-primary transition-colors" @click="emitSearch('marke', displayFood.brand.name)">{{ displayFood.brand.name }}</span>
                  <span v-if="displayFood.brand?.manufacturer" class="text-content-muted font-normal"> &gt; <span class="cursor-pointer hover:text-primary transition-colors" @click="emitSearch('marke', displayFood.brand.manufacturer.name)">{{ displayFood.brand.manufacturer.name }}</span></span>
                </span>
              </div>
            </div>
            <div class="flex items-start justify-end pt-2 shrink-0">
              <div v-if="packagingPhoto" class="border border-border rounded p-2 bg-surface w-48 max-w-full aspect-square flex flex-col">
                <a :href="`http://localhost:8000/storage/${packagingPhoto.file_path}`" target="_blank" class="block flex-1 min-h-0 hover:opacity-90 transition-opacity flex flex-col justify-center">
                  <img :src="`http://localhost:8000/storage/${packagingPhoto.file_path}`" class="w-full h-full object-contain rounded bg-surface-muted">
                </a>
                <span class="block text-center text-[10px] font-semibold text-content-muted uppercase mt-2 bg-surface-subtle py-1 rounded">Verpackung</span>
              </div>
              <div v-else class="w-48 max-w-full aspect-square border border-dashed border-border-strong rounded flex flex-col items-center justify-center bg-surface-muted text-content-subtle">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Kategorisierung & Zutaten -->
        <div class="bg-surface-muted p-5 rounded-md border border-border-muted">
          <h3 class="text-lg font-semibold text-content-secondary mb-4 border-b border-border pb-2">Kategorisierung & Zutaten</h3>
          <div class="flex flex-col md:flex-row justify-between gap-6">
            <div class="space-y-3 text-sm flex-1">
              <div v-if="displayFood.main_category" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Kategorie</span>
                <span class="text-content font-semibold">
                  <span class="cursor-pointer hover:text-primary transition-colors" @click="emitSearch('kategorie', displayFood.main_category.name)">{{ displayFood.main_category.name }}</span>
                  <span v-if="displayFood.sub_category" class="text-content-muted font-normal"> &gt; <span class="cursor-pointer hover:text-primary transition-colors" @click="emitSearch('kategorie', displayFood.sub_category.name)">{{ displayFood.sub_category.name }}</span></span>
                </span>
              </div>
              <div v-if="displayFood.state || (displayFood.meat_type && displayFood.meat_type !== 'Unbekannt')" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm space-y-2">
                <div v-if="displayFood.meat_type && displayFood.meat_type !== 'Unbekannt'">
                  <span class="block text-content-subtle text-xs font-medium uppercase">Fleischsorte</span>
                  <span class="text-content font-semibold cursor-pointer hover:text-primary transition-colors" @click="emitSearch('fleischsorte', displayFood.meat_type)">{{ displayFood.meat_type }}</span>
                </div>
                <div v-if="displayFood.state">
                  <span class="block text-content-subtle text-xs font-medium uppercase">Zustand</span>
                  <span class="text-content font-semibold cursor-pointer hover:text-primary transition-colors" @click="emitSearch('zustand', displayFood.state)">{{ displayFood.state }}</span>
                </div>
              </div>
            </div>
            <div class="flex items-start justify-end pt-2 shrink-0">
              <div v-if="ingredientsPhoto" class="border border-border rounded p-2 bg-surface w-48 max-w-full aspect-square flex flex-col">
                <a :href="`http://localhost:8000/storage/${ingredientsPhoto.file_path}`" target="_blank" class="block flex-1 min-h-0 hover:opacity-90 transition-opacity flex flex-col justify-center">
                  <img :src="`http://localhost:8000/storage/${ingredientsPhoto.file_path}`" class="w-full h-full object-contain rounded bg-surface-muted">
                </a>
                <span class="block text-center text-[10px] font-semibold text-content-muted uppercase mt-2 bg-surface-subtle py-1 rounded">Zutatenliste</span>
              </div>
              <div v-else class="w-48 max-w-full aspect-square border border-dashed border-border-strong rounded flex flex-col items-center justify-center bg-surface-muted text-content-subtle">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Menge & Portion -->
        <div class="bg-surface-muted p-5 rounded-md border border-border-muted">
          <h3 class="text-lg font-semibold text-content-secondary mb-4 border-b border-border pb-2">Menge & Portion</h3>
          <div class="flex flex-col md:flex-row justify-between gap-6">
            <div class="space-y-3 text-sm flex-1">
              <div v-if="displayFood.total_amount" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Gesamtmenge</span>
                <span class="text-content font-semibold">{{ displayFood.total_amount }} {{ displayFood.measurement_unit }}</span>
              </div>
              <div v-if="displayFood.portion_amount" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Portion ({{ displayFood.portion_label || 'Stück' }})</span>
                <span class="text-content font-semibold">{{ displayFood.portion_amount }} {{ displayFood.measurement_unit }}</span>
              </div>
            </div>
            <div class="flex items-start justify-end pt-2 shrink-0">
              <div v-if="contentPhoto" class="border border-border rounded p-2 bg-surface w-48 max-w-full aspect-square flex flex-col">
                <a :href="`http://localhost:8000/storage/${contentPhoto.file_path}`" target="_blank" class="block flex-1 min-h-0 hover:opacity-90 transition-opacity flex flex-col justify-center">
                  <img :src="`http://localhost:8000/storage/${contentPhoto.file_path}`" class="w-full h-full object-contain rounded bg-surface-muted">
                </a>
                <span class="block text-center text-[10px] font-semibold text-content-muted uppercase mt-2 bg-surface-subtle py-1 rounded">Essen selbst</span>
              </div>
              <div v-else class="w-48 max-w-full aspect-square border border-dashed border-border-strong rounded flex flex-col items-center justify-center bg-surface-muted text-content-subtle">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. Nährwerte pro 100g -->
        <div class="bg-surface-muted p-5 rounded-md border border-border-muted">
          <h3 class="text-lg font-semibold text-content-secondary mb-4 border-b border-border pb-2">Nährwerte pro 100{{ displayFood?.measurement_unit || 'g' }}</h3>
          <div class="flex flex-col md:flex-row justify-between gap-6">
            <div class="space-y-3 flex-1">
              <div class="overflow-hidden rounded-lg border border-border bg-surface">
                <table class="min-w-full text-sm text-left">
                  <tbody class="divide-y divide-border-muted">
                    <tr v-if="displayFood?.calories_p100 !== null && displayFood?.calories_p100 !== undefined" class="bg-nutrient-energy-bg">
                      <th class="px-4 py-2 font-bold text-nutrient-energy-text">Kalorien</th>
                      <td class="px-4 py-2 text-right font-bold text-nutrient-energy-text">{{ displayFood.calories_p100 }} kcal</td>
                    </tr>
                    <tr v-if="displayFood?.fat_p100 !== null && displayFood?.fat_p100 !== undefined" class="bg-nutrient-fat-bg">
                      <th class="px-4 py-2 font-medium text-nutrient-fat-text">Fett</th>
                      <td class="px-4 py-2 text-right font-bold text-nutrient-fat-text">{{ displayFood.fat_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.sat_fat_p100 !== null && displayFood?.sat_fat_p100 !== undefined" class="bg-nutrient-fat-bg-soft/50">
                      <th class="px-4 py-1 font-normal text-nutrient-fat-text-soft/80 pl-8">davon gesättigt</th>
                      <td class="px-4 py-1 text-right text-nutrient-fat-text-soft/80">{{ displayFood.sat_fat_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.carbs_p100 !== null && displayFood?.carbs_p100 !== undefined" class="bg-nutrient-carbs-bg">
                      <th class="px-4 py-2 font-medium text-nutrient-carbs-text">Kohlenhydrate</th>
                      <td class="px-4 py-2 text-right font-bold text-nutrient-carbs-text">{{ displayFood.carbs_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.sugar_p100 !== null && displayFood?.sugar_p100 !== undefined" class="bg-nutrient-carbs-bg-soft/50">
                      <th class="px-4 py-1 font-normal text-nutrient-carbs-text-soft/80 pl-8">davon Zucker</th>
                      <td class="px-4 py-1 text-right text-nutrient-carbs-text-soft/80">{{ displayFood.sugar_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.fiber_p100 !== null && displayFood?.fiber_p100 !== undefined" class="bg-surface-muted">
                      <th class="px-4 py-2 font-medium text-content-secondary">Ballaststoffe</th>
                      <td class="px-4 py-2 text-right text-content-secondary">{{ displayFood.fiber_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.protein_p100 !== null && displayFood?.protein_p100 !== undefined" class="bg-nutrient-protein-bg">
                      <th class="px-4 py-2 font-medium text-nutrient-protein-text">Protein</th>
                      <td class="px-4 py-2 text-right font-bold text-nutrient-protein-text">{{ displayFood.protein_p100 }} g</td>
                    </tr>
                    <tr v-if="displayFood?.salt_p100 !== null && displayFood?.salt_p100 !== undefined" class="bg-surface">
                      <th class="px-4 py-2 font-medium text-content-secondary">Salz</th>
                      <td class="px-4 py-2 text-right text-content-secondary">{{ displayFood.salt_p100 }} g</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="flex items-start justify-end pt-2 shrink-0">
              <div v-if="nutritionPhoto" class="border border-border rounded p-2 bg-surface w-48 max-w-full aspect-square flex flex-col">
                <a :href="`http://localhost:8000/storage/${nutritionPhoto.file_path}`" target="_blank" class="block flex-1 min-h-0 hover:opacity-90 transition-opacity flex flex-col justify-center">
                  <img :src="`http://localhost:8000/storage/${nutritionPhoto.file_path}`" class="w-full h-full object-contain rounded bg-surface-muted">
                </a>
                <span class="block text-center text-[10px] font-semibold text-content-muted uppercase mt-2 bg-surface-subtle py-1 rounded">Nährwert-Tabelle</span>
              </div>
              <div v-else class="w-48 max-w-full aspect-square border border-dashed border-border-strong rounded flex flex-col items-center justify-center bg-surface-muted text-content-subtle">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 5. Zusätzliche Informationen -->
        <div class="bg-surface-muted p-5 rounded-md border border-border-muted">
          <h3 class="text-lg font-semibold text-content-secondary mb-4 border-b border-border pb-2">Zusätzliche Informationen</h3>
          <div class="flex flex-col md:flex-row justify-between gap-6">
            <div class="space-y-3 text-sm flex-1">
              <div v-if="displayFood.barcode" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Barcode (EAN)</span>
                <span class="text-content font-semibold cursor-pointer hover:text-primary transition-colors" @click="emitSearch('barcode', displayFood.barcode)">{{ displayFood.barcode }}</span>
              </div>
              
              <div v-if="displayFood.price" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <span class="block text-content-subtle text-xs font-medium uppercase">Preis</span>
                <span class="text-content font-semibold">{{ displayFood.price }} €</span>
              </div>

              <div v-if="displayFood.source_url || displayFood.source_type" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm space-y-2">
                <div v-if="displayFood.source_type">
                  <span class="block text-content-subtle text-xs font-medium uppercase">Datenquelle</span>
                  <span class="text-content font-semibold">{{ displayFood.source_type }}</span>
                </div>
                <div v-if="displayFood.source_url">
                  <span class="block text-content-subtle text-xs font-medium uppercase">Quellen-Link</span>
                  <a :href="displayFood.source_url" target="_blank" class="text-primary hover:underline font-medium break-all text-xs">{{ displayFood.source_url }}</a>
                </div>
              </div>

              <div v-if="displayFood.notes || otherPhotos.length > 0" class="p-3 bg-surface border border-border-muted rounded-lg shadow-sm">
                <div v-if="displayFood.notes" class="mb-4">
                  <span class="block text-content-subtle text-xs font-medium uppercase mb-1">Notizen</span>
                  <p class="text-content-secondary whitespace-pre-line bg-surface-muted p-3 rounded border border-border-muted">{{ displayFood.notes }}</p>
                </div>
                <div v-if="otherPhotos.length > 0">
                  <span class="block text-content-subtle text-xs font-medium uppercase mb-2">Weitere Fotos</span>
                  <div class="flex gap-2 flex-wrap">
                    <div v-for="photo in otherPhotos" :key="photo.id" class="border border-border rounded p-1 bg-surface w-20 aspect-square">
                      <a :href="`http://localhost:8000/storage/${photo.file_path}`" target="_blank" class="block hover:opacity-90 transition-opacity w-full h-full">
                        <img :src="`http://localhost:8000/storage/${photo.file_path}`" class="w-full h-full object-cover rounded bg-surface-muted">
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-start justify-end pt-2 shrink-0">
              <div v-if="barcodePhoto" class="border border-border rounded p-2 bg-surface w-48 max-w-full aspect-square flex flex-col">
                <a :href="`http://localhost:8000/storage/${barcodePhoto.file_path}`" target="_blank" class="block flex-1 min-h-0 hover:opacity-90 transition-opacity flex flex-col justify-center">
                  <img :src="`http://localhost:8000/storage/${barcodePhoto.file_path}`" class="w-full h-full object-contain rounded bg-surface-muted">
                </a>
                <span class="block text-center text-[10px] font-semibold text-content-muted uppercase mt-2 bg-surface-subtle py-1 rounded">Barcode</span>
              </div>
              <div v-else class="w-48 max-w-full aspect-square border border-dashed border-border-strong rounded flex flex-col items-center justify-center bg-surface-muted text-content-subtle">
                <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs">Kein Foto</span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="displayFood.creator" class="text-xs text-content-subtle text-right pt-4 border-t border-border-muted italic space-y-1">
          <div>
            ID: {{ displayFood.id }}
          </div>
          <div>
            Angelegt von
            <span class="cursor-pointer hover:text-content-muted transition-colors" @click="emitSearch('nutzer', displayFood.creator.name)">
              {{ displayFood.creator.name }}
            </span>
            am {{ formatDate(displayFood.created_at) }}
          </div>
          <div v-if="displayFood.updated_at && displayFood.updater">
            Zuletzt geändert von
            <span class="cursor-pointer hover:text-content-muted transition-colors" @click="emitSearch('nutzer', displayFood.updater.name)">
              {{ displayFood.updater.name }}
            </span>
            am {{ formatDate(displayFood.updated_at) }}
          </div>
        </div>
      </div>

      <div class="px-6 py-4 border-t border-border-muted flex justify-between space-x-3 bg-surface-muted sticky bottom-0">
        <div>
          <BaseButton v-if="canEditOrDelete" variant="danger" class="rounded-md" @click="handleDelete">
            Löschen
          </BaseButton>
        </div>
        <div class="flex space-x-3">
          <BaseButton v-if="currentUser" variant="success" class="rounded-md" @click="goToFoodLog">
            Essen
          </BaseButton>
          <BaseButton v-if="canEditOrDelete" variant="warning" class="rounded-md" @click="goToEdit">
            Bearbeiten
          </BaseButton>
          <BaseButton variant="secondary" class="rounded-md" @click="$emit('close')">
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

const emitSearch = (field: string, value: any) => {
  if (!value || value === '-') return
  emit('search', { field, term: String(value) })
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

const goToFoodLog = () => {
  emit('close')
  router.push(`/food-log?addFood=${props.food.id}`)
}
</script>