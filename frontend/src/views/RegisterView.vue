<template>
  <div class="max-w-md mx-auto p-6 bg-white shadow-sm rounded-lg mt-10 border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Registrieren</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <BaseInput v-model="form.name" required />
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
        <BaseInput v-model="form.email" type="email" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Passwort</label>
        <BaseInput v-model="form.password" type="password" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Passwort bestätigen</label>
        <BaseInput v-model="form.password_confirmation" type="password" required />
      </div>

      <div class="pt-4">
        <BaseButton type="submit" class="w-full bg-blue-600 text-white hover:bg-blue-700 shadow-sm py-2">
          Registrieren
        </BaseButton>
      </div>
      
      <div class="text-center mt-4 text-sm text-gray-600">
        Bereits einen Account? <RouterLink to="/login" class="text-blue-600 hover:underline">Hier einloggen</RouterLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '../components/BaseButton.vue'
import BaseInput from '../components/BaseInput.vue'

const router = useRouter()
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const submit = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/register', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    })
    
    if (response.ok) {
      window.dispatchEvent(new Event('auth-change'))
      router.push('/food-log')
    } else {
      const errorData = await response.json()
      alert('Fehler bei der Registrierung: ' + (errorData.message || JSON.stringify(errorData.errors)))
    }
  } catch (error) {
    console.error('Netzwerk-Fehler:', error)
  }
}
</script>
