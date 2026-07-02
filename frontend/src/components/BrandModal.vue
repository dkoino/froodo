<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
      
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <h3 class="text-lg font-bold text-gray-800">Neue Marke anlegen</h3>
        <button @click="close" class="text-gray-400 hover:text-gray-600 font-bold text-xl">
          &times;
        </button>
      </div>

      <form @submit.prevent="submitForm" class="p-6 space-y-4">
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Markenname *</label>
          <input 
            v-model="brandData.name" 
            type="text" 
            required 
            placeholder="z.B. ja!"
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Zugehöriger Laden (Store)</label>
          <input 
            v-model="brandData.store" 
            type="text" 
            placeholder="z.B. REWE"
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Logo (Optional)</label>
          <input 
            type="file" 
            @change="handleFileUpload"
            accept="image/*"
            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          >
        </div>

        <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
          <button 
            type="button" 
            @click="close"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            Abbrechen
          </button>
          <button 
            type="submit" 
            :disabled="isSubmitting"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-blue-400 transition-colors"
          >
            {{ isSubmitting ? 'Speichert...' : 'Marke speichern' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits(['close', 'brand-created'])

const isSubmitting = ref(false)
const brandData = ref({
  name: '',
  store: ''
})
const selectedFile = ref<File | null>(null)

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    selectedFile.value = target.files[0]
  }
}

const close = () => {
  brandData.value = { name: '', store: '' }
  selectedFile.value = null
  emit('close')
}

const submitForm = async () => {
  isSubmitting.value = true

  const formData = new FormData()
  formData.append('name', brandData.value.name)
  if (brandData.value.store) formData.append('store', brandData.value.store)
  if (selectedFile.value) formData.append('logo', selectedFile.value)

  try {
    const response = await fetch('http://localhost:8000/api/brands', {
      method: 'POST',
      body: formData,
    })

    if (response.ok) {
      const newBrand = await response.json()
      emit('brand-created', newBrand)
      close()
    } else {
      console.error('Fehler vom Server:', await response.text())
      alert('Fehler beim Speichern der Marke.')
    }
  } catch (error) {
    console.error('Netzwerkfehler:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>