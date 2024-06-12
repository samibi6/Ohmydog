<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['prix', 'services', 'tailles']);

const form = usePrecognitionForm("patch", route("prix.update", { prix: props.prix.id }), {
    service_id: props.prix.service_id,
    taille_id: props.prix.taille_id,
    prix: props.prix.prix
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>

<template>
    <form @submit.prevent="submit" class="max-w-lg mx-auto">
        <h2 class="text-2xl text-center mb-4">Modifier un prix</h2>

        <div class="mb-5">
            <label for="service" class="text-gray-700 mb-2 block font-medium">Service</label>
            <select id="service" v-model="form.service_id" @change="form.validate('service_id')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                <option v-for="service in services" :key="service.id" :value="service.id">
                    {{ service.nom }}
                </option>
            </select>
            <div v-if="form.invalid('service_id')" class="text-sm text-red-600">
                {{ form.errors.service_id }}
            </div>
        </div>

        <div class="mb-5">
            <label for="taille" class="text-gray-700 mb-2 block font-medium">Taille</label>
            <select id="taille" v-model="form.taille_id" @change="form.validate('taille_id')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                <option v-for="taille in tailles" :key="taille.id" :value="taille.id">
                    {{ taille.nom }}
                </option>
            </select>
            <div v-if="form.invalid('taille_id')" class="text-sm text-red-600">
                {{ form.errors.taille_id }}
            </div>
        </div>

        <div class="mb-5">
            <label for="prix" class="text-gray-700 mb-2 block font-medium">Prix</label>
            <input type="number" id="prix" v-model="form.prix" @change="form.validate('prix')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
            <div v-if="form.invalid('prix')" class="text-sm text-red-600">
                {{ form.errors.prix }}
            </div>
        </div>



        <div class="flex justify-center">
            <button :disabled="form.processing"
                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                Modifier le prix
            </button>
            <a class="bg-red-500 text-white font-medium hover:bg-red-800 p-2 rounded-lg ml-4 h-fit"
                :href="route('prix.index')">Annuler</a>
        </div>
    </form>

</template>
