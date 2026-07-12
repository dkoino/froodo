<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Allgemein</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <BaseInput v-model="form.name" required label="Produktname" placeholder="z. B. Sprite" />
                <BaseInput v-model="form.variant" label="Variante" placeholder="z. B. Zero" />

                <AutocompleteInput
                    v-model="form.brand_name"
                    :items="filteredBrands"
                    label="Marke"
                    placeholder="Marke suchen..."
                    @select="selectBrand"
                >
                <template #item="{ item }">
                    {{ item.name }} <span v-if="item.manufacturer" class="text-xs text-gray-500">({{ item.manufacturer.name }})</span>
                </template>
                <template #feedback>
                    <p v-if="exactBrandMatch" class="text-xs text-green-600 mt-1 flex items-center">
                        ✓ Marke vorhanden
                    </p>
                    <p v-else-if="form.brand_name" class="text-xs text-amber-600 mt-1 flex items-center">
                        + Neue Marke wird angelegt
                    </p>
                </template>
            </AutocompleteInput>
            
            <BaseInput 
                v-model="form.manufacturer_name" 
                label="Hersteller" 
                :disabled="!!exactBrandMatch"
                :placeholder="exactBrandMatch ? 'Wird durch Marke bestimmt' : 'Hersteller (Optional)'" 
            />
                <AutocompleteInput
                    v-model="form.main_category_name"
                    :items="filteredMainCategories"
                    label="Hauptkategorie"
                    placeholder="z. B. Milchprodukt"
                    @select="selectMainCategory"
                />
                <AutocompleteInput
                    v-model="form.sub_category_name"
                    :items="filteredSubCategories"
                    label="Unterkategorie (optional)"
                    placeholder="z. B. Käse"
                    @select="selectSubCategory"
                />

                <BaseSelect v-model="form.meat_type" label="Fleischsorte">
                    <option value="Unbekannt">Unbekannt</option>
                    <option value="Schwein">Schwein</option>
                    <option value="Hähnchen">Hähnchen</option>
                    <option value="Pute">Pute</option>
                    <option value="Ente">Ente</option>
                    <option value="Rind">Rind</option>
                    <option value="Fisch">Fisch</option>
                    <option value="Gemischt">Gemischt</option>
                    <option value="Anderes">Anderes</option>
                    <option value="Nein">Nein (Vegetarisch/Vegan)</option>
                </BaseSelect>
                <BaseInput v-model="form.state" label="Zustand (optional)" placeholder="z. B. tiefgefroren, frisch" />
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Menge & Portion</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
                <BaseInput v-model="form.total_amount" type="number" required label="Gesamtmenge" placeholder="z. B. 500" />
                <BaseSelect v-model="form.measurement_unit" required label="Basis-Einheit">
                    <option value="g">Gramm (g)</option>
                    <option value="ml">Milliliter (ml)</option>
                </BaseSelect>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-4 border-t border-gray-200">
                <BaseInput v-model="form.portion_label" label="Portions-Label (optional)" placeholder="z. B. 1 Glas, 1 Riegel" />
                <BaseInput v-model="form.portion_amount" type="number" step="0.1" :label="`Portions-Menge in ${form.measurement_unit} (optional)`" placeholder="z. B. 250" />
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Nährwerte pro 100 {{
                form.measurement_unit }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-2 gap-3">
                <BaseInput v-model="form.calories_p100" type="number" required label="Kalorien (kcal)" />
                <div></div>
                <BaseInput v-model="form.fat_p100" type="number" step="0.1" required label="Fett (g)" />
                <BaseInput v-model="form.sat_fat_p100" type="number" step="0.1" label="davon gesättigt" />
                <BaseInput v-model="form.carbs_p100" type="number" step="0.1" required label="Kohlenhydrate (g)" />
                <BaseInput v-model="form.sugar_p100" type="number" step="0.1" label="davon Zucker" />
                <BaseInput v-model="form.fiber_p100" type="number" step="0.1" label="Ballaststoffe" />
                <div></div>
                <BaseInput v-model="form.protein_p100" type="number" step="0.1" required label="Protein (g)" />
                <div></div>
                <BaseInput v-model="form.salt_p100" type="number" step="0.1" label="Salz (g)" />
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Zusätzliche Infos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-5">
                <BaseInput v-model="form.price" type="number" step="0.01" label="Preis (optional)" placeholder="0.00" />
                <BaseInput v-model="form.barcode" type="text" label="Barcode (EAN)" placeholder="z. B. 4000000000000" />
                <BaseSelect v-model="form.source_type" required label="Datenquelle">
                    <option value="Verpackung">Verpackung</option>
                    <option value="Herstellerseite">Herstellerseite</option>
                    <option value="Datenbank">Andere Food-Datenbank</option>
                    <option value="Sonstiges">Sonstiges</option>
                </BaseSelect>
                <BaseInput v-model="form.source_url" type="url" label="Quellen-Link (optional)" placeholder="https://example.com" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notizen</label>
                <textarea v-model="form.notes" rows="3"
                    class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white"></textarea>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Produktbilder</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div v-for="(photo, index) in existingPhotos" :key="'ex-' + photo.id"
                    class="relative border border-blue-200 rounded p-3 bg-blue-50/30 flex flex-col items-center group">
                    <span
                        class="absolute top-1 left-1 bg-blue-100 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Gespeichert</span>
                    <BaseButton variant="danger-icon" size="none" customClass="absolute top-1 right-1 rounded-full w-5 h-5 flex items-center justify-center text-xs" @click="removeExistingImage(index, photo.id)" title="Bild löschen">
                        &times;
                    </BaseButton>
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
                    <BaseButton variant="danger-icon" size="none" customClass="absolute top-1 right-1 rounded-full w-5 h-5 flex items-center justify-center text-xs md:opacity-0 md:group-hover:opacity-100 transition-opacity" @click="removeNewImage(index)" title="Bild löschen">
                        &times;
                    </BaseButton>
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

        <div class="pt-2">
            <BaseButton type="submit" variant="primary" size="lg" customClass="w-full py-3 shadow-sm">
                {{ isEditMode ? 'Änderungen speichern' : 'Lebensmittel anlegen' }}
            </BaseButton>
        </div>

    </form>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import BaseInput from './BaseInput.vue';
import BaseSelect from './BaseSelect.vue';
import AutocompleteInput from './AutocompleteInput.vue';
import BaseButton from './BaseButton.vue';

const props = defineProps<{
    initialData?: any
}>();

const emit = defineEmits(['submit']);

const isEditMode = computed(() => !!props.initialData);

interface FoodForm {
    brand_name: string;
    manufacturer_name: string;
    main_category_name: string;
    sub_category_name: string;
    meat_type: 'Schwein' | 'Hähnchen' | 'Pute' | 'Ente' | 'Rind' | 'Fisch' | 'Gemischt' | 'Anderes' | 'Nein' | 'Unbekannt';
    state: string;
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
    brand_name: '',
    manufacturer_name: '',
    main_category_name: '',
    sub_category_name: '',
    meat_type: 'Unbekannt',
    state: '',
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

const mainCategories = ref<any[]>([]);
const subCategories = ref<any[]>([]);

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
            form.value.brand_name = newData.brand.name;
            if (newData.brand.manufacturer) {
                form.value.manufacturer_name = newData.brand.manufacturer.name;
            }
        }

        if (newData.main_category) {
            form.value.main_category_name = newData.main_category.name;
        }

        if (newData.sub_category) {
            form.value.sub_category_name = newData.sub_category.name;
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

const fetchCategories = async () => {
    try {
        const [mainRes, subRes] = await Promise.all([
            fetch('http://localhost:8000/api/main-categories'),
            fetch('http://localhost:8000/api/sub-categories')
        ]);
        mainCategories.value = await mainRes.json();
        subCategories.value = await subRes.json();
    } catch (error) {
        console.error(error);
    }
};

const filteredMainCategories = computed(() => {
    if (!form.value.main_category_name) return mainCategories.value;
    return mainCategories.value.filter(cat =>
        cat.name.toLowerCase().includes(form.value.main_category_name.toLowerCase())
    );
});

const filteredSubCategories = computed(() => {
    let cats = subCategories.value;

    if (form.value.main_category_name) {
        const mainCat = mainCategories.value.find(
            c => c.name.toLowerCase() === form.value.main_category_name.toLowerCase()
        );
        if (mainCat) {
            cats = cats.filter(cat => cat.main_category_id === mainCat.id);
        }
    }

    if (form.value.sub_category_name) {
        cats = cats.filter(cat =>
            cat.name.toLowerCase().includes(form.value.sub_category_name.toLowerCase())
        );
    }
    
    return cats;
});

const selectMainCategory = (cat: any) => {
    form.value.main_category_name = cat.name;
};

const selectSubCategory = (cat: any) => {
    form.value.sub_category_name = cat.name;
};

const filteredBrands = computed(() => {
    if (!form.value.brand_name) return brands.value;
    return brands.value.filter(brand =>
        brand.name.toLowerCase().includes(form.value.brand_name.toLowerCase())
    );
});

const exactBrandMatch = computed(() => {
    if (!form.value.brand_name) return null;
    return brands.value.find(b => b.name.toLowerCase() === form.value.brand_name.toLowerCase()) || null;
});

watch(exactBrandMatch, (newBrand) => {
    if (newBrand && newBrand.manufacturer) {
        form.value.manufacturer_name = newBrand.manufacturer.name;
    } else if (newBrand) {
        form.value.manufacturer_name = '';
    }
});

const selectBrand = (brand: any) => {
    form.value.brand_name = brand.name;
    form.value.manufacturer_name = brand.manufacturer?.name || '';
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

onMounted(() => {
    fetchBrands();
    fetchCategories();
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