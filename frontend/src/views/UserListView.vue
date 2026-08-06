<template>
  <div class="max-w-6xl mx-auto p-6 bg-surface shadow-sm rounded-lg mt-10 mb-10 border border-border">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h2 class="text-2xl font-bold text-content">Nutzerverwaltung</h2>
      <div class="flex items-center gap-4">
        <BaseButton @click="router.push('/users/create')" variant="primary">
          Neuen Nutzer anlegen
        </BaseButton>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse table-fixed">
        <thead>
          <tr class="border-b border-border text-sm font-semibold text-content-muted bg-surface-muted">
            <th class="p-4 w-1/3">Name</th>
            <th class="p-4 w-1/3">E-Mail</th>
            <th class="p-4 w-1/3 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border-muted text-base text-content-secondary">
          <tr v-for="user in users" :key="user.id" class="bg-surface even:bg-surface-muted hover:bg-surface-subtle transition duration-75">
            <td class="p-4 font-medium text-content-strong">
              {{ user.name }}
              <span v-if="user.is_admin" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-soft-strong text-primary-strong">
                Admin
              </span>
            </td>
            <td class="p-4">{{ user.email }}</td>
            <td class="p-4 text-right">
              <BaseButton variant="secondary" class="mr-2 text-sm px-3 py-1" @click="router.push(`/users/${user.id}/edit`)">
                Bearbeiten
              </BaseButton>
              <BaseButton variant="danger-soft" class="text-sm px-3 py-1" @click="deleteUser(user.id)">
                Löschen
              </BaseButton>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="users.length === 0" class="p-12 text-center text-content-muted border-t border-border-muted">
        Keine Nutzer gefunden.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '../components/BaseButton.vue'

const users = ref<any[]>([])
const router = useRouter()

const fetchUsers = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/users', { credentials: 'include', credentials: 'include' })
    const data = await response.json()
    users.value = Array.isArray(data) ? data : data.data || data
  } catch (error) {
    console.error('Fehler beim Laden der Nutzer:', error)
  }
}

const deleteUser = async (id: number) => {
  if (!confirm('Willst du diesen Nutzer wirklich löschen?')) {
    return
  }
  try {
    const response = await fetch(`http://localhost:8000/api/users/${id}`, { credentials: 'include',
      method: 'DELETE',
    })
    if (response.ok || response.status === 204) {
      users.value = users.value.filter(u => u.id !== id)
    } else {
      console.error('Fehler vom Backend:', await response.text())
    }
  } catch (error) {
    console.error('Netzwerk-Fehler beim Löschen:', error)
  }
}

onMounted(() => {
  fetchUsers()
})
</script>
