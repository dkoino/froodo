<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Allgemein</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name des Produkts *</label>
                    <input v-model="form.name" type="text" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="z. B. Sprite">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Variante (optional)</label>
                    <input v-model="form.variant" type="text"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="z. B. Zero">
                </div>

                <div class="md:col-span-2 relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marke</label>
                    <div class="flex space-x-2">
                        <div class="relative flex-1">
                            <input v-model="brandSearchQuery" @input="showDropdown = true" @focus="showDropdown = true"
                                type="text" placeholder="Marke suchen..."
                                class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                            <ul v-if="showDropdown && filteredBrands.length > 0"
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                <li v-for="brand in filteredBrands" :key="brand.id" @click="selectBrand(brand)"
                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 text-gray-700">
                                    {{ brand.name }} <span v-if="brand.store" class="text-xs text-gray-500">({{
                                        brand.store }})</span>
                                </li>
                            </ul>
                        </div>
                        <button type="button" @click="isBrandModalOpen = true"
                            class="px-4 py-2 bg-white text-gray-700 font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition-colors whitespace-nowrap">
                            + Marke anlegen
                        </button>
                    </div>
                    <p v-if="form.brand_id" class="text-xs text-green-600 mt-1 flex items-center">
                        ✓ Marke (ID: {{ form.brand_id }}) ausgewählt
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Produktbilder</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div v-for="(photo, index) in existingPhotos" :key="'ex-' + photo.id"
                    class="relative border border-blue-200 rounded p-3 bg-blue-50/30 flex flex-col items-center group">
                    <span
                        class="absolute top-1 left-1 bg-blue-100 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Gespeichert</span>
                    <button type="button" @click="removeExistingImage(index, photo.id)"
                        class="absolute top-1 right-1 bg-red-100 text-red-600 rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-200 transition-colors"
                        title="Bild löschen">
                        &times;
                    </button>
                    <img :src="`http://localhost:8000/storage/${photo.file_path}`"
                        class="w-full h-28 object-contain rounded mb-3 mt-3">
                    <select v-model="photo.type"
                        class="block w-full text-xs border border-gray-300 rounded p-1.5 bg-white focus:ring-0 focus:border-gray-400">
                        <option value="front">Vorderseite</option>
                        <option value="product">Produktbild</option>
                        <option value="nutrition">Nährwerte</option>
                        <option value="ingredients">Zutaten</option>
                        <option value="barcode">Barcode</option>
                        <option value="general">Allgemein</option>
                    </select>
                </div>

                <div v-for="(src, index) in newImagePreviews" :key="'new-' + index"
                    class="relative border border-gray-200 rounded p-3 bg-white flex flex-col items-center group">
                    <span
                        class="absolute top-1 left-1 bg-green-100 text-green-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Neu</span>
                    <button type="button" @click="removeNewImage(index)"
                        class="absolute top-1 right-1 bg-red-100 text-red-600 rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity hover:bg-red-200">
                        &times;
                    </button>
                    <img :src="src" class="w-full h-28 object-contain rounded mb-3 mt-3">
                    <select v-model="newPhotoTypes[index]"
                        class="block w-full text-xs border border-gray-300 rounded p-1.5 bg-white focus:ring-0 focus:border-gray-400">
                        <option value="front">Vorderseite</option>
                        <option value="product">Produktbild</option>
                        <option value="nutrition">Nährwerte</option>
                        <option value="ingredients">Zutaten</option>
                        <option value="barcode">Barcode</option>
                        <option value="general">Allgemein</option>
                    </select>
                </div>

                <label
                    class="border-2 border-dashed border-gray-300 rounded p-3 bg-white hover:bg-gray-100 hover:border-gray-400 transition-colors flex flex-col items-center justify-center cursor-pointer min-h-[170px]">
                    <input type="file" accept="image/*" @change="handleSingleFileChange" class="hidden">
                    <svg class="w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-xs font-medium text-gray-500">Bild hinzufügen</span>
                </label>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Menge & Portion</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gesamtmenge *</label>
                    <input v-model="form.total_amount" type="number" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="z. B. 500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Basis-Einheit *</label>
                    <select v-model="form.measurement_unit" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                        <option value="g">Gramm (g)</option>
                        <option value="ml">Milliliter (ml)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Portions-Label (optional)</label>
                    <input v-model="form.portion_label" type="text"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="z. B. 1 Glas, 1 Riegel">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Portions-Menge in {{
                        form.measurement_unit }} (optional)</label>
                    <input v-model="form.portion_amount" type="number" step="0.1"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="z. B. 250">
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Nährwerte pro 100 {{
                form.measurement_unit }} *</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kalorien (kcal) *</label>
                    <input v-model="form.calories_p100" type="number" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fett (g) *</label>
                    <input v-model="form.fat_p100" type="number" step="0.1" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 text-gray-500">davon gesättigt</label>
                    <input v-model="form.sat_fat_p100" type="number" step="0.1"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kohlenhydrate (g) *</label>
                    <input v-model="form.carbs_p100" type="number" step="0.1" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 text-gray-500">davon Zucker</label>
                    <input v-model="form.sugar_p100" type="number" step="0.1"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 text-gray-500">Ballaststoffe</label>
                    <input v-model="form.fiber_p100" type="number" step="0.1"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Protein (g) *</label>
                    <input v-model="form.protein_p100" type="number" step="0.1" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 text-gray-500">Salz (g)</label>
                    <input v-model="form.salt_p100" type="number" step="0.1"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Zusätzliche Infos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preis (optional)</label>
                    <input v-model="form.price" type="number" step="0.01"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode (EAN)</label>
                    <input v-model="form.barcode" type="text"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="z. B. 4000000000000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Datenquelle *</label>
                    <select v-model="form.source_type" required
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white">
                        <option value="Verpackung">Verpackung</option>
                        <option value="Herstellerseite">Herstellerseite</option>
                        <option value="Datenbank">Andere Food-Datenbank</option>
                        <option value="Sonstiges">Sonstiges</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quellen-Link (optional)</label>
                    <input v-model="form.source_url" type="url"
                        class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"
                        placeholder="https://example.com">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notizen</label>
                <textarea v-model="form.notes" rows="3"
                    class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"></textarea>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 px-4 rounded-md shadow-sm transition duration-150">
                {{ isEditMode ? 'Änderungen speichern' : 'Lebensmittel anlegen' }}
            </button>
        </div>

        <BrandModal :isOpen="isBrandModalOpen" @close="isBrandModalOpen = false" @brand-created="handleBrandCreated" />
    </form>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import BrandModal from './BrandModal.vue';

const props = defineProps<{
    initialData?: any
}>();

const emit = defineEmits(['submit']);

const isEditMode = computed(() => !!props.initialData);

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
    measurement_unit: 'g' | 'ml';
    total_amount: number | null;
    portion_label: string | null;
    portion_amount: number | null;
    price: number | null;
    source_type: 'Verpackung' | 'Herstellerseite' | 'Datenbank' | 'Sonstiges';
    source_url: string;
    notes: string;
}

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
    measurement_unit: 'g',
    total_amount: null,
    portion_label: null,
    portion_amount: null,
    price: null,
    source_type: 'Verpackung',
    source_url: '',
    notes: ''
});

const brands = ref<any[]>([]);
const brandSearchQuery = ref('');
const showDropdown = ref(false);
const isBrandModalOpen = ref(false);

const existingPhotos = ref<any[]>([]);
const deletedPhotoIds = ref<number[]>([]);

const newSelectedFiles = ref<File[]>([]);
const newImagePreviews = ref<string[]>([]);
const newPhotoTypes = ref<string[]>([]);

watch(() => props.initialData, (newData) => {
    if (newData) {
        Object.keys(form.value).forEach(key => {
            if (newData[key] !== undefined) {
                (form.value as any)[key] = newData[key];
            }
        });

        if (newData.brand) {
            brandSearchQuery.value = newData.brand.name;
        }

        if (newData.photos && Array.isArray(newData.photos)) {
            existingPhotos.value = JSON.parse(JSON.stringify(newData.photos));
        }
    }
}, { immediate: true });

const fetchBrands = async () => {
    try {
        const response = await fetch('http://localhost:8000/api/brands');
        brands.value = await response.json();
    } catch (error) {
        console.error(error);
    }
};

const filteredBrands = computed(() => {
    if (!brandSearchQuery.value) return brands.value;
    return brands.value.filter(brand =>
        brand.name.toLowerCase().includes(brandSearchQuery.value.toLowerCase())
    );
});

const selectBrand = (brand: any) => {
    form.value.brand_id = brand.id;
    brandSearchQuery.value = brand.name;
    showDropdown.value = false;
};

const handleBrandCreated = (newBrand: any) => {
    brands.value.push(newBrand);
    selectBrand(newBrand);
};

const handleSingleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        newSelectedFiles.value.push(file);
        newImagePreviews.value.push(URL.createObjectURL(file));
        newPhotoTypes.value.push('general');
        target.value = '';
    }
};

const removeNewImage = (index: number) => {
    newSelectedFiles.value.splice(index, 1);
    newImagePreviews.value.splice(index, 1);
    newPhotoTypes.value.splice(index, 1);
};

const removeExistingImage = (index: number, id: number) => {
    deletedPhotoIds.value.push(id);
    existingPhotos.value.splice(index, 1);
};

const closeDropdown = (e: MouseEvent) => {
    if (!(e.target as HTMLElement).closest('.relative')) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    fetchBrands();
    document.addEventListener('click', closeDropdown);
});

const submitForm = () => {
    const formData = new FormData();

    Object.keys(form.value).forEach(key => {
        const value = form.value[key as keyof FoodForm];
        if (value !== null) {
            formData.append(key, String(value));
        }
    });

    newSelectedFiles.value.forEach((file) => {
        formData.append('photos[]', file);
    });
    newPhotoTypes.value.forEach((type) => {
        formData.append('photo_types[]', type);
    });

    deletedPhotoIds.value.forEach(id => {
        formData.append('deleted_photo_ids[]', String(id));
    });

    existingPhotos.value.forEach(photo => {
        formData.append(`existing_photo_types[${photo.id}]`, photo.type);
    });

    emit('submit', formData);
};
</script>