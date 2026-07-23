<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50" v-if="title">
      <h2 class="text-xl font-bold text-gray-800">{{ title }}</h2>
    </div>
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
              {{ emptyMessage || 'Keine Einträge vorhanden.' }}
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
        <tfoot v-if="!loading" class="bg-gray-50 font-bold border-t-2 border-gray-200 text-gray-800">
          <tr>
            <td colspan="3" class="p-3 text-left">
              <button @click="$emit('add-log', date)" class="text-sm bg-blue-600 text-white hover:bg-blue-700 font-medium py-1.5 px-4 rounded shadow-sm transition-colors inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Hinzufügen
              </button>
            </td>
            <td class="p-3 text-right">Summe:</td>
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
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  logs: any[]
  loading?: boolean
  title?: string
  emptyMessage?: string
  date?: string
}>()

const emit = defineEmits(['update-log', 'delete-log', 'add-log'])

const formatNutrient = (per100: number, amount: number, unit: string) => {
  if (per100 === null || per100 === undefined || amount === null) return '-'
  const value = (Number(per100) / 100) * Number(amount)
  
  if (Number.isInteger(value)) return value + unit
  return value.toFixed(1).replace('.0', '') + unit
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
  if (Number.isInteger(total)) return total + unit
  return total.toFixed(1).replace('.0', '') + unit
}

const editAmount = async (log: any) => {
  const input = prompt(`Menge für ${log.food.name} anpassen (bisher: ${log.amount} ${log.food.measurement_unit}):`, log.amount)
  if (input === null) return

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
