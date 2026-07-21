<template>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-sm rounded-lg mt-10 mb-10 border border-gray-200">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Lebensmittel bearbeiten</h2>
                <p class="text-sm text-gray-500 mt-1">ID: {{ foodId }}</p>
            </div>
            <BaseButton size="none" class="text-gray-500 hover:text-gray-700" @click="router.push('/foods')">
                &larr; Zurück
            </BaseButton>
        </div>

        <div v-if="isLoading" class="p-12 text-center text-gray-500">
            Daten werden geladen...
        </div>

        <FoodForm v-else-if="foodData" :initialData="foodData" @submit="handleUpdate" />

        <div v-else class="p-12 text-center text-red-500">
            Lebensmittel konnte nicht geladen werden.
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import FoodForm from '../components/FoodForm.vue';
import BaseButton from '../components/BaseButton.vue';

const route = useRoute();
const router = useRouter();
const foodId = route.params.id;

const foodData = ref<any>(null);
const isLoading = ref(true);

const fetchFood = async () => {
    try {
        const response = await fetch(`http://localhost:8000/api/foods/${foodId}`, { credentials: 'include', credentials: 'include' });
        if (response.ok) {
            foodData.value = await response.json();
        } else {
            console.error('Fehler beim Laden des Lebensmittels');
        }
    } catch (error) {
        console.error('Netzwerkfehler:', error);
    } finally {
        isLoading.value = false;
    }
};

const handleUpdate = async (formData: FormData) => {
    formData.append('_method', 'PUT');

    try {
        const response = await fetch(`http://localhost:8000/api/foods/${foodId}`, { credentials: 'include',
            method: 'POST',
            body: formData,
        });

        if (response.ok) {
            router.push('/foods');
        } else {
            const errorData = await response.json();
            console.error('Validierungsfehler:', errorData);
            alert('Fehler beim Aktualisieren. Bitte Konsole prüfen.');
        }
    } catch (error) {
        console.error('Netzwerkfehler:', error);
    }
};

onMounted(() => {
    fetchFood();
});
</script>