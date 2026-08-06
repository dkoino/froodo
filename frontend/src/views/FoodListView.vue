<template>
  <div class="max-w-4xl mx-auto p-6 bg-surface shadow-sm rounded-lg mt-10 mb-10 border border-border">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h2 class="text-2xl font-bold text-content">Gespeicherte Lebensmittel</h2>
      <div class="flex items-center gap-3 w-full md:w-auto">
        <div class="w-36">
          <BaseSelect v-model="searchScope">
            <option value="alle">Alle Felder</option>
            <option value="name">Name</option>
            <option value="marke">Marke</option>
            <option value="kategorie">Kategorie</option>
            <option value="barcode">Barcode</option>
            <option value="fleischsorte">Fleischsorte</option>
          </BaseSelect>
        </div>
        <div class="w-full md:w-72">
          <BaseInput
            ref="searchInputRef"
            v-model="searchQuery"
            placeholder="Suchbegriff..."
          />
        </div>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse table-fixed">
        <thead>
          <tr class="border-b border-border text-sm font-semibold text-content-muted bg-surface-muted">
            <th class="p-2.5 w-20">Bild</th>
            <th class="p-2.5 w-2/5">Name</th>
            <th class="p-2.5 pl-3 w-1/4">Brand</th>
            <th class="p-2.5 pr-3 w-1/5 text-right">Menge</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border-muted text-base text-content-secondary">
          <tr v-for="food in paginatedFoods" :key="food.id" @click="openDetailModal(food)" class="bg-surface even:bg-surface-muted hover:bg-surface-subtle transition duration-75 group cursor-pointer">
            
            <td class="p-2.5">
              <div 
                class="w-14 h-14 bg-surface-subtle rounded border border-border overflow-hidden flex items-center justify-center relative cursor-pointer"
                @mousemove="getPrimaryImage(food) ? updateMousePos($event) : null"
                @mouseenter="getPrimaryImage(food) ? setHoveredImage(getPrimaryImage(food)) : null"
                @mouseleave="clearHoveredImage"
              >
                <img 
                  v-if="getPrimaryImage(food)" 
                  :src="getPrimaryImage(food)" 
                  alt="Produktbild"
                  class="w-full h-full object-cover"
                >
                <svg v-else class="w-6 h-6 text-content-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </td>
            
            <td class="p-2.5 overflow-hidden">
              <div class="font-semibold text-content-strong text-lg truncate" :title="food.name">{{ food.name }}</div>
              <div v-if="food.variant" class="text-sm text-content-muted mt-0.5 truncate" :title="food.variant">{{ food.variant }}</div>
            </td>
            
            <td class="p-2.5 pl-3 overflow-hidden">
              <div v-if="food.brand" class="flex items-center space-x-3" :title="food.brand.name">
                <img 
                  v-if="food.brand.logo_path" 
                  :src="`http://localhost:8000/storage/${food.brand.logo_path}`" 
                  alt="Logo"
                  class="w-8 h-8 flex-shrink-0 object-contain rounded bg-surface border border-border p-0.5"
                >
                <div v-else class="w-8 h-8 flex-shrink-0 flex items-center justify-center bg-surface-subtle text-content-subtle rounded border border-border text-xs font-bold uppercase">
                  {{ food.brand.name.charAt(0) }}
                </div>
                <span class="text-content-secondary font-medium truncate">{{ food.brand.name }}</span>
              </div>
              <span v-else class="text-content-subtle">-</span>
            </td>
            
            <td class="p-2.5 pr-3 font-medium text-content text-right">
              {{ Number(food.total_amount) }}{{ food.measurement_unit }}
            </td>

          </tr>
        </tbody>
      </table>

      <div v-if="filteredFoods.length === 0" class="p-12 text-center text-content-muted border-t border-border-muted">
        Keine Lebensmittel gefunden.
      </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 text-sm text-content-muted gap-4" v-if="filteredFoods.length > 0">
      <div>
        Zeige {{ (currentPage - 1) * itemsPerPage + 1 }} bis {{ Math.min(currentPage * itemsPerPage, filteredFoods.length) }} von {{ filteredFoods.length }} Einträgen
      </div>
      <div class="flex items-center space-x-2">
        <BaseButton variant="secondary" class="rounded-md" :disabled="currentPage === 1" @click="currentPage--">
          Zurück
        </BaseButton>
        
        <BaseButton variant="secondary" class="rounded-md" :disabled="currentPage === totalPages" @click="currentPage++">
          Weiter
        </BaseButton>
      </div>
    </div>

    <FoodDetailModal 
      :isOpen="isDetailModalOpen" 
      :food="selectedFood" 
      @close="isDetailModalOpen = false" 
      @delete="deleteFood"
      @search="handleSearch"
    />

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
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import FoodDetailModal from '../components/FoodDetailModal.vue'
import BaseButton from '../components/BaseButton.vue'
import BaseInput from '../components/BaseInput.vue'
import BaseSelect from '../components/BaseSelect.vue'

const foods = ref<any[]>([])
const selectedFood = ref<any>(null)
const isDetailModalOpen = ref(false)

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

const searchQuery = ref('')
const searchInputRef = ref<InstanceType<typeof BaseInput> | null>(null)
const itemsPerPage = 10
const currentPage = ref(1)

// Persistenter Feld-Filter, unabhängig vom Text im Suchfeld. Wer lieber
// "Marke: Chio" direkt eintippt, kann das weiterhin tun (siehe FIELD_ALIASES) —
// beide Wege laufen über dieselben FIELD_MATCHERS, damit sie nie voneinander abweichen.
const searchScope = ref('alle')

watch(searchScope, () => {
  nextTick(() => searchInputRef.value?.focusEnd())
})

const FIELD_MATCHERS: Record<string, (food: any, term: string) => boolean> = {
  name: (food, term) =>
    (food.name || '').toLowerCase().includes(term) ||
    (food.variant || '').toLowerCase().includes(term),
  marke: (food, term) =>
    (food.brand?.name || '').toLowerCase().includes(term) ||
    (food.brand?.manufacturer?.name || '').toLowerCase().includes(term),
  kategorie: (food, term) =>
    (food.main_category?.name || '').toLowerCase().includes(term) ||
    (food.sub_category?.name || '').toLowerCase().includes(term),
  barcode: (food, term) => (food.barcode || '').toLowerCase().includes(term),
  fleischsorte: (food, term) => (food.meat_type || '').toLowerCase().includes(term),
  zustand: (food, term) => (food.state || '').toLowerCase().includes(term),
  notiz: (food, term) => (food.notes || '').toLowerCase().includes(term),
  nutzer: (food, term) => (food.creator?.name || '').toLowerCase().includes(term),
}

// Synonyme, die beim manuellen Eintippen von "prefix:begriff" erkannt werden.
// zustand/notiz/nutzer stehen bewusst nicht im Dropdown, funktionieren aber
// weiterhin per Texteingabe und über die Klick-Links im FoodDetailModal.
const FIELD_ALIASES: Record<string, string> = {
  name: 'name',
  marke: 'marke', brand: 'marke', hersteller: 'marke',
  kategorie: 'kategorie', category: 'kategorie',
  barcode: 'barcode', ean: 'barcode',
  fleisch: 'fleischsorte', fleischsorte: 'fleischsorte', meat: 'fleischsorte',
  zustand: 'zustand', state: 'zustand',
  notiz: 'notiz', notizen: 'notiz', notes: 'notiz',
  nutzer: 'nutzer', user: 'nutzer',
}

const matchAnyField = (list: any[], query: string) =>
  list.filter(food => (
    (food.name || '').toLowerCase().includes(query) ||
    (food.variant || '').toLowerCase().includes(query) ||
    (food.barcode || '').toLowerCase().includes(query) ||
    (food.meat_type || '').toLowerCase().includes(query) ||
    (food.state || '').toLowerCase().includes(query) ||
    (food.notes || '').toLowerCase().includes(query) ||
    (food.brand?.name || '').toLowerCase().includes(query) ||
    (food.brand?.manufacturer?.name || '').toLowerCase().includes(query) ||
    (food.main_category?.name || '').toLowerCase().includes(query) ||
    (food.sub_category?.name || '').toLowerCase().includes(query) ||
    (food.creator?.name || '').toLowerCase().includes(query)
  ))

const filteredFoods = computed(() => {
  const rawQuery = searchQuery.value.trim()
  if (!rawQuery) return foods.value

  const query = rawQuery.toLowerCase()
  const prefixMatch = query.match(/^([a-zäöüß]+):(.*)/i)

  if (prefixMatch) {
    const field = FIELD_ALIASES[(prefixMatch[1] ?? '').trim()]
    const term = (prefixMatch[2] ?? '').trim()
    const matcher = field ? FIELD_MATCHERS[field] : undefined
    if (matcher && term) {
      return foods.value.filter(food => matcher(food, term))
    }
    // Unbekannter Prefix oder nichts nach dem Doppelpunkt: wie gewohnt als
    // reiner Substring über alle Felder suchen, Doppelpunkt inklusive.
    return matchAnyField(foods.value, query)
  }

  const scopeMatcher = searchScope.value !== 'alle' ? FIELD_MATCHERS[searchScope.value] : undefined
  if (scopeMatcher) {
    return foods.value.filter(food => scopeMatcher(food, query))
  }

  return matchAnyField(foods.value, query)
})

const totalPages = computed(() => Math.ceil(filteredFoods.value.length / itemsPerPage) || 1)

watch(searchQuery, () => {
  currentPage.value = 1
})

watch(totalPages, (newTotal) => {
  if (currentPage.value > newTotal) {
    currentPage.value = newTotal || 1
  }
})

const paginatedFoods = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredFoods.value.slice(start, end)
})

const fetchFoods = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/foods', { credentials: 'include', credentials: 'include' })
    const data = await response.json()
    foods.value = Array.isArray(data) ? data : data.data || data
  } catch (error) {
    console.error('Fehler beim Laden:', error)
  }
}

const getPrimaryImage = (food: any) => {
  if (!food.photos || !Array.isArray(food.photos) || food.photos.length === 0) {
    return null
  }
  const primaryPhoto = food.photos.find((p: any) => p.type === 'packaging') 
    || food.photos.find((p: any) => p.type === 'content')
    || food.photos.find((p: any) => p.type === 'other') 
                    || food.photos[0]
  if (!primaryPhoto || !primaryPhoto.file_path) {
    return null
  }
  return `http://localhost:8000/storage/${primaryPhoto.file_path}`
}

const openDetailModal = (food: any) => {
  selectedFood.value = food
  isDetailModalOpen.value = true
}

const deleteFood = async (id: number) => {
  if (!confirm('Willst du dieses Lebensmittel wirklich löschen?')) {
    return
  }
  try {
    const response = await fetch(`http://localhost:8000/api/foods/${id}`, { credentials: 'include',
      method: 'DELETE',
    })
    if (response.ok || response.status === 204) {
      foods.value = foods.value.filter(food => food.id !== id)
      isDetailModalOpen.value = false
    } else {
      console.error('Fehler vom Backend:', await response.text())
    }
  } catch (error) {
    console.error('Netzwerk-Fehler beim Löschen:', error)
  }
}

const handleSearch = (payload: { field: string; term: string }) => {
  searchScope.value = payload.field
  searchQuery.value = payload.term
  isDetailModalOpen.value = false
}

onMounted(() => {
  fetchFoods()
})
</script>