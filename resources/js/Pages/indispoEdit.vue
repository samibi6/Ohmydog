<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['reservation']);

const form = usePrecognitionForm("patch", route("indispo.update", { reservation: props.reservation.id }), {
    date: props.reservation.date,
    heure: props.reservation.heure,
    heure_fin: props.reservation.heure_fin
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>

<template>
    <form @submit.prevent="submit" class="max-w-lg mx-auto">
        <h2 class="text-2xl text-center mb-4">Modifier une Indisponibilité</h2>

        <div class="mb-5">
            <label for="date" class="text-gray-700 mb-2 block font-medium">Date</label>
            <input type="date" id="date" v-model="form.date" @change="form.validate('date')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"
                required />
            <div v-if="form.invalid('date')" class="text-sm text-red-600">
                {{ form.errors.date }}
            </div>
        </div>

        <div class="mb-5">
            <label for="heure" class="text-gray-700 mb-2 block font-medium">Heure debut:</label>
            <input type="time" id="heure" v-model="form.heure" @change="form.validate('heure')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"
                required />
            <div v-if="form.invalid('heure')" class="text-sm text-red-600">
                {{ form.errors.heure }}
            </div>
        </div>

        <div class="mb-5">
            <label for="heure_fin" class="text-gray-700 mb-2 block font-medium">Heure fin:</label>
            <input type="time" id="heure_fin" v-model="form.heure_fin" @change="form.validate('heure_fin')"
                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"
                required />
            <div v-if="form.invalid('heure_fin')" class="text-sm text-red-600">
                {{ form.errors.heure_fin }}
            </div>
        </div>



        <div class="flex justify-center">
            <button :disabled="form.processing"
                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                Modifier l'indisponibilité'
            </button>
            <a :href="route('dashboard')">Annuler</a>
        </div>
    </form>

</template>
