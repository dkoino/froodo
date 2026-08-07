<template>
  <div v-if="isOpen" class="fixed inset-0 bg-overlay flex items-center justify-center z-50 p-4">
    <div class="bg-surface rounded-lg shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col">
      <div class="px-6 py-4 border-b border-border flex justify-between items-start bg-surface-muted sticky top-0 z-10">
        <div>
          <h3 class="text-xl font-bold text-content">{{ isEditMode ? 'Eintrag bearbeiten' : 'Lebensmittel hinzufügen' }}</h3>
          <div class="text-sm text-content-muted mt-1 flex items-center">
            <span>für den</span>
            <input type="date" v-model="selectedDate" :max="getTodayDateStr()" :disabled="isEditMode" @click="($event.target as HTMLInputElement)?.showPicker()" @keydown.prevent class="ml-2 px-2 py-1 bg-surface-subtle hover:bg-surface-subtle border border-border-strong rounded-md text-sm font-medium text-content-secondary cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-border transition-colors disabled:cursor-not-allowed disabled:opacity-70" />
          </div>
        </div>
        <button type="button" @click="$emit('close')" class="text-2xl leading-none font-bold text-content-subtle hover:text-content-muted transition-colors">
          &times;
        </button>
      </div>

      <div class="p-6 overflow-y-auto">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div>
            <label class="block text-sm font-bold tracking-wider text-content-secondary uppercase mb-2">Lebensmittel</label>

            <div v-if="!isEditMode && !addForm.food_id" class="relative">
              <div class="flex gap-3 mb-2">
                <select v-model="searchScope" class="px-3 py-3 bg-surface border border-border-strong rounded-md focus:outline-none focus:ring-2 focus:ring-primary-border focus:border-transparent transition-shadow text-content-secondary">
                  <option v-for="opt in FOOD_SEARCH_FIELDS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
                <input ref="searchInputEl" v-model="searchQuery" type="text" class="block w-full px-4 py-3 bg-surface border border-border-strong rounded-md focus:outline-none focus:ring-2 focus:ring-primary-border focus:border-transparent transition-shadow" placeholder="Suche nach Name, Marke, Barcode..." />
              </div>

              <div v-if="searchQuery && filteredAvailableFoods.length > 0" class="absolute z-20 mt-1 w-full max-h-60 overflow-y-auto border border-border rounded-md shadow-lg bg-surface">
                <div v-for="food in filteredAvailableFoods.slice(0, 20)" :key="food.id" @click="selectFood(food)" class="px-4 py-3 hover:bg-primary-soft cursor-pointer border-b border-border-muted last:border-0 transition-colors flex items-center space-x-3">
                  <img v-if="getPrimaryPhoto(food)" :src="`http://localhost:8000/storage/${getPrimaryPhoto(food).file_path}`" class="w-10 h-10 object-cover rounded shadow-sm border border-border flex-shrink-0" />
                  <div v-else class="w-10 h-10 bg-surface-subtle rounded flex items-center justify-center text-content-faint border border-border flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div>
                    <div class="font-bold text-content">{{ food.name }}</div>
                    <div class="text-sm text-content-muted mt-1">{{ food.variant ? food.variant + ' • ' : '' }}{{ food.brand ? food.brand.name : 'Keine Marke' }}</div>
                  </div>
                </div>
              </div>
              <div v-else-if="searchQuery" class="mt-2 text-sm text-content-muted">
                Keine Treffer gefunden.
              </div>
            </div>

            <div v-else class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-surface-muted border border-border rounded-md gap-4">
              <div class="flex items-start space-x-4">
                <img v-if="getPrimaryPhoto(selectedFoodObj)" :src="`http://localhost:8000/storage/${getPrimaryPhoto(selectedFoodObj).file_path}`" class="w-14 h-14 object-cover rounded shadow-sm border border-border flex-shrink-0" />
                <div v-else class="w-14 h-14 bg-surface-subtle rounded flex items-center justify-center text-content-faint border border-border flex-shrink-0">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <div class="font-bold text-content text-lg">{{ selectedFoodObj?.name }}</div>
                  <div class="text-sm text-content-muted mt-1">{{ selectedFoodObj?.variant ? selectedFoodObj.variant + ' • ' : '' }}{{ selectedFoodObj?.brand?.name }}</div>
                </div>
              </div>
              <button v-if="!isEditMode" type="button" @click="clearSelection" class="text-primary hover:text-primary-strong text-sm font-semibold transition-colors px-3 py-1.5 border border-primary-soft-border bg-surface hover:bg-primary-soft rounded-md">Auswahl ändern</button>
            </div>

            <div v-if="addForm.food_id" class="flex gap-2 mt-2">
              <button type="button" @click="fillAmountFromTotal" class="flex-1 px-3 py-1.5 text-xs font-semibold rounded-md border border-border-strong bg-surface hover:bg-primary-soft hover:border-primary-soft-border hover:text-primary text-content-secondary transition-colors">
                Gesamtmenge: {{ Number(selectedFoodObj?.total_amount) }} {{ selectedFoodObj?.measurement_unit }}
              </button>
              <button v-if="selectedFoodObj?.portion_amount" type="button" @click="fillAmountFromPortion" class="flex-1 px-3 py-1.5 text-xs font-semibold rounded-md border border-border-strong bg-surface hover:bg-primary-soft hover:border-primary-soft-border hover:text-primary text-content-secondary transition-colors">
                + {{ selectedFoodObj.portion_label || 'Portion' }}: {{ Number(selectedFoodObj.portion_amount) }} {{ selectedFoodObj.measurement_unit }}
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-bold tracking-wider text-content-secondary uppercase mb-2">Konsumierte Menge</label>
            <div class="relative">
              <input
                v-model.number="addForm.amount"
                type="number" step="1" min="1" required
                :class="amountFlash ? 'bg-primary-soft border-primary-border' : 'bg-surface border-border-strong'"
                class="block w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-border focus:border-transparent transition-colors duration-500 text-content font-medium"
                placeholder="z.B. 100"
              />
              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <span class="text-content-muted font-medium">{{ selectedFoodUnit || 'g/ml' }}</span>
              </div>
            </div>
          </div>

          <div class="pt-4 flex justify-between items-center border-t border-border-muted mt-6">
            <button v-if="isEditMode" type="button" @click="handleDelete" :disabled="deleting" class="px-3 py-2.5 text-sm font-semibold text-danger hover:text-danger-hover transition-colors disabled:opacity-50">
              {{ deleting ? 'Löschen...' : 'Löschen' }}
            </button>
            <div v-else></div>
            <div class="flex space-x-3">
              <button type="button" @click="$emit('close')" class="px-5 py-2.5 border border-border-strong shadow-sm text-sm font-medium rounded-md text-content-secondary bg-surface hover:bg-surface-muted transition-colors">
                Abbrechen
              </button>
              <button type="submit" :disabled="saving" class="px-5 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-md text-on-primary bg-primary hover:bg-primary-hover transition-colors disabled:opacity-50">
                {{ saving ? 'Speichere...' : (isEditMode ? 'Änderungen speichern' : 'Eintrag hinzufügen') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { useFoodFieldSearch, FOOD_SEARCH_FIELDS } from '@/composables/useFoodFieldSearch'

const props = defineProps<{
  isOpen: boolean
  date?: string | null
  editingLog?: any | null
  presetFoodId?: string | number | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'saved'): void
  (e: 'deleted'): void
}>()

const isEditMode = computed(() => !!props.editingLog)

const getTodayDateStr = () => {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

const addForm = ref({
  food_id: '' as string | number,
  amount: null as number | null,
})
const selectedDate = ref<string | null>(null)
const saving = ref(false)
const deleting = ref(false)

const availableFoods = ref<any[]>([])
const searchInputEl = ref<HTMLInputElement | null>(null)
const { searchScope, searchQuery, filtered: filteredAvailableFoods } = useFoodFieldSearch(availableFoods)

watch(searchScope, () => {
  nextTick(() => searchInputEl.value?.focus())
})

const getPrimaryPhoto = (food: any) => {
  if (!food || !food.photos || !Array.isArray(food.photos) || food.photos.length === 0) {
    return null
  }
  return food.photos.find((p: any) => p.type === 'packaging')
    || food.photos.find((p: any) => p.type === 'content')
    || food.photos.find((p: any) => p.type === 'other')
    || food.photos[0]
}

const selectedFoodObj = computed(() => {
  if (!addForm.value.food_id) return null
  return availableFoods.value.find(f => f.id === addForm.value.food_id)
})

const selectedFoodUnit = computed(() => {
  return selectedFoodObj.value ? selectedFoodObj.value.measurement_unit : ''
})

// Kurzes Aufleuchten des Mengenfelds, damit sichtbar wird, dass sich der Wert
// geändert hat. Bei erneutem Klick vor Ablauf wird der Timer neu gestartet.
const amountFlash = ref(false)
let amountFlashTimeout: ReturnType<typeof setTimeout> | null = null

const triggerAmountFlash = () => {
  if (amountFlashTimeout) clearTimeout(amountFlashTimeout)
  amountFlash.value = true
  amountFlashTimeout = setTimeout(() => {
    amountFlash.value = false
  }, 500)
}

const fillAmountFromTotal = () => {
  if (selectedFoodObj.value) {
    addForm.value.amount = Number(selectedFoodObj.value.total_amount)
    triggerAmountFlash()
  }
}

const fillAmountFromPortion = () => {
  if (selectedFoodObj.value?.portion_amount) {
    const portion = Number(selectedFoodObj.value.portion_amount)
    addForm.value.amount = (addForm.value.amount || 0) + portion
    triggerAmountFlash()
  }
}

const selectFood = (food: any) => {
  addForm.value.food_id = food.id
  searchQuery.value = ''
}

const clearSelection = () => {
  addForm.value.food_id = ''
  addForm.value.amount = null
}

const fetchAvailableFoods = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/foods', {
      credentials: 'include'
    })
    if (response.ok) {
      const data = await response.json()
      availableFoods.value = data.data || data // Handles paginated or raw data
    }
  } catch (error) {
    console.error('Fehler beim Laden der Lebensmittel:', error)
  }
}

// Formular je nach Modus befüllen, sobald das Modal geöffnet wird.
watch(() => props.isOpen, (open) => {
  if (!open) return
  searchQuery.value = ''

  if (props.editingLog) {
    addForm.value = {
      food_id: props.editingLog.food_id,
      amount: Number(props.editingLog.amount),
    }
    selectedDate.value = props.editingLog.consumed_at?.slice(0, 10) ?? getTodayDateStr()
  } else {
    addForm.value = {
      food_id: props.presetFoodId ?? '',
      amount: null,
    }
    selectedDate.value = props.date || getTodayDateStr()
  }
})

const submitForm = async () => {
  if (!addForm.value.food_id) {
    alert('Bitte wähle zuerst ein Lebensmittel aus.')
    return
  }
  if (!selectedDate.value) {
    alert('Bitte wähle ein Datum aus.')
    return
  }

  saving.value = true
  try {
    const response = isEditMode.value
      ? await fetch(`http://localhost:8000/api/food-logs/${props.editingLog.id}`, {
          method: 'PUT',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ amount: addForm.value.amount })
        })
      : await fetch('http://localhost:8000/api/food-logs', {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({
            food_id: addForm.value.food_id,
            amount: addForm.value.amount,
            consumed_at: selectedDate.value
          })
        })

    if (response.ok) {
      emit('saved')
      emit('close')
    } else {
      alert(isEditMode.value ? 'Fehler beim Aktualisieren des Eintrags.' : 'Fehler beim Speichern des Eintrags.')
    }
  } catch (error) {
    console.error(error)
    alert('Fehler beim Speichern.')
  } finally {
    saving.value = false
  }
}

const handleDelete = async () => {
  if (!props.editingLog) return
  if (!confirm('Eintrag wirklich löschen?')) return

  deleting.value = true
  try {
    const response = await fetch(`http://localhost:8000/api/food-logs/${props.editingLog.id}`, {
      method: 'DELETE',
      credentials: 'include'
    })
    if (response.ok) {
      emit('deleted')
      emit('close')
    } else {
      alert('Fehler beim Löschen.')
    }
  } catch (error) {
    console.error(error)
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchAvailableFoods()
})
</script>
