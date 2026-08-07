<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-content-strong tracking-tight">Heute</h1>
    </div>

    <DailyFoodLog
      :logs="logs"
      :loading="loading"
      empty-message="Heute wurde noch nichts eingetragen."
      :date="getTodayDateStr()"
      @add-log="openAddModal"
      @edit-log="openEditModal"
    />

    <FoodLogEntryModal
      :is-open="modalOpen"
      :date="modalDate"
      :editing-log="editingLog"
      :preset-food-id="presetFoodId"
      @close="modalOpen = false"
      @saved="fetchLogs"
      @deleted="fetchLogs"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DailyFoodLog from '@/components/DailyFoodLog.vue'
import FoodLogEntryModal from '@/components/FoodLogEntryModal.vue'

const route = useRoute()
const router = useRouter()

const logs = ref<any[]>([])
const loading = ref(true)

const modalOpen = ref(false)
const modalDate = ref<string | null>(null)
const editingLog = ref<any | null>(null)
const presetFoodId = ref<string | number | null>(null)

const getTodayDateStr = () => {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

const openAddModal = (dateStr: string) => {
  editingLog.value = null
  presetFoodId.value = null
  modalDate.value = dateStr || getTodayDateStr()
  modalOpen.value = true
}

const openEditModal = (log: any) => {
  editingLog.value = log
  modalOpen.value = true
}

const fetchLogs = async () => {
  loading.value = true
  try {
    const response = await fetch('http://localhost:8000/api/food-logs', {
      credentials: 'include'
    })
    if (response.ok) {
      logs.value = await response.json()
    } else if (response.status === 401) {
      alert('Bitte logge dich ein, um dein Tagebuch zu sehen.')
    }
  } catch (error) {
    console.error('Fehler beim Laden der Food Logs:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchLogs()

  const foodId = route.query.addFood
  if (foodId) {
    presetFoodId.value = Number(foodId)
    openAddModal(getTodayDateStr())
    router.replace({ query: {} })
  }
})
</script>
