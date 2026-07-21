<template>
    <div class="max-w-4xl mx-auto p-6 mt-10">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Import / Export</h2>
            <p class="text-sm text-gray-500 mt-1">Datenverwaltung</p>
        </div>

        <div v-if="notification"
            :class="`mb-6 p-4 rounded-md border ${notification.type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'}`">
            <div class="flex items-center">
                <svg v-if="notification.type === 'success'" class="w-5 h-5 mr-2" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ notification.message }}</span>
            </div>
            <ul v-if="notification.details" class="mt-2 ml-7 text-sm list-disc">
                <li>Neu angelegt: {{ notification.details.created }} Datensätze</li>
                <li>Aktualisiert: {{ notification.details.updated }} Datensätze</li>
            </ul>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Daten-Export</h3>
                <p class="text-sm text-gray-500 mb-6">Lade die komplette Lebensmittel-Datenbank als CSV-Datei herunter,
                    um sie in Excel zu bearbeiten oder ein Backup zu erstellen.</p>
                <BaseButton size="lg" class="w-full bg-gray-100 text-gray-800 border border-gray-300 rounded-md" @click="handleExport">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    CSV Exportieren
                </BaseButton>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Daten-Import</h3>
                <p class="text-sm text-gray-500 mb-6">Lade eine bearbeitete CSV-Datei hoch. Bestehende IDs werden
                    geupdatet, neue Einträge (ohne ID) werden hinzugefügt.</p>

                <form @submit.prevent="handleImport">
                    <input type="file" accept=".csv" @change="onFileSelect" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-4 cursor-pointer">
                    <BaseButton type="submit" size="lg" class="w-full bg-gray-800 text-white shadow-sm rounded-md" :disabled="isUploading">
                        <span v-if="isUploading">Wird importiert...</span>
                        <span v-else class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            CSV Importieren
                        </span>
                    </BaseButton>
                </form>
            </div>

        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import BaseButton from '../components/BaseButton.vue';

const selectedFile = ref<File | null>(null);
const isUploading = ref(false);
const notification = ref<{ type: 'success' | 'error', message: string, details?: any } | null>(null);

const onFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        selectedFile.value = target.files[0];
    }
};

const handleExport = () => {
    // Simpler Redirect, der Browser behandelt den Download-Stream automatisch
    window.location.href = 'http://localhost:8000/api/export/foods';
};

const handleImport = async () => {
    if (!selectedFile.value) return;

    isUploading.value = true;
    notification.value = null;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const response = await fetch('http://localhost:8000/api/import/foods', { credentials: 'include',
            method: 'POST',
            body: formData,
        });

        const data = await response.json();

        if (response.ok) {
            notification.value = {
                type: 'success',
                message: data.message,
                details: { created: data.created, updated: data.updated }
            };
            selectedFile.value = null;
            // Input-Feld im DOM resetten
            (document.querySelector('input[type="file"]') as HTMLInputElement).value = '';
        } else {
            notification.value = { type: 'error', message: 'Fehler beim Importieren der Daten.' };
        }
    } catch (error) {
        notification.value = { type: 'error', message: 'Netzwerkfehler beim Import.' };
    } finally {
        isUploading.value = false;
    }
};
</script>