<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['duree', 'services', 'tailles', 'etats']);

const form = usePrecognitionForm("post", route("duree.update", { duree: props.duree.id }), {
    service_id: props.duree.service_id,
    taille_id: props.duree.taille_id,
    etat_id: props.duree.etat_id,
    duree: props.duree.duree.slice(0, 5),
    _method: 'patch'
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>

<template>
    <form @submit.prevent="submit" class="max-w-lg mx-auto">
        <h2 class="text-2xl text-center mb-4">Modifier une durée</h2>

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
            <label for="etat" class="text-gray-700 mb-2 block font-medium">Etat</label>
            <select id="etat" v-model="form.etat_id" @change="form.validate('etat_id')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                <option v-for="etat in etats" :key="etat.id" :value="etat.id">
                    {{ etat.nom }}
                </option>
            </select>
            <div v-if="form.invalid('etat_id')" class="text-sm text-red-600">
                {{ form.errors.etat_id }}
            </div>
        </div>

        <div class="mb-5">
            <label for="duree" class="text-gray-700 mb-2 block font-medium">duree</label>
            <input type="time" id="duree" v-model="form.duree" @change="form.validate('duree')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
            <div v-if="form.invalid('duree')" class="text-sm text-red-600">
                {{ form.errors.duree }}
            </div>
        </div>



        <div class="flex justify-center">
            <button :disabled="form.processing"
                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                Modifier la durée
            </button>
            <a class="bg-red-500 text-white font-medium hover:bg-red-800 p-2 rounded-lg ml-4 h-fit"
                :href="route('duree.index')">Annuler</a>
        </div>
    </form>

</template>
