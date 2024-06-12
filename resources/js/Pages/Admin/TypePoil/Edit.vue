<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import heic2any from 'heic2any';

const props = defineProps(['typePoil']);

const form = usePrecognitionForm("post", route("typePoil.update", { typePoil: props.typePoil.id }), {
    nom: props.typePoil.nom,
    illustration: null,
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
        <h2 class="text-2xl text-center mb-4">Modifier un type de poil</h2>

        <div class="mb-5">
            <label for="nom" class="text-gray-700 mb-2 block font-medium">Nom du type de poil</label>
            <input type="text" id="nom" v-model="form.nom" @change="form.validate('nom')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
            <div v-if="form.invalid('nom')" class="text-sm text-red-600">
                {{ form.errors.nom }}
            </div>
        </div>

        <div class="mb-5">
            <img class="aspect-square object-cover h-80" :src="'/storage/' + typePoil.illustration" alt="">
            <label for="illustration" class="text-gray-700 mb-2 block font-medium">image d'illustration</label>
            <input type="file" id="illustration" @change="handleFileChange($event, 'illustration')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full" />
            <div v-if="form.invalid('illustration')" class="text-sm text-red-600">
                {{ form.errors.illustration }}
            </div>
        </div>



        <div class="flex justify-center">
            <button :disabled="form.processing"
                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                Modifier le type de poil
            </button>
            <a class="bg-red-500 text-white font-medium hover:bg-red-800 p-2 rounded-lg ml-4 h-fit"
                :href="route('typePoil.index')">Annuler</a>
        </div>
    </form>
</template>
