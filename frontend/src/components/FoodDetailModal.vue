<template>
  <div v-if="isOpen && food" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
      
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
        <div>
          <h3 class="text-xl font-bold text-gray-800">{{ food.name }}</h3>
          <p v-if="food.variant" class="text-sm text-gray-500 mt-0.5">{{ food.variant }}</p>
        </div>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 font-bold text-xl">
          &times;
        </button>
      </div>

      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-2 space-y-4">
            <div class="bg-gray-50 p-4 rounded-md border border-gray-100 grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="block text-gray-400 text-xs font-medium uppercase">Marke</span>
                <span class="text-gray-800 font-semibold">{{ food.brand ? food.brand.name : '-' }}</span>
              </div>
              <div>
                <span class="block text-gray-400 text-xs font-medium uppercase">Gesamtmenge</span>
                <span class="text-gray-800 font-semibold">{{ food.total_amount }} {{ food.measurement_unit }}</span>
              </div>
              <div>
                <span class="block text-gray-400 text-xs font-medium uppercase">Preis</span>
                <span class="text-gray-800 font-semibold">{{ food.price ? `${food.price} €` : '-' }}</span>
              </div>
              <div>
                <span class="block text-gray-400 text-xs font-medium uppercase">Barcode (EAN)</span>
                <span class="text-gray-800 font-semibold">{{ food.barcode || '-' }}</span>
              </div>
            </div>

            <div>
              <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Nährwerte pro 100 {{ food.measurement_unit }}</h4>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-blue-50/50 p-3 rounded border border-blue-100/50 text-center">
                  <span class="block text-xs text-blue-600 font-medium">Kalorien</span>
                  <span class="text-lg font-bold text-blue-900">{{ food.calories_p100 }} kcal</span>
                </div>
                <div class="bg-amber-50/50 p-3 rounded border border-amber-100/50 text-center">
                  <span class="block text-xs text-amber-600 font-medium">Fett</span>
                  <span class="text-lg font-bold text-amber-900">{{ food.fat_p100 }} g</span>
                </div>
                <div class="bg-purple-50/50 p-3 rounded border border-purple-100/50 text-center">
                  <span class="block text-xs text-purple-600 font-medium">Kohlenhydrate</span>
                  <span class="text-lg font-bold text-purple-900">{{ food.carbs_p100 }} g</span>
                </div>
                <div class="bg-emerald-50/50 p-3 rounded border border-emerald-100/50 text-center">
                  <span class="block text-xs text-emerald-600 font-medium">Protein</span>
                  <span class="text-lg font-bold text-emerald-900">{{ food.protein_p100 }} g</span>
                </div>
              </div>

              <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-gray-500 bg-gray-50 p-3 rounded border border-gray-100">
                <div>davon gesättigt: <span class="font-semibold text-gray-700">{{ food.sat_fat_p100 || '0' }} g</span></div>
                <div>davon Zucker: <span class="font-semibold text-gray-700">{{ food.sugar_p100 || '0' }} g</span></div>
                <div>Ballaststoffe: <span class="font-semibold text-gray-700">{{ food.fiber_p100 || '0' }} g</span></div>
                <div>Salz: <span class="font-semibold text-gray-700">{{ food.salt_p100 || '0' }} g</span></div>
              </div>
            </div>

            <div v-if="food.notes" class="text-sm bg-gray-50 p-4 rounded border border-gray-100">
              <span class="block text-gray-400 text-xs font-medium uppercase mb-1">Notizen</span>
              <p class="text-gray-700 whitespace-pre-line">{{ food.notes }}</p>
            </div>
          </div>

          <div class="space-y-4">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Bilder-Galerie</h4>
            <div v-if="food.photos && food.photos.length > 0" class="space-y-3">
              <div v-for="photo in food.photos" :key="photo.id" class="border border-gray-200 rounded p-2 bg-white">
                <img :src="`http://localhost:8000/storage/${photo.file_path}`" class="w-full h-32 object-contain rounded bg-gray-50">
                <span class="block text-center text-xs font-semibold text-gray-500 uppercase mt-2 bg-gray-100 py-1 rounded">
                  {{ translateType(photo.type) }}
                </span>
              </div>
            </div>
            <div v-else class="text-center p-8 bg-gray-50 border border-gray-100 rounded text-gray-400 text-sm">
              Keine Bilder vorhanden.
            </div>
          </div>
        </div>
      </div>

      <div class="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50 sticky bottom-0">
        <button @click="$emit('close')" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-md text-sm shadow-sm transition">
          Schließen
        </button>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  isOpen: boolean
  food: any
}>()

defineEmits(['close'])

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
</script>