<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from "@/Components/DialogModal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
const props = defineProps(['groupedReservations',]);
let confirmEntryDelete = ref(false);
let reservationDelete = ref(null);
function formatDate(dateString) {
    // Sépare la chaîne de date en composantes
    const [year, month, day] = dateString.split('-');

    // Recombine les composantes dans le format souhaité
    return `${day}/${month}/${year}`;
}
function confirmingEntryDeletion(reservation) {
    reservationDelete.value = formatDate(reservation.date) + ' ' + reservation.heure.slice(0, 5);
    deleteForm.id = reservation.id;
    confirmEntryDelete.value = true;
};

const closeModal = () => {
    confirmEntryDelete.value = false;
    deleteForm.reset();
};

const deleteForm = useForm({
    id: '',
});

const deleteEntry = () => {
    confirmEntryDelete.value = false;
    deleteForm.delete(route('userReservation.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};
</script>
<style>
.full-height {
    height: calc(100vh - 12.6rem);
    /* ajustez 4rem en fonction de votre barre de navigation si besoin */
}
</style>
<template>
    <AppLayout title="Mes Réservations" v-if="$page.props.auth.user.role_id === 1">
        <template #header>
            <h2 class="text-3xl text-gray-800 leading-tight">
                Vos Réservations
            </h2>
        </template>
        <div v-if="groupedReservations.length === 0" class="full-height flex flex-col items-center justify-center">
            <h2 class="text-center text-2xl mb-4">Vous n'avez aucune réserversation</h2><a
                class="bg-green-500 hover:bg-green-800 w-fit text-white font-bold text-lg py-2 px-4 mt-4 block mx-auto rounded-lg transition"
                :href="$page.props.auth.user ? route('reservation.select') : route('reservation.login')">Prendre
                Rendez-vous</a>
        </div>
        <div class="w-full flex flex-col items-center p-5 space-y-6">
            <div class="space-y-4" v-for="(reservations, date) in groupedReservations" :key="date">
                <div v-for="reservation in reservations"
                    class="bg-white/70 shadow-lg rounded-xl w-fit p-4 flex space-x-8 items-center">
                    <div>
                        {{ formatDate(reservation.date) }}
                    </div>
                    <div>
                        {{ reservation.heure.slice(0, 5) }}
                    </div>
                    <div>
                        {{ reservation.race }}
                    </div>
                    <button v-if="reservation.isCancellable" @click="confirmingEntryDeletion(reservation)"
                        class="bg-red-500 rounded-lg p-2 hover:bg-red-700 text-white font-medium">
                        Annuler
                    </button>
                    <div v-else class="font-bold">Délai d'annulation dépassé</div>
                </div>
            </div>
        </div>
    </AppLayout>
    <FrontLayout title="Mes Réservations" :page="$page" v-else>
        <template #header>
            <h2 class="text-3xl text-gray-800 leading-tight">
                Vos Réservations
            </h2>
        </template>
        <div v-if="groupedReservations.length === 0" class="full-height flex flex-col items-center justify-center">
            <h2 class="text-center text-2xl mb-4">Vous n'avez aucune réserversation</h2><a
                class="bg-green-500 hover:bg-green-800 w-fit text-white font-bold text-lg py-2 px-4 mt-4 block mx-auto rounded-lg transition"
                :href="$page.props.auth.user ? route('reservation.select') : route('reservation.login')">Prendre
                Rendez-vous</a>
        </div>
        <div class="min-h-screen w-full flex flex-col items-center p-5 space-y-6">
            <div v-for="(reservations, date) in groupedReservations" :key="date" class="space-y-4">
                <div v-for="reservation in reservations"
                    class="bg-white/70 shadow-lg rounded-xl w-fit p-4 flex space-x-4 items-center">
                    <div>
                        {{ formatDate(reservation.date) }}
                    </div>
                    <div>
                        {{ reservation.heure.slice(0, 5) }}
                    </div>
                    <div>
                        {{ reservation.race }}
                    </div>
                    <button v-if="reservation.isCancellable" @click="confirmingEntryDeletion(reservation)"
                        class="bg-red-500 rounded-lg p-2 hover:bg-red-700 text-white font-medium">
                        Annuler
                    </button>
                    <div v-else class="font-bold">Délai d'annulation dépassé</div>
                </div>
            </div>
        </div>
    </FrontLayout>
    <DialogModal :show="confirmEntryDelete" @close="closeModal">
        <template #title>
            Annuler reservation
        </template>

        <template #content>
            Êtes-vous sûr de vouloir annuler votre reservation du: {{ reservationDelete }} ?
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Non
            </SecondaryButton>

            <DangerButton class="ms-3" :class="{ 'opacity-25': deleteForm.processing }"
                :disabled="deleteForm.processing" @click="deleteEntry">
                Oui
            </DangerButton>
        </template>
    </DialogModal>
</template>
