<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import heic2any from 'heic2any';

const props = defineProps(['gallerie']);

const form = usePrecognitionForm("post", route("gallerie.update", { gallerie: props.gallerie.id }), {
    description: props.gallerie.description,
    imgBefore: null,
    imgAfter: null,
    _method: 'patch'
});

form.setValidationTimeout(300);

const handleFileChange = async (event, field) => {
    const file = event.target.files[0];
    const fileName = file.name.toLowerCase();
    if (file && (fileName.endsWith('.heic') || fileName.endsWith('.heif'))) {
        try {
            const convertedBlob = await heic2any({
                blob: file,
                toType: 'image/jpeg',
                quality: 0.3,
            });
            form[field] = new File([convertedBlob], file.name.replace(/\.[^/.]+$/, '.jpg'), {
                type: 'image/jpeg',
            });
            form.validate(field);
        } catch (error) {
            console.error('Error converting HEIC to JPEG:', error);
        }
    } else {
        form[field] = file;
        form.validate(field);
    }
};

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
    headers: {
        'Content-Type': 'multipart/form-data',
    },
});

</script>

<template>
    <form @submit.prevent="submit" class="max-w-lg mx-auto">
        <h2 class="text-2xl text-center mb-4">Modifier une image</h2>

        <div class="mb-5">
            <label for="description" class="text-gray-700 mb-2 block font-medium">description de l'image</label>
            <textarea id="description" rows="5" v-model="form.description" @change="form.validate('description')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
            </textarea>
            <div v-if="form.invalid('description')" class="text-sm text-red-600">
                {{ form.errors.description }}
            </div>
        </div>

        <div class="mb-5">
            <img v-if="gallerie.imgBefore" class="w-80 aspect-square object-cover"
                :src="'/storage/' + gallerie.imgBefore" alt="">
            <label for="imgBefore" class="text-gray-700 mb-2 block font-medium">Photo Avant (optionnel)</label>
            <input type="file" id="imgBefore" @change="handleFileChange($event, 'imgBefore')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full" />
            <div v-if="form.invalid('imgBefore')" class="text-sm text-red-600">
                {{ form.errors.imgBefore }}
            </div>
        </div>

        <div class="mb-5">
            <img class="w-80 aspect-square object-cover" :src="'/storage/' + gallerie.imgAfter" alt="">
            <label for="imgAfter" class="text-gray-700 mb-2 block font-medium">Photo Après</label>
            <input type="file" id="imgAfter" @change="handleFileChange($event, 'imgAfter')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full" />
            <div v-if="form.invalid('imgAfter')" class="text-sm text-red-600">
                {{ form.errors.imgAfter }}
            </div>
        </div>



        <div class="flex justify-center">
            <button :disabled="form.processing"
                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                Modifier l'image
            </button>
            <a class="bg-red-500 text-white font-medium hover:bg-red-800 p-2 rounded-lg ml-4 h-fit"
                :href="route('gallerie.index')">Annuler</a>
        </div>
    </form>
</template>
