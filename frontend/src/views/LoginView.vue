<template>
  <div class="max-w-md mx-auto p-6 bg-white shadow-sm rounded-lg mt-10 border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
        <BaseInput v-model="form.email" type="email" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Passwort</label>
        <BaseInput v-model="form.password" type="password" required />
      </div>

      <div v-if="error" class="text-red-600 text-sm">
        {{ error }}
      </div>

      <div class="pt-4">
        <BaseButton type="submit" class="w-full bg-blue-600 text-white hover:bg-blue-700 border border-transparent shadow-sm flex justify-center py-2">
          Anmelden
        </BaseButton>
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
  email: '',
  password: ''
})
const error = ref('')

const submit = async () => {
  error.value = ''
  try {
    const response = await fetch('http://localhost:8000/api/login', {
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
      router.push('/foods')
    } else {
      const errorData = await response.json()
      error.value = errorData.message || 'Login fehlgeschlagen'
    }
  } catch (err) {
    console.error('Netzwerk-Fehler:', err)
    error.value = 'Netzwerk-Fehler'
  }
}
</script>
