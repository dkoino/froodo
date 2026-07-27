<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Allgemein</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Links: Eingabefelder -->
                <div class="space-y-4">
                    <div class="space-y-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                        <BaseInput v-model="form.name" required label="Produktname" placeholder="z. B. Sprite" />
                        <BaseInput v-model="form.variant" label="Variante" placeholder="z. B. Zero" />
                    </div>

                    <div class="space-y-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
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
                        
                        <AutocompleteInput
                            v-model="form.manufacturer_name"
                            :items="filteredManufacturers"
                            label="Hersteller"
                            :disabled="!!exactBrandMatch"
                            :placeholder="exactBrandMatch ? 'Wird durch Marke bestimmt' : 'Hersteller suchen...'"
                            @select="selectManufacturer"
                        >
                            <template #item="{ item }">
                                {{ item.name }}
                            </template>
                            <template #feedback>
                                <p v-if="exactManufacturerMatch" class="text-xs text-green-600 mt-1 flex items-center">
                                    ✓ Hersteller vorhanden
                                </p>
                                <p v-else-if="form.manufacturer_name && !exactBrandMatch" class="text-xs text-amber-600 mt-1 flex items-center">
                                    + Neuer Hersteller wird angelegt
                                </p>
                            </template>
                        </AutocompleteInput>
                    </div>
                </div>

                <!-- Rechts: Foto Upload -->
                <div class="flex items-start justify-center pt-2">
                    <PhotoUploadSlot 
                        :photo="packagingPhoto" 
                        label="Verpackung" 
                        @upload="(e) => openCropper(e, 'packaging')" 
                        @remove="removePhoto('packaging')" 
                        class="w-80 max-w-full"
                    />
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Kategorisierung & Zutaten</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Links: Eingabefelder -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
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
                            label="Unterkategorie"
                            placeholder="z. B. Käse"
                            @select="selectSubCategory"
                        />
                    </div>

                    <div class="space-y-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
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
                            <option value="Kein Fleisch">Kein Fleisch</option>
                            <option value="Vegan">Vegan</option>
                        </BaseSelect>
                        <BaseInput v-model="form.state" label="Zustand" placeholder="z. B. tiefgefroren, frisch" />
                    </div>
                </div>

                <!-- Rechts: Foto Upload -->
                <div class="flex items-start justify-center pt-2">
                    <PhotoUploadSlot 
                        :photo="ingredientsPhoto" 
                        label="Zutatenliste" 
                        @upload="(e) => openCropper(e, 'ingredients')" 
                        @remove="removePhoto('ingredients')" 
                        class="w-80 max-w-full"
                    />
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-200 pb-2 text-gray-700">Menge & Portion</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Links: Eingabefelder -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                        <BaseInput v-model="form.total_amount" type="number" required label="Gesamtmenge" placeholder="z. B. 500" />
                        <BaseSelect v-model="form.measurement_unit" required label="Basis-Einheit">
                            <option value="g">Gramm (g)</option>
                            <option value="ml">Milliliter (ml)</option>
                        </BaseSelect>
                    </div>
                    <div class="grid grid-cols-2 gap-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                        <BaseInput v-model="form.portion_label" label="Portions-Label" placeholder="z. B. 1 Glas, 1 Riegel" />
                        <BaseInput v-model="form.portion_amount" type="number" step="1" :label="`Portions-Menge in ${form.measurement_unit}`" placeholder="z. B. 250" />
                    </div>
                </div>

                <!-- Rechts: Foto Upload -->
                <div class="flex items-start justify-center pt-2">
                    <PhotoUploadSlot 
                        :photo="contentPhoto" 
                        label="Essen selbst" 
                        @upload="(e) => openCropper(e, 'content')" 
                        @remove="removePhoto('content')" 
                        class="w-80 max-w-full"
                    />
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Nährwerte pro 100 {{ form.measurement_unit }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Links: Eingabefelder (Viertelgröße) -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                        <div class="col-span-2 sm:col-span-1">
                            <BaseInput v-model="form.calories_p100" type="number" required label="Kalorien (kcal)" />
                            <p v-if="calorieDiscrepancy" class="text-[10px] text-amber-600 mt-1 leading-tight">
                                ⚠️ Rechnerisch ca. {{ Math.round(calculatedCalories) }} kcal. Stimmen die Makros?
                            </p>
                        </div>
                        <div class="hidden sm:block"></div>
                        
                        <BaseInput v-model="form.fat_p100" type="number" step="0.1" required label="Fett (g)" />
                        <BaseInput v-model="form.sat_fat_p100" type="number" step="0.1" label="davon gesättigt" />
                        
                        <BaseInput v-model="form.carbs_p100" type="number" step="0.1" required label="Kohlenhydrate (g)" />
                        <BaseInput v-model="form.sugar_p100" type="number" step="0.1" label="davon Zucker" />
                        
                        <BaseInput v-model="form.protein_p100" type="number" step="0.1" required label="Protein (g)" />
                        <BaseInput v-model="form.fiber_p100" type="number" step="0.1" label="Ballaststoffe" />
                        
                        <BaseInput v-model="form.salt_p100" type="number" step="0.1" label="Salz (g)" />
                    </div>
                </div>

                <!-- Rechts: Foto Upload -->
                <div class="flex items-start justify-center pt-2">
                    <PhotoUploadSlot 
                        :photo="nutritionPhoto" 
                        label="Nährwert-Tabelle" 
                        @upload="(e) => openCropper(e, 'nutrition')" 
                        @remove="removePhoto('nutrition')" 
                        class="w-80 max-w-full"
                    />
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Zusätzliche Informationen</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Links: Eingabefelder -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3 p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                        <BaseInput v-model="form.price" type="number" step="0.01" label="Preis" placeholder="0.00" />
                        <BaseInput v-model="form.barcode" type="text" label="Barcode (EAN)" placeholder="z. B. 4000000000000" />
                        
                        <BaseSelect v-model="form.source_type" required label="Datenquelle">
                            <option value="Verpackung">Verpackung</option>
                            <option value="Herstellerseite">Herstellerseite</option>
                            <option value="Datenbank">Andere Food-Datenbank</option>
                            <option value="Sonstiges">Sonstiges</option>
                        </BaseSelect>
                        <BaseInput v-model="form.source_url" type="url" label="Quellen-Link" placeholder="https://example.com" />
                        
                        <div class="col-span-2 mt-2 flex flex-col xl:flex-row gap-4 items-start">
                            <div class="flex-1 w-full">
                                <label class="block text-sm mb-1 font-normal text-gray-400">Notizen</label>
                                <textarea v-model="form.notes" rows="4"
                                    class="block w-full border border-gray-300 rounded-md p-2.5 focus:ring-0 focus:border-gray-400 bg-white h-[104px]"></textarea>
                            </div>

                            <div class="flex-shrink-0">
                                <label class="block text-sm font-normal text-gray-400 mb-1 text-center">Weitere Fotos (Max. 3)</label>
                                <div class="flex gap-2 flex-wrap max-w-[200px] justify-center">
                                    <PhotoUploadSlot 
                                        v-for="(photo, index) in otherPhotos" 
                                        :key="photo.id || index"
                                        :photo="photo" 
                                        label="Weiteres" 
                                        @remove="removeOtherPhoto(index)" 
                                        class="w-24 max-w-full"
                                    />
                                    <PhotoUploadSlot 
                                        v-if="otherPhotos.length < 3"
                                        :photo="null" 
                                        label="Weiteres" 
                                        @upload="(e) => openCropper(e, 'other')" 
                                        class="w-24 max-w-full"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rechts: Foto Upload -->
                <div class="flex items-start justify-center pt-2">
                    <PhotoUploadSlot 
                        :photo="barcodePhoto" 
                        label="Barcode" 
                        @upload="(e) => openCropper(e, 'barcode')" 
                        @remove="removePhoto('barcode')" 
                        class="w-80 max-w-full"
                    />
                </div>
            </div>
        </div>

        <div class="pt-2">
            <BaseButton type="submit" size="lg" class="w-full py-3 bg-gray-800 text-white shadow-sm rounded-md">
                {{ isEditMode ? 'Änderungen speichern' : 'Lebensmittel anlegen' }}
            </BaseButton>
        </div>

    </form>

    <!-- Cropper Modal -->
    <Teleport to="body">
        <div v-if="isCropModalOpen" class="fixed inset-0 z-50 bg-black/80 flex flex-col">
            <div class="flex justify-between items-center p-4 bg-gray-900 text-white">
                <h3 class="font-semibold text-lg">Bild zuschneiden</h3>
                <button type="button" @click="cancelCrop" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-grow flex items-center justify-center p-4 overflow-hidden relative">
                <cropper
                    ref="cropperRef"
                    class="h-full w-full max-h-[70vh]"
                    :src="cropImageUrl"
                    :stencil-props="{ aspectRatio: 0 }"
                />
            </div>
            <div class="p-4 bg-gray-900 flex justify-between items-center space-x-4">
                <!-- Feinjustierung (Halten zum Drehen) -->
                <div class="flex items-center space-x-2">
                    <BaseButton 
                        @mousedown.prevent="startRotation(-1)" 
                        @mouseup.prevent="stopRotation" 
                        @mouseleave.prevent="stopRotation" 
                        @touchstart.prevent="startRotation(-1)" 
                        @touchend.prevent="stopRotation" 
                        class="bg-gray-800 text-gray-300 hover:text-white border border-gray-700 hover:bg-gray-700 px-3 py-2 text-xs select-none"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    </BaseButton>
                    <BaseButton 
                        @mousedown.prevent="startRotation(1)" 
                        @mouseup.prevent="stopRotation" 
                        @mouseleave.prevent="stopRotation" 
                        @touchstart.prevent="startRotation(1)" 
                        @touchend.prevent="stopRotation"
                        class="bg-gray-800 text-gray-300 hover:text-white border border-gray-700 hover:bg-gray-700 px-3 py-2 text-xs select-none"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"></path></svg>
                    </BaseButton>
                </div>
                
                <div class="flex items-center space-x-2">
                    <BaseButton @click="rotate90" class="bg-gray-800 text-gray-300 hover:text-white border border-gray-700 hover:bg-gray-700 flex items-center px-3">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        90°
                    </BaseButton>
                    <BaseButton @click="cancelCrop" class="bg-gray-700 text-white hover:bg-gray-600 px-3">Abbrechen</BaseButton>
                    <BaseButton @click="confirmCrop" class="bg-blue-600 text-white hover:bg-blue-700 px-3">Übernehmen</BaseButton>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import imageCompression from 'browser-image-compression';
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

import BaseInput from './BaseInput.vue';
import BaseSelect from './BaseSelect.vue';
import AutocompleteInput from './AutocompleteInput.vue';
import BaseButton from './BaseButton.vue';
import PhotoUploadSlot from './PhotoUploadSlot.vue';

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
    meat_type: 'Schwein' | 'Hähnchen' | 'Pute' | 'Ente' | 'Rind' | 'Fisch' | 'Gemischt' | 'Anderes' | 'Kein Fleisch' | 'Vegan' | 'Unbekannt';
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
const manufacturers = ref<any[]>([]);
const mainCategories = ref<any[]>([]);
const subCategories = ref<any[]>([]);

// Photo Slots
type PhotoSlot = { id?: number, src: string, file?: File, type: string, recorded_at?: string };
const packagingPhoto = ref<PhotoSlot | null>(null);
const contentPhoto = ref<PhotoSlot | null>(null);
const nutritionPhoto = ref<PhotoSlot | null>(null);
const ingredientsPhoto = ref<PhotoSlot | null>(null);
const barcodePhoto = ref<PhotoSlot | null>(null);
const otherPhotos = ref<PhotoSlot[]>([]);
const deletedPhotoIds = ref<number[]>([]);

// Cropper State
const isCropModalOpen = ref(false);
const cropImageUrl = ref('');
const cropTargetType = ref('');
const cropperRef = ref<any>(null);
let rawFileBuffer: File | null = null;

const openCropper = (e: Event, type: string) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        rawFileBuffer = target.files[0] || null;
        cropTargetType.value = type;
        if (rawFileBuffer) {
            cropImageUrl.value = URL.createObjectURL(rawFileBuffer as Blob);
        }
        isCropModalOpen.value = true;
        target.value = ''; 
    }
};

const cancelCrop = () => {
    isCropModalOpen.value = false;
    cropImageUrl.value = '';
    rawFileBuffer = null;
    stopRotation();
};

let rotateInterval: any = null;

const startRotation = (degrees: number) => {
    // Initial step
    if (cropperRef.value) {
        cropperRef.value.rotate(degrees);
    }
    // Continuous rotation when held
    if (!rotateInterval) {
        rotateInterval = setInterval(() => {
            if (cropperRef.value) {
                cropperRef.value.rotate(degrees);
            }
        }, 50);
    }
};

const stopRotation = () => {
    if (rotateInterval) {
        clearInterval(rotateInterval);
        rotateInterval = null;
    }
};

const rotate90 = () => {
    if (cropperRef.value) {
        cropperRef.value.rotate(90);
    }
};

const confirmCrop = () => {
    if (cropperRef.value) {
        const { canvas } = cropperRef.value.getResult();
        if (canvas) {
            canvas.toBlob(async (blob: Blob | null) => {
                if (!blob) return;
                const file = new File([blob], 'cropped.jpg', { type: 'image/jpeg' });
                
                try {
                    const compressedFile = await imageCompression(file, { maxSizeMB: 1.5, maxWidthOrHeight: 1920 });
                    const src = URL.createObjectURL(compressedFile);
                    const slotData: PhotoSlot = { 
                        file: compressedFile, 
                        src, 
                        type: cropTargetType.value,
                        recorded_at: new Date().toISOString().split('T')[0]
                    };
                    
                    if (cropTargetType.value === 'packaging') setSlot(packagingPhoto, slotData);
                    else if (cropTargetType.value === 'content') setSlot(contentPhoto, slotData);
                    else if (cropTargetType.value === 'nutrition') setSlot(nutritionPhoto, slotData);
                    else if (cropTargetType.value === 'ingredients') setSlot(ingredientsPhoto, slotData);
                    else if (cropTargetType.value === 'barcode') setSlot(barcodePhoto, slotData);
                    else if (cropTargetType.value === 'other') otherPhotos.value.push(slotData);
                } catch (e) {
                    console.error('Compression failed', e);
                } finally {
                    cancelCrop();
                }
            }, 'image/jpeg');
        }
    }
};

const setSlot = (refVar: any, newData: PhotoSlot) => {
    if (refVar.value && refVar.value.id) {
        deletedPhotoIds.value.push(refVar.value.id);
    }
    refVar.value = newData;
};

const removePhoto = (type: string) => {
    if (type === 'packaging') {
        if (packagingPhoto.value?.id) deletedPhotoIds.value.push(packagingPhoto.value.id);
        packagingPhoto.value = null;
    } else if (type === 'content') {
        if (contentPhoto.value?.id) deletedPhotoIds.value.push(contentPhoto.value.id);
        contentPhoto.value = null;
    } else if (type === 'nutrition') {
        if (nutritionPhoto.value?.id) deletedPhotoIds.value.push(nutritionPhoto.value.id);
        nutritionPhoto.value = null;
    } else if (type === 'ingredients') {
        if (ingredientsPhoto.value?.id) deletedPhotoIds.value.push(ingredientsPhoto.value.id);
        ingredientsPhoto.value = null;
    } else if (type === 'barcode') {
        if (barcodePhoto.value?.id) deletedPhotoIds.value.push(barcodePhoto.value.id);
        barcodePhoto.value = null;
    }
};

const removeOtherPhoto = (index: number) => {
    const photo = otherPhotos.value[index];
    if (photo && photo.id) {
        deletedPhotoIds.value.push(photo.id);
    }
    otherPhotos.value.splice(index, 1);
};


watch(() => props.initialData, (newData) => {
    if (newData) {
        Object.keys(form.value).forEach(key => {
            if (newData[key] !== undefined) {
                (form.value as any)[key] = newData[key];
            }
        });

        if (newData.brand) {
            form.value.brand_name = newData.brand.name;
            form.value.manufacturer_name = newData.brand.manufacturer?.name || '';
        }

        if (newData.main_category) form.value.main_category_name = newData.main_category.name;
        if (newData.sub_category) form.value.sub_category_name = newData.sub_category.name;

        if (newData.photos && Array.isArray(newData.photos)) {
            packagingPhoto.value = null;
            contentPhoto.value = null;
            nutritionPhoto.value = null;
            ingredientsPhoto.value = null;
            barcodePhoto.value = null;
            otherPhotos.value = [];
            
            newData.photos.forEach((photo: any) => {
                const slotData = { id: photo.id, src: `http://localhost:8000/storage/${photo.file_path}`, type: photo.type, recorded_at: photo.recorded_at };
                
                if (photo.type === 'packaging') {
                    if (!packagingPhoto.value) packagingPhoto.value = slotData;
                    else otherPhotos.value.push(slotData);
                }
                else if (photo.type === 'content') {
                    if (!contentPhoto.value) contentPhoto.value = slotData;
                    else otherPhotos.value.push(slotData);
                }
                else if (photo.type === 'nutrition') {
                    if (!nutritionPhoto.value) nutritionPhoto.value = slotData;
                    else otherPhotos.value.push(slotData);
                }
                else if (photo.type === 'ingredients') {
                    if (!ingredientsPhoto.value) ingredientsPhoto.value = slotData;
                    else otherPhotos.value.push(slotData);
                }
                else if (photo.type === 'barcode') {
                    if (!barcodePhoto.value) barcodePhoto.value = slotData;
                    else otherPhotos.value.push(slotData);
                }
                else {
                    otherPhotos.value.push(slotData);
                }
            });
        }
    }
}, { immediate: true });

const fetchBrands = async () => {
    try {
        const response = await fetch('http://localhost:8000/api/brands', { credentials: 'include' });
        brands.value = await response.json();
    } catch (error) { console.error(error); }
};

const fetchManufacturers = async () => {
    try {
        const response = await fetch('http://localhost:8000/api/manufacturers', { credentials: 'include' });
        manufacturers.value = await response.json();
    } catch (error) { console.error(error); }
};

const fetchCategories = async () => {
    try {
        const [mainRes, subRes] = await Promise.all([
            fetch('http://localhost:8000/api/main-categories', { credentials: 'include' }),
            fetch('http://localhost:8000/api/sub-categories', { credentials: 'include' })
        ]);
        mainCategories.value = await mainRes.json();
        subCategories.value = await subRes.json();
    } catch (error) { console.error(error); }
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
        if (mainCat) cats = cats.filter(cat => cat.main_category_id === mainCat.id);
    }
    if (form.value.sub_category_name) {
        cats = cats.filter(cat => cat.name.toLowerCase().includes(form.value.sub_category_name.toLowerCase()));
    }
    return cats;
});

const selectMainCategory = (cat: any) => form.value.main_category_name = cat.name;
const selectSubCategory = (cat: any) => form.value.sub_category_name = cat.name;

const filteredBrands = computed(() => {
    if (!form.value.brand_name) return brands.value;
    return brands.value.filter(brand => brand.name.toLowerCase().includes(form.value.brand_name.toLowerCase()));
});

const exactBrandMatch = computed(() => {
    if (!form.value.brand_name) return null;
    return brands.value.find(b => b.name.toLowerCase() === form.value.brand_name.toLowerCase()) || null;
});

const filteredManufacturers = computed(() => {
    if (!form.value.manufacturer_name) return manufacturers.value;
    return manufacturers.value.filter(m => m.name.toLowerCase().includes(form.value.manufacturer_name.toLowerCase()));
});

const exactManufacturerMatch = computed(() => {
    if (!form.value.manufacturer_name) return null;
    return manufacturers.value.find(m => m.name.toLowerCase() === form.value.manufacturer_name.toLowerCase()) || null;
});

const calculatedCalories = computed(() => {
    const fat = Number(form.value.fat_p100) || 0;
    const carbs = Number(form.value.carbs_p100) || 0;
    const protein = Number(form.value.protein_p100) || 0;
    return (fat * 9) + (carbs * 4) + (protein * 4);
});

const calorieDiscrepancy = computed(() => {
    const declared = Number(form.value.calories_p100);
    if (!declared || calculatedCalories.value === 0) return false;
    
    const diff = Math.abs(declared - calculatedCalories.value);
    const percentDiff = diff / declared;
    
    // Warn if difference is > 2% AND > 10 kcal
    return percentDiff > 0.02 && diff > 10;
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

const selectManufacturer = (m: any) => {
    form.value.manufacturer_name = m.name;
};

onMounted(() => {
    fetchBrands();
    fetchManufacturers();
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

    const allPhotos = [
        packagingPhoto.value,
        contentPhoto.value,
        nutritionPhoto.value,
        ingredientsPhoto.value,
        barcodePhoto.value,
        ...otherPhotos.value
    ].filter(Boolean) as PhotoSlot[];

    allPhotos.forEach(p => {
        if (p.file) {
            formData.append('photos[]', p.file);
            formData.append('photo_types[]', p.type);
            formData.append('photo_recorded_at[]', p.recorded_at || '');
        } else if (p.id) {
            formData.append(`existing_photo_types[${p.id}]`, p.type);
            formData.append(`existing_photo_recorded_at[${p.id}]`, p.recorded_at || '');
        }
    });

    deletedPhotoIds.value.forEach(id => {
        formData.append('deleted_photo_ids[]', String(id));
    });

    emit('submit', formData);
};
</script>