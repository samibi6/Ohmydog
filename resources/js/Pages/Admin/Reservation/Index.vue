<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['reservations', 'tailles', 'poils', 'etats', 'services', 'durees', 'users']);

let confirmEntryDelete = ref(false);
let reservationDelete = ref(null);
let open = ref(false);

const form = usePrecognitionForm("post", route("adminReservation.store"), {
    user_id: '',
    date: '',
    heure: '',
    race: '',
    poil_id: '',
    duree_id: '',
    taille_id: '',
    service_id: '',
    etat_id: '',
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

function envoiForm() {
    form.duree_id = getDureeIdByIds(form.taille_id, form.service_id, form.etat_id);
    submit;
};

function getDureeIdByIds(taille_id, service_id, etat_id) {
    const dureeObj = props.durees.find(duree =>
        duree.taille_id === taille_id &&
        duree.service_id === service_id &&
        duree.etat_id === etat_id
    );
    return dureeObj ? dureeObj.id : null;
};

function confirmingEntryDeletion(reservation) {
    reservationDelete.value = reservation.race;
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
    deleteForm.delete(route('adminReservation.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};
const getDureeById = (id) => {
    const dureeObj = props.durees.find(duree => duree.id === id);
    return dureeObj;
};
const getServiceById = (id) => {
    const serviceObj = props.services.find(service => service.id === id);
    return serviceObj;
};
const getEtatById = (id) => {
    const etatObj = props.etats.find(etat => etat.id === id);
    return etatObj;
};
const getPoilById = (id) => {
    const poilObj = props.poils.find(poil => poil.id === id);
    return poilObj;
};
const getTailleById = (id) => {
    const tailleObj = props.tailles.find(taille => taille.id === id);
    return tailleObj;
};

const getUserById = (id) => {
    const userObj = props.users.find(user => user.id === id);
    return userObj;
};
function formatDate(dateString) {
    // Sépare la chaîne de date en composantes
    const [year, month, day] = dateString.split('-');

    // Recombine les composantes dans le format souhaité
    return `${day}-${month}-${year}`;
}
</script>

<template>
    <AppLayout title="reservation">
        <template #header>

        </template>
        <div class="w-fit mx-auto">
            <button @click="open = !open"
                class="bg-blue-500 p-3 rounded-lg hover:bg-blue-800 font-medium text-white m-5">Afficher
                formulaire</button>
        </div>
        <div v-if="open">
            <form @submit.prevent="submit" class="max-w-lg mx-auto">
                <h2 class="text-2xl text-center mb-4">Créer une reservation</h2>

                <div class="mb-5">
                    <label for="user" class="text-gray-700 mb-2 block font-medium">Utilisateur</label>
                    <select id="user" v-model="form.user_id" @change="form.validate('user_id')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                    <div v-if="form.invalid('user_id')" class="text-sm text-red-600">
                        {{ form.errors.user_id }}
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
                    <label for="poil" class="text-gray-700 mb-2 block font-medium">poil</label>
                    <select id="poil" v-model="form.poil_id" @change="form.validate('poil_id')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                        <option v-for="poil in poils" :key="poil.id" :value="poil.id">
                            {{ poil.nom }}
                        </option>
                    </select>
                    <div v-if="form.invalid('poil_id')" class="text-sm text-red-600">
                        {{ form.errors.poil_id }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="etat" class="text-gray-700 mb-2 block font-medium">etat</label>
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
                    <label for="race" class="text-gray-700 mb-2 block font-medium">race</label>
                    <input type="text" id="race" v-model="form.race" @change="form.validate('race')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('race')" class="text-sm text-red-600">
                        {{ form.errors.race }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="date" class="text-gray-700 mb-2 block font-medium">date</label>
                    <input type="date" id="date" v-model="form.date" @change="form.validate('date')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('date')" class="text-sm text-red-600">
                        {{ form.errors.date }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="heure" class="text-gray-700 mb-2 block font-medium">heure</label>
                    <input type="time" id="heure" v-model="form.heure" @change="form.validate('heure')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('heure')" class="text-sm text-red-600">
                        {{ form.errors.heure }}
                    </div>
                </div>

                <div class="flex justify-center">
                    <button @click="envoiForm()" :disabled="form.processing"
                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                        Ajouter la reservation
                    </button>
                </div>
            </form>
        </div>
        <div class="py-4 pb-10">
            <div v-for="reservation in reservations"
                class="border-2 w-fit mx-auto flex bg-white p-2 space-x-6 mb-4 items-center rounded-xl">
                <div class="flex flex-col">
                    <h2 class="text-lg text-center mb-1">{{ formatDate(reservation.date) }}
                    </h2>
                    <h2 class="text-lg text-center mb-1">{{ reservation.heure.slice(0,
                        5) }}</h2>
                </div>
                <p class="text-center text-wrap">{{
                    'Nom: '
                    + getUserById(reservation.user_id).name
                    + ' | Taille: '
                    + getTailleById(getDureeById(reservation.duree_id).taille_id).nom
                    + ' | Service: '
                    + getServiceById(getDureeById(reservation.duree_id).service_id).nom
                    + ' | Poil: '
                    + getPoilById(reservation.type_poil_id).nom
                    + ' | Etat: '
                    + getEtatById(getDureeById(reservation.duree_id).etat_id).nom
                    + ' | Race: '
                    + reservation.race
                    + ' | Durée: '
                    + getDureeById(reservation.duree_id).duree.slice(0, 5)
                    }}</p>
                <div class="flex flex-wrap lg:flex-nowrap lg:space-y-0 space-y-4 w-min lg:space-x-4">
                    <a class="bg-blue-500 p-2 rounded-lg text-white font-medium hover:bg-blue-800"
                        :href="route('adminReservation.edit', { reservation })">Modifier</a>
                    <a class="bg-red-500 p-2 rounded-lg text-white font-medium hover:bg-red-800"
                        @click="confirmingEntryDeletion(reservation)">Supprimer</a>
                </div>
            </div>
        </div>


        <DialogModal :show="confirmEntryDelete" @close="closeModal">
            <template #title>
                Supprimer reservation
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer la reservation {{ reservationDelete }} ?
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Annuler
                </SecondaryButton>

                <DangerButton class="ms-3" :class="{ 'opacity-25': deleteForm.processing }"
                    :disabled="deleteForm.processing" @click="deleteEntry">
                    Supprimer
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
