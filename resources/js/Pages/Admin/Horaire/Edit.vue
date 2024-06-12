<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['horaire']);

const form = usePrecognitionForm("patch", route("horaire.update", { horaire: props.horaire.id }), {
    jour: props.horaire.jour,
    ouvert: props.horaire.ouvert,
    heure_debut: props.horaire.heure_debut.slice(0, 5),
    heure_fin: props.horaire.heure_fin.slice(0, 5)
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>

<template>
    <form @submit.prevent="submit" class="max-w-lg mx-auto">
        <h2 class="text-2xl text-center mb-4">Modifier un horaire</h2>

        <div class="mb-5">
            <label for="jour" class="text-gray-700 mb-2 block font-medium">jour de la semaine</label>
            <input type="text" id="jour" v-model="form.jour" @change="form.validate('jour')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
            <div v-if="form.invalid('jour')" class="text-sm text-red-600">
                {{ form.errors.jour }}
            </div>
        </div>

        <div class="mb-5">
            <label for="ouvert" class="text-gray-700 mb-2 block font-medium">est ouvert ?</label>
            <input type="checkbox" id="ouvert" v-model="form.ouvert" @change="form.validate('ouvert')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" />
            <div v-if="form.invalid('ouvert')" class="text-sm text-red-600">
                {{ form.errors.ouvert }}
            </div>
        </div>

        <div class="mb-5" v-if="form.ouvert">
            <label for="heure_debut" class="text-gray-700 mb-2 block font-medium">heure d'ouverture</label>
            <input type="time" id="heure_debut" v-model="form.heure_debut" @change="form.validate('heure_debut')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" />
            <div v-if="form.invalid('heure_debut')" class="text-sm text-red-600">
                {{ form.errors.heure_debut }}
            </div>
        </div>

        <div class="mb-5" v-if="form.ouvert">
            <label for="heure_fin" class="text-gray-700 mb-2 block font-medium">heure de fermeture</label>
            <input type="time" id="heure_fin" v-model="form.heure_fin" @change="form.validate('heure_fin')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" />
            <div v-if="form.invalid('heure_fin')" class="text-sm text-red-600">
                {{ form.errors.heure_fin }}
            </div>
        </div>



        <div class="flex justify-center">
            <button :disabled="form.processing"
                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                Modifier l'horaire
            </button>
            <a class="bg-red-500 text-white font-medium hover:bg-red-800 p-2 rounded-lg ml-4 h-fit"
                :href="route('horaire.index')">Annuler</a>
        </div>
    </form>
</template>
