<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Heute</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-xs whitespace-nowrap">
          <thead>
            <tr class="border-b border-gray-200 text-gray-500 bg-gray-50 uppercase">
              <th class="p-3">Lebensmittel</th>
              <th class="p-3">Variante</th>
              <th class="p-3">Marke</th>
              <th class="p-3 text-right">Menge</th>
              <th class="p-3 text-right">Kcal</th>
              <th class="p-3 text-right">Fett</th>
              <th class="p-3 text-right text-gray-400">dav. ges.</th>
              <th class="p-3 text-right">Kohlenh.</th>
              <th class="p-3 text-right text-gray-400">dav. Zucker</th>
              <th class="p-3 text-right">Ballastst.</th>
              <th class="p-3 text-right">Eiweiß</th>
              <th class="p-3 text-right">Salz</th>
              <th class="p-3 text-center">Aktion</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700">
            <tr v-if="loading" class="animate-pulse">
              <td colspan="13" class="p-3 text-center text-gray-400">Lade Einträge...</td>
            </tr>
            <tr v-else-if="logs.length === 0">
              <td colspan="13" class="p-8 text-center text-gray-400">
                Heute wurde noch nichts eingetragen.
              </td>
            </tr>
            <tr v-else v-for="log in logs" :key="log.id" class="hover:bg-gray-50 transition-colors">
              <td class="p-3 truncate max-w-[150px] text-gray-900">
                {{ log.food.name }}
              </td>
              <td class="p-3 truncate text-gray-500 max-w-[120px]">
                {{ log.food.variant || '-' }}
              </td>
              <td class="p-3 truncate text-gray-500 max-w-[120px]">
                {{ log.food.brand ? log.food.brand.name : '-' }}
              </td>
              <td class="p-3 text-right">
                <span @click="editAmount(log)" class="inline-flex items-center px-2 py-0.5 rounded border border-blue-200 bg-blue-50 text-blue-800 hover:bg-blue-100 hover:border-blue-300 cursor-pointer transition-colors" title="Menge anpassen">
                  {{ log.amount }} {{ log.food.measurement_unit }}
                </span>
              </td>
              <td class="p-3 text-right text-gray-900 font-medium">{{ formatNutrient(log.food.calories_p100, log.amount, 'kcal') }}</td>
              <td class="p-3 text-right">{{ formatNutrient(log.food.fat_p100, log.amount, 'g') }}</td>
              <td class="p-3 text-right text-gray-500">{{ formatNutrient(log.food.sat_fat_p100, log.amount, 'g') }}</td>
              <td class="p-3 text-right">{{ formatNutrient(log.food.carbs_p100, log.amount, 'g') }}</td>
              <td class="p-3 text-right text-gray-500">{{ formatNutrient(log.food.sugar_p100, log.amount, 'g') }}</td>
              <td class="p-3 text-right">{{ formatNutrient(log.food.fiber_p100, log.amount, 'g') }}</td>
              <td class="p-3 text-right">{{ formatNutrient(log.food.protein_p100, log.amount, 'g') }}</td>
              <td class="p-3 text-right text-gray-500">{{ formatNutrient(log.food.salt_p100, log.amount, 'g') }}</td>
              <td class="p-3 text-center">
                <button @click="deleteLog(log.id)" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors" title="Löschen">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="!loading && logs.length > 0" class="bg-gray-50 font-bold border-t-2 border-gray-200 text-gray-800">
            <tr>
              <td colspan="4" class="p-3 text-right">Summe:</td>
              <td class="p-3 text-right">{{ formatTotalNutrient('calories_p100', 'kcal') }}</td>
              <td class="p-3 text-right">{{ formatTotalNutrient('fat_p100', 'g') }}</td>
              <td class="p-3 text-right text-gray-500">{{ formatTotalNutrient('sat_fat_p100', 'g') }}</td>
              <td class="p-3 text-right">{{ formatTotalNutrient('carbs_p100', 'g') }}</td>
              <td class="p-3 text-right text-gray-500">{{ formatTotalNutrient('sugar_p100', 'g') }}</td>
              <td class="p-3 text-right">{{ formatTotalNutrient('fiber_p100', 'g') }}</td>
              <td class="p-3 text-right">{{ formatTotalNutrient('protein_p100', 'g') }}</td>
              <td class="p-3 text-right text-gray-500">{{ formatTotalNutrient('salt_p100', 'g') }}</td>
              <td class="p-3"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div>
      <button @click="showAddModal = true" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2.5 px-5 rounded-lg shadow-sm transition-colors flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Hinzufügen
      </button>
    </div>

    <!-- Add Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
          <h3 class="text-xl font-bold text-gray-800">Lebensmittel hinzufügen</h3>
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

const logs = ref<any[]>([])
const availableFoods = ref<any[]>([])
const loading = ref(true)
const showAddModal = ref(false)
const saving = ref(false)

const addForm = ref({
  food_id: '',
  amount: null as number | null
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
  
  saving.value = true
  try {
    const response = await fetch('http://localhost:8000/api/food-logs', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(addForm.value)
    })

    if (response.ok) {
      const newLog = await response.json()
      logs.value.push(newLog) // directly add to list
      showAddModal.value = false
      addForm.value = { food_id: '', amount: null } // reset form
      searchQuery.value = ''
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

const fetchLogs = async () => {
  loading.value = true
  try {
    const response = await fetch('http://localhost:8000/api/food-logs', {
      credentials: 'include'
    })
    if (response.ok) {
      logs.value = await response.json()
    } else if (response.status === 401) {
      alert('Bitte logge dich ein, um dein Tagebuch zu sehen.')
    }
  } catch (error) {
    console.error('Fehler beim Laden der Food Logs:', error)
  } finally {
    loading.value = false
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
      logs.value = logs.value.filter(log => log.id !== id)
    } else {
      alert('Fehler beim Löschen.')
    }
  } catch (error) {
    console.error(error)
  }
}

const editAmount = async (log: any) => {
  const input = prompt(`Menge für ${log.food.name} anpassen (bisher: ${log.amount} ${log.food.measurement_unit}):`, log.amount)
  if (input === null) return // abgebrochen

  const parsed = parseFloat(input.replace(',', '.'))
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
      log.amount = parsed
    } else {
      alert('Fehler beim Aktualisieren der Menge.')
    }
  } catch (error) {
    console.error('Update Error:', error)
  }
}

const formatNutrient = (per100: number, amount: number, unit: string) => {
  if (per100 === null || per100 === undefined || amount === null) return '-'
  const value = (Number(per100) / 100) * Number(amount)
  
  if (Number.isInteger(value)) return value + unit
  return value.toFixed(1).replace('.0', '') + unit
}

const formatTotalNutrient = (nutrientKey: string, unit: string) => {
  let total = 0
  let hasValue = false
  for (const log of logs.value) {
    const per100 = log.food[nutrientKey]
    if (per100 !== null && per100 !== undefined && log.amount !== null) {
      total += (Number(per100) / 100) * Number(log.amount)
      hasValue = true
    }
  }
  
  if (!hasValue) return '-'
  if (Number.isInteger(total)) return total + unit
  return total.toFixed(1).replace('.0', '') + unit
}

onMounted(() => {
  fetchLogs()
  fetchAvailableFoods()
})
</script>
