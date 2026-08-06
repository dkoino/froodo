<template>
  <div class="max-w-4xl mx-auto p-6 bg-surface shadow-sm rounded-lg mt-10 mb-10 border border-border">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-content">Neues Lebensmittel anlegen</h2>
      <p class="text-sm text-content-muted mt-1">Füge ein neues Produkt zur Datenbank hinzu.</p>
    </div>

    <FoodForm @submit="handleCreate" />
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import FoodForm from '../components/FoodForm.vue';

const router = useRouter();

const handleCreate = async (formData: FormData) => {
  try {
    const response = await fetch('http://localhost:8000/api/foods', { credentials: 'include',
      method: 'POST',
      body: formData, // FormData setzt den Content-Type automatisch auf multipart/form-data
    });

    if (response.ok) {
      router.push('/foods');
    } else {
      const errorData = await response.json();
      console.error('Validierungsfehler:', errorData);
      alert('Fehler beim Speichern. Bitte Konsole prüfen.');
    }
  } catch (error) {
    console.error('Netzwerkfehler:', error);
  }
};
</script>