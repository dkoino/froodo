<template>
  <div class="max-w-4xl mx-auto p-6 mt-16 text-center">
    <div v-if="loading" class="py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
    </div>
    <div v-else>
      <h1 class="text-4xl font-bold text-content-strong mb-4">Hallo! Willkommen bei Froodo</h1>
      <p class="text-lg text-content-muted mb-10">Dein Begleiter für Lebensmittel und Ernährung.</p>
      
      <div class="flex justify-center gap-6">
        <BaseButton @click="router.push('/register')" variant="primary" class="shadow-md text-lg px-8 py-3 rounded-lg">
          Registrieren
        </BaseButton>
        <BaseButton @click="router.push('/login')" variant="secondary" class="shadow-md text-lg px-8 py-3 rounded-lg">
          Einloggen
        </BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '../components/BaseButton.vue'

const router = useRouter()
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await fetch('http://localhost:8000/api/me', {
      credentials: 'include',
      headers: { 'Accept': 'application/json' }
    })
    
    if (response.ok) {
      // User is logged in, redirect to food-log
      router.push('/food-log')
    } else {
      loading.value = false
    }
  } catch (error) {
    loading.value = false
  }
})
</script>
