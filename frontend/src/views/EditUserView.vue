<template>
  <div class="max-w-2xl mx-auto p-6 bg-surface shadow-sm rounded-lg mt-10 border border-border">
    <h2 class="text-2xl font-bold text-content mb-6">Nutzer bearbeiten</h2>

    <form @submit.prevent="submit" class="space-y-4" v-if="loaded">
      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">Name</label>
        <BaseInput v-model="form.name" required />
      </div>
      
      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">E-Mail</label>
        <BaseInput v-model="form.email" type="email" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">Neues Passwort (optional)</label>
        <BaseInput v-model="form.password" type="password" />
      </div>

      <div>
        <label class="block text-sm font-medium text-content-secondary mb-1">Passwort bestätigen</label>
        <BaseInput v-model="form.password_confirmation" type="password" />
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
          Änderungen speichern
        </BaseButton>
      </div>
    </form>
    <div v-else class="py-12 text-center text-content-muted">
      Lade Nutzerdaten...
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import BaseButton from '../components/BaseButton.vue'
import BaseInput from '../components/BaseInput.vue'

const router = useRouter()
const route = useRoute()
const userId = route.params.id

const loaded = ref(false)
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_admin: false
})

const fetchUser = async () => {
  try {
    const response = await fetch(`http://localhost:8000/api/users/${userId}`, { credentials: 'include', credentials: 'include' })
    const data = await response.json()
    const user = data.data || data
    form.value.name = user.name
    form.value.email = user.email
    form.value.is_admin = !!user.is_admin
    loaded.value = true
  } catch (error) {
    console.error('Fehler beim Laden:', error)
    alert('Fehler beim Laden der Nutzerdaten')
    router.push('/users')
  }
}

const submit = async () => {
  try {
    const payload: any = {
      name: form.value.name,
      email: form.value.email,
      is_admin: form.value.is_admin
    }
    if (form.value.password) {
      payload.password = form.value.password
      payload.password_confirmation = form.value.password_confirmation
    }

    const response = await fetch(`http://localhost:8000/api/users/${userId}`, { credentials: 'include',
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
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

onMounted(() => {
  fetchUser()
})
</script>
