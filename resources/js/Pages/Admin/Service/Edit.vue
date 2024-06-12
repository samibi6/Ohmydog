<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['service']);

const form = usePrecognitionForm("patch", route("service.update", { service: props.service.id }), {
    nom: props.service.nom,
    description: props.service.description,
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>

<template>
    <form @submit.prevent="submit" class="max-w-lg mx-auto">
        <h2 class="text-2xl text-center mb-4">Modifier un service</h2>

        <div class="mb-5">
            <label for="nom" class="text-gray-700 mb-2 block font-medium">Nom du service</label>
            <input type="text" id="nom" v-model="form.nom" @change="form.validate('nom')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
            <div v-if="form.invalid('nom')" class="text-sm text-red-600">
                {{ form.errors.nom }}
            </div>
        </div>

        <div class="mb-5">
            <label for="description" class="text-gray-700 mb-2 block font-medium">description du service</label>
            <input type="text" id="description" v-model="form.description" @change="form.validate('description')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full" />
            <div v-if="form.invalid('description')" class="text-sm text-red-600">
                {{ form.errors.description }}
            </div>
        </div>



        <div class="flex justify-center">
            <button :disabled="form.processing"
                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                Modifier le service
            </button>
            <a class="bg-red-500 text-white font-medium hover:bg-red-800 p-2 rounded-lg ml-4 h-fit"
                :href="route('service.index')">Annuler</a>
        </div>
    </form>
</template>
