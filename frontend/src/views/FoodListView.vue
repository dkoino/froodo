<template>
  <div class="max-w-6xl mx-auto p-6 bg-white shadow-sm rounded-lg mt-10 mb-10 border border-gray-200">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Gespeicherte Lebensmittel</h2>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">
            <th class="p-4 w-16">Bild</th>
            <th class="p-4">Name</th>
            <th class="p-4">Variante</th>
            <th class="p-4">Brand</th>
            <th class="p-4">Menge</th>
            <th class="p-4 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
          <tr v-for="food in foods" :key="food.id" class="hover:bg-gray-50 transition duration-75 group">
            
            <td class="p-4">
              <div class="w-12 h-12 bg-gray-100 rounded border border-gray-200 overflow-hidden flex items-center justify-center">
                <img 
                  v-if="getPrimaryImage(food)" 
                  :src="getPrimaryImage(food)" 
                  alt="Produktbild"
                  class="w-full h-full object-cover"
                >
                <svg v-else class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </td>
            
            <td class="p-4 font-semibold text-gray-900">
              {{ food.name }}
            </td>
            
            <td class="p-4">
              <span v-if="food.variant" class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                {{ food.variant }}
              </span>
              <span v-else class="text-gray-400">-</span>
            </td>
            
            <td class="p-4">
              <div v-if="food.brand" class="flex items-center space-x-3">
                <img 
                  v-if="food.brand.logo_path" 
                  :src="`http://localhost:8000/storage/${food.brand.logo_path}`" 
                  alt="Logo"
                  class="w-8 h-8 object-contain rounded bg-white border border-gray-200 p-0.5"
                >
                <div v-else class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-400 rounded border border-gray-200 text-xs font-bold uppercase">
                  {{ food.brand.name.charAt(0) }}
                </div>
                <span class="text-gray-700 font-medium">{{ food.brand.name }}</span>
              </div>
              <span v-else class="text-gray-400">-</span>
            </td>
            
            <td class="p-4 font-medium text-gray-800">
              {{ food.total_amount }} {{ food.measurement_unit }}
            </td>
            
            <td class="p-4 text-right">
              <div class="flex items-center justify-end space-x-4 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                <button 
                  @click="openDetailModal(food)"
                  class="text-blue-600 hover:text-blue-800 font-medium transition-colors text-sm"
                  title="Details anzeigen"
                >
                  Details
                </button>
                <button 
                  @click="deleteFood(food.id)" 
                  class="text-red-600 hover:text-red-800 font-medium transition-colors text-sm"
                  title="Eintrag löschen"
                >
                  Löschen
                </button>
              </div>
            </td>

          </tr>
        </tbody>
      </table>

      <div v-if="foods.length === 0" class="p-12 text-center text-gray-500 border-t border-gray-100">
        Noch keine Lebensmittel in der Datenbank vorhanden.
      </div>
    </div>

    <FoodDetailModal 
      :isOpen="isDetailModalOpen" 
      :food="selectedFood" 
      @close="isDetailModalOpen = false" 
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import FoodDetailModal from '../components/FoodDetailModal.vue'

const foods = ref<any[]>([])
const selectedFood = ref<any>(null)
const isDetailModalOpen = ref(false)

const fetchFoods = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/foods')
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
  const primaryPhoto = food.photos.find((p: any) => p.type === 'front') 
                    || food.photos.find((p: any) => p.type === 'product') 
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
    const response = await fetch(`http://localhost:8000/api/foods/${id}`, {
      method: 'DELETE',
    })
    if (response.ok || response.status === 204) {
      foods.value = foods.value.filter(food => food.id !== id)
    } else {
      console.error('Fehler vom Backend:', await response.text())
    }
  } catch (error) {
    console.error('Netzwerk-Fehler beim Löschen:', error)
  }
}

onMounted(() => {
  fetchFoods()
})
</script>