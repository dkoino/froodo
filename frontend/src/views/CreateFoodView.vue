<template>
  <div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Neues Lebensmittel anlegen</h2>

    <form @submit.prevent="submitFood" class="space-y-6">
      
      <div class="bg-gray-50 p-4 rounded-md border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Allgemein</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name des Produkts *</label>
            <input v-model="form.name" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm" placeholder="z. B. Sprite">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Variante (optional)</label>
            <input v-model="form.variant" type="text" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm" placeholder="z. B. Zero">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Barcode (EAN)</label>
            <input v-model="form.barcode" type="text" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Brand ID (optional)</label>
            <input v-model="form.brand_id" type="number" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm" placeholder="ID der Marke">
          </div>
        </div>
      </div>

      <div class="bg-gray-50 p-4 rounded-md border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Menge & Preis</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Gesamtmenge *</label>
            <input v-model="form.quantity_total" type="number" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Basis-Einheit *</label>
            <select v-model="form.base_unit" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm bg-white">
              <option value="g">Gramm (g)</option>
              <option value="ml">Milliliter (ml)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Preis (optional)</label>
            <input v-model="form.price" type="number" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm" placeholder="0.00">
          </div>
        </div>
      </div>

      <div class="bg-gray-50 p-4 rounded-md border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Nährwerte pro 100 {{ form.base_unit }} *</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Kalorien (kcal)</label>
            <input v-model="form.calories_p100" type="number" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Fett (g)</label>
            <input v-model="form.fat_p100" type="number" step="0.1" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">davon gesättigt (g)</label>
            <input v-model="form.sat_fat_p100" type="number" step="0.1" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Kohlenhydrate (g)</label>
            <input v-model="form.carbs_p100" type="number" step="0.1" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">davon Zucker (g)</label>
            <input v-model="form.sugar_p100" type="number" step="0.1" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Ballaststoffe (g)</label>
            <input v-model="form.fiber_p100" type="number" step="0.1" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Protein (g)</label>
            <input v-model="form.protein_p100" type="number" step="0.1" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Salz (g)</label>
            <input v-model="form.salt_p100" type="number" step="0.1" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
          </div>
        </div>
      </div>

      <div class="bg-gray-50 p-4 rounded-md border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Zusätzliche Infos</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Datenquelle *</label>
            <input v-model="form.source" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm" placeholder="z. B. Verpackung">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Notizen</label>
            <textarea v-model="form.notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm"></textarea>
          </div>
        </div>
      </div>

      <div class="pt-4">
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded shadow-sm transition duration-150">
          Lebensmittel speichern
        </button>
      </div>
      
      <p v-if="successMessage" class="text-green-600 font-semibold mt-4 p-3 bg-green-50 rounded border border-green-200">{{ successMessage }}</p>
      <p v-if="errorMessage" class="text-red-600 font-semibold mt-4 p-3 bg-red-50 rounded border border-red-200">{{ errorMessage }}</p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface FoodForm {
  brand_id: number | null;
  name: string;
  variant: string;
  barcode: string;
  calories_p100: number | null;
  fat_p100: number | null;
  sat_fat_p100: number | null;
  carbs_p100: number | null;
  sugar_p100: number | null;
  fiber_p100: number | null;
  protein_p100: number | null;
  salt_p100: number | null;
  base_unit: 'g' | 'ml';
  quantity_total: number | null;
  price: number | null;
  source: string;
  notes: string;
}

const successMessage = ref<string>('');
const errorMessage = ref<string>('');

const form = ref<FoodForm>({
  brand_id: null,
  name: '',
  variant: '',
  barcode: '',
  calories_p100: null,
  fat_p100: null,
  sat_fat_p100: null,
  carbs_p100: null,
  sugar_p100: null,
  fiber_p100: null,
  protein_p100: null,
  salt_p100: null,
  base_unit: 'g',
  quantity_total: null,
  price: null,
  source: 'Verpackung',
  notes: ''
});

const submitFood = async () => {
  successMessage.value = '';
  errorMessage.value = '';
  
  try {
    const response = await fetch('http://localhost:8000/api/foods', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error('Validierungsfehler Backend:', errorData);
      throw new Error(`Fehler: ${response.status}`);
    }

    const data = await response.json();
    successMessage.value = data.message;
    
    // Optional: Formular nach erfolgreichem Speichern zurücksetzen
    // Object.assign(form.value, initialFormState);
    
  } catch (error) {
    errorMessage.value = 'Fehler beim Speichern. Bitte überprüfe die Eingaben oder die Browser-Konsole.';
    console.error(error);
  }
};
</script>