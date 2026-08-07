<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-content-strong tracking-tight">Verlauf</h1>
      <button @click="openAddModal('')" class="bg-primary text-on-primary hover:bg-primary-hover font-medium py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Eintrag hinzufügen
      </button>
    </div>

    <div v-if="historyLogs.length > 0" class="mt-6">
      <DailyFoodLog
        v-for="day in historyLogs"
        :key="day.date"
        :title="formatDate(day.date)"
        :logs="day.logs"
        :date="day.date"
        @add-log="openAddModal"
        @edit-log="openEditModal"
      />
    </div>

    <FoodLogEntryModal
      :is-open="modalOpen"
      :date="modalDate"
      :editing-log="editingLog"
      @close="modalOpen = false"
      @saved="fetchHistoryLogs"
      @deleted="fetchHistoryLogs"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import DailyFoodLog from '@/components/DailyFoodLog.vue'
import FoodLogEntryModal from '@/components/FoodLogEntryModal.vue'

const historyLogs = ref<any[]>([])
const loading = ref(true)

const modalOpen = ref(false)
const modalDate = ref<string | null>(null)
const editingLog = ref<any | null>(null)

const getTodayDateStr = () => {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

const openAddModal = (dateStr: string) => {
  editingLog.value = null
  modalDate.value = dateStr || getTodayDateStr()
  modalOpen.value = true
}

const openEditModal = (log: any) => {
  editingLog.value = log
  modalOpen.value = true
}

const fetchHistoryLogs = async () => {
  loading.value = true
  try {
    const response = await fetch('http://localhost:8000/api/food-logs/history', {
      credentials: 'include'
    })
    if (response.ok) {
      historyLogs.value = await response.json()
    }
  } catch (error) {
    console.error('Fehler beim Laden des Verlaufs:', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr: string) => {
  const options: Intl.DateTimeFormatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  const date = new Date(dateStr)
  return date.toLocaleDateString('de-DE', options)
}

onMounted(() => {
  fetchHistoryLogs()
})
</script>
