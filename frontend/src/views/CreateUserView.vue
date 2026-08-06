<template>
  <div class="max-w-2xl mx-auto p-6 bg-surface shadow-sm rounded-lg mt-10 border border-border">
    <h2 class="text-2xl font-bold text-content mb-6">Neuen Nutzer anlegen</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">Name</label>
        <BaseInput v-model="form.name" required />
      </div>
      
      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">E-Mail</label>
        <BaseInput v-model="form.email" type="email" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">Passwort</label>
        <BaseInput v-model="form.password" type="password" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">Passwort bestätigen</label>
        <BaseInput v-model="form.password_confirmation" type="password" required />
      </div>

      <div class="flex items-center mt-4">
        <input id="is_admin" v-model="form.is_admin" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary-border border-border-strong rounded">
        <label for="is_admin" class="ml-2 block text-sm text-content-strong">
          Administrator Rechte
        </label>
      </div>

      <div class="pt-4 flex justify-end gap-3">
        <BaseButton type="button" variant="neutral" @click="router.push('/users')">
          Abbrechen
        </BaseButton>
        <BaseButton type="submit" variant="primary">
          Speichern
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
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_admin: false
})

const submit = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/users', { credentials: 'include',
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    })
    
    if (response.ok) {
      router.push('/users')
    } else {
      const errorData = await response.json()
      alert('Fehler beim Speichern: ' + (errorData.message || JSON.stringify(errorData.errors)))
    }
  } catch (error) {
    console.error('Netzwerk-Fehler:', error)
  }
}
</script>
