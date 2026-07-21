<template>
  <div class="max-w-6xl mx-auto p-6 bg-white shadow-sm rounded-lg mt-10 mb-10 border border-gray-200">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h2 class="text-2xl font-bold text-gray-800">Nutzerverwaltung</h2>
      <div class="flex items-center gap-4">
        <BaseButton @click="router.push('/users/create')" class="bg-blue-600 text-white hover:bg-blue-700 border border-transparent shadow-sm">
          Neuen Nutzer anlegen
        </BaseButton>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse table-fixed">
        <thead>
          <tr class="border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">
            <th class="p-4 w-1/3">Name</th>
            <th class="p-4 w-1/3">E-Mail</th>
            <th class="p-4 w-1/3 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-base text-gray-700">
          <tr v-for="user in users" :key="user.id" class="bg-white even:bg-gray-50 hover:bg-gray-100 transition duration-75">
            <td class="p-4 font-medium text-gray-900">{{ user.name }}</td>
            <td class="p-4">{{ user.email }}</td>
            <td class="p-4 text-right">
              <BaseButton class="bg-white text-gray-700 border border-gray-300 mr-2 text-sm px-3 py-1 shadow-sm" @click="router.push(`/users/${user.id}/edit`)">
                Bearbeiten
              </BaseButton>
              <BaseButton class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 text-sm px-3 py-1 shadow-sm" @click="deleteUser(user.id)">
                Löschen
              </BaseButton>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="users.length === 0" class="p-12 text-center text-gray-500 border-t border-gray-100">
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
