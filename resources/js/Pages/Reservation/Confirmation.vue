<script setup>
import { computed } from 'vue';
import FrontLayout from "@/Layouts/FrontLayout.vue";
const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    user: null,
    date: { type: String },
    heure: { type: String }
});
function formatDate(dateString) {
    // Sépare la chaîne de date en composantes
    const [year, month, day] = dateString.split('-');

    // Recombine les composantes dans le format souhaité
    return `${day}/${month}/${year}`;
}
const heureRdv = computed(() => props.heure.slice(0, 5));
const dateRdv = computed(() => formatDate(props.date));
</script>
<style>
#logo {
    background-image: url('/storage/logo.svg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}
</style>
<template>
    <FrontLayout title="Confimation RDV" :page="$page" :canLogin="canLogin">
        <div class="w-screen h-screen flex items-center  justify-center">
            <div class="bg-white/50 rounded-lg p-6 text-center">
                <h2 class="text-2xl">Votre réservation pour le {{ dateRdv + ' à ' + heureRdv }} à bien été
                    enregistrée</h2>
            </div>
        </div>
    </FrontLayout>
</template>
