<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Verlauf</h1>
      <button @click="openAddModal('')" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Eintrag hinzufügen
      </button>
    </div>

    <div v-if="historyLogs.length > 0" class="mt-6">
      <DailyFoodLog 
        v-for="day in historyLogs" 
        :key="day.date" 
        :title="formatDate(day.date)" 
        :logs="day.logs" 
        :date="day.date"
        @update-log="handleHistoryUpdateLog($event, day.date)" 
        @delete-log="handleHistoryDeleteLog($event, day.date)" 
        @add-log="openAddModal"
      />
    </div>



    <!-- Add Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-start bg-gray-50 sticky top-0 z-10">
          <div>
            <h3 class="text-xl font-bold text-gray-800">Lebensmittel hinzufügen</h3>
            <div class="text-sm text-gray-500 mt-1 flex items-center">
              <span>für den</span>
              <input type="date" v-model="selectedDateForAdd" :max="getTodayDateStr()" @click="$event.target.showPicker()" @keydown.prevent class="ml-2 px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-sm font-medium text-gray-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" />
            </div>
          </div>
          <button type="button" @click="showAddModal = false" class="text-2xl leading-none font-bold text-gray-400 hover:text-gray-600 transition-colors">
            &times;
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="submitAddLog" class="space-y-6">
            <div>
              <label class="block text-sm font-bold tracking-wider text-gray-700 uppercase mb-2">Lebensmittel suchen</label>
              
              <div v-if="!addForm.food_id" class="relative">
                <input v-model="searchQuery" type="text" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="Suche nach Name, Marke, Barcode..." />
                
                <div v-if="searchQuery && filteredAvailableFoods.length > 0" class="absolute z-20 mt-1 w-full max-h-60 overflow-y-auto border border-gray-200 rounded-md shadow-lg bg-white">
                  <div v-for="food in filteredAvailableFoods.slice(0, 20)" :key="food.id" @click="selectFood(food)" class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors">
                    <div class="font-bold text-gray-800">{{ food.name }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ food.variant ? food.variant + ' • ' : '' }}{{ food.brand ? food.brand.name : 'Keine Marke' }}</div>
                  </div>
                </div>
                <div v-else-if="searchQuery" class="mt-2 text-sm text-gray-500">
                  Keine Treffer gefunden.
                </div>
              </div>

              <div v-else class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-md gap-4">
                <div>
                  <div class="font-bold text-gray-800">{{ selectedFoodObj?.name }}</div>
                  <div class="text-sm text-gray-500 mt-1">{{ selectedFoodObj?.variant ? selectedFoodObj.variant + ' • ' : '' }}{{ selectedFoodObj?.brand?.name }}</div>
                  
                  <div class="text-xs text-gray-600 mt-3 p-2 bg-white rounded border border-gray-200 shadow-sm inline-block">
                    <div><span class="font-semibold text-gray-700">Gesamtmenge:</span> {{ selectedFoodObj?.total_amount }} {{ selectedFoodObj?.measurement_unit }}</div>
                    <div v-if="selectedFoodObj?.portion_amount" class="mt-1"><span class="font-semibold text-gray-700">Portion ({{ selectedFoodObj.portion_label || 'Stück' }}):</span> {{ selectedFoodObj.portion_amount }} {{ selectedFoodObj.measurement_unit }}</div>
                  </div>
                </div>
                <button type="button" @click="clearSelection" class="text-blue-600 hover:text-blue-800 text-sm font-semibold transition-colors px-3 py-1.5 border border-blue-200 bg-white hover:bg-blue-50 rounded-md">Auswahl ändern</button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-bold tracking-wider text-gray-700 uppercase mb-2">Konsumierte Menge</label>
              <div class="relative">
                <input v-model.number="addForm.amount" type="number" step="0.1" min="0.1" required class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow text-gray-800 font-medium" placeholder="z.B. 100" />
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                  <span class="text-gray-500 font-medium">{{ selectedFoodUnit || 'g/ml' }}</span>
                </div>
              </div>
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-gray-100 mt-6">
              <button type="button" @click="showAddModal = false" class="px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Abbrechen
              </button>
              <button type="submit" :disabled="saving" class="px-5 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors disabled:opacity-50">
                {{ saving ? 'Speichere...' : 'Eintrag hinzufügen' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import DailyFoodLog from '@/components/DailyFoodLog.vue'

const historyLogs = ref<any[]>([])
const availableFoods = ref<any[]>([])
const loading = ref(true)
const showAddModal = ref(false)
const saving = ref(false)
const selectedDateForAdd = ref<string | null>(null)

const getTodayDateStr = () => {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

const openAddModal = (dateStr: string) => {
  selectedDateForAdd.value = dateStr || getTodayDateStr()
  showAddModal.value = true
}

const addForm = ref({
  food_id: '',
  amount: null as number | null,
  consumed_at: ''
})

const searchQuery = ref('')

const filteredAvailableFoods = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) return availableFoods.value
  
  return availableFoods.value.filter(food => {
    return (
      (food.name || '').toLowerCase().includes(query) ||
      (food.variant || '').toLowerCase().includes(query) ||
      (food.barcode || '').toLowerCase().includes(query) ||
      (food.brand?.name || '').toLowerCase().includes(query)
    )
  })
})

const selectedFoodObj = computed(() => {
  if (!addForm.value.food_id) return null
  return availableFoods.value.find(f => f.id === addForm.value.food_id)
})

const selectFood = (food: any) => {
  addForm.value.food_id = food.id
  searchQuery.value = ''
}

const clearSelection = () => {
  addForm.value.food_id = ''
  addForm.value.amount = null
}

const selectedFoodUnit = computed(() => {
  return selectedFoodObj.value ? selectedFoodObj.value.measurement_unit : ''
})

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

const submitAddLog = async () => {
  if (!addForm.value.food_id) {
    alert('Bitte wähle zuerst ein Lebensmittel aus.')
    return
  }

  if (!selectedDateForAdd.value) {
    alert('Bitte wähle ein Datum aus.')
    return
  }
  
  saving.value = true
  try {
    // Set the selected date before sending
    const payload = {
      ...addForm.value,
      consumed_at: selectedDateForAdd.value
    }

    const response = await fetch('http://localhost:8000/api/food-logs', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })

    if (response.ok) {
      showAddModal.value = false
      addForm.value = { food_id: '', amount: null, consumed_at: '' } // reset form
      searchQuery.value = ''
      
      // Refresh all to keep it simple and ensure order is correct
      fetchHistoryLogs()
    } else {
      alert('Fehler beim Speichern des Eintrags.')
    }
  } catch (error) {
    console.error(error)
    alert('Fehler beim Speichern.')
  } finally {
    saving.value = false
  }
}



const fetchHistoryLogs = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/food-logs/history', {
      credentials: 'include'
    })
    if (response.ok) {
      historyLogs.value = await response.json()
    }
  } catch (error) {
    console.error('Fehler beim Laden des Verlaufs:', error)
  }
}

const formatDate = (dateStr: string) => {
  const options: Intl.DateTimeFormatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  const date = new Date(dateStr)
  return date.toLocaleDateString('de-DE', options)
}



const handleHistoryUpdateLog = (payload: { id: number, amount: number }, dateStr: string) => {
  const day = historyLogs.value.find(d => d.date === dateStr)
  if (day) {
    const log = day.logs.find((l: any) => l.id === payload.id)
    if (log) {
      log.amount = payload.amount
    }
  }
}

const handleHistoryDeleteLog = (id: number, dateStr: string) => {
  const day = historyLogs.value.find(d => d.date === dateStr)
  if (day) {
    day.logs = day.logs.filter((l: any) => l.id !== id)
    if (day.logs.length === 0) {
      historyLogs.value = historyLogs.value.filter(d => d.date !== dateStr)
    }
  }
}



onMounted(() => {
  fetchHistoryLogs()
  fetchAvailableFoods()
})
</script>
