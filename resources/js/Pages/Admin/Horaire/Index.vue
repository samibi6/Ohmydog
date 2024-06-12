<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['horaires']);
props.horaires.forEach(horaire => {
    // Extraire l'heure de début et de fin et les formater en hh:mm
    horaire.heure_debut = horaire.heure_debut.slice(0, 5); // Format HH:MM
    horaire.heure_fin = horaire.heure_fin.slice(0, 5); // Format HH:MM
});

</script>

<template>
    <AppLayout title="Horaire">
        <template #header>

        </template>

        <div v-if="horaires.length === 0">
            <a class="bg-blue-500 p-5" :href="route('horaire.generate')">Générer horaires</a>
        </div>

        <div class="py-4 pb-10">
            <div v-for="horaire in horaires" class="border-2 w-fit mx-auto flex space-x-4 mb-4">
                <h2 class="text-2xl text-center mb-4">{{ horaire.jour }}</h2>
                <div>
                    <p class="text-center" :class="horaire.ouvert === 0 ? 'text-red-500' : 'text-green-500'">{{
                        horaire.ouvert
                            === 0 ? 'Fermé' : 'Ouvert' }}</p>
                    <p v-if="horaire.ouvert">{{ horaire.heure_debut + ' - ' + horaire.heure_fin }}</p>
                </div>
                <a class="bg-blue-500 p-2 rounded-lg text-white font-medium hover:bg-blue-800"
                    :href="route('horaire.edit', { horaire })">Modifier</a>
            </div>
        </div>
    </AppLayout>
</template>
