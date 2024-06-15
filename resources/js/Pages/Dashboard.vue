<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import axios from 'axios';

const props = defineProps(['indispos', 'days', 'month', 'year', 'durees', 'tailles', 'poils', 'etats', 'services', 'users']);

const currentDate = new Date();
const day = currentDate.getDate();
let confirmEntryDelete = ref(false);
let reservationDelete = ref(null);
let open = ref(false);
const RDV = ref([]);
const selectedDate = ref(null);
const processing = ref(false);
const errors = ref({});

const form = usePrecognitionForm("post", route("dashboard.store"), {
    date: '',
    heure: '',
    heure_fin: '',
    isPlage: '',
    isAllday: '',
    dateFin: '',
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

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

const monthForm = useForm({
    month: '',
    year: ''
});

const monthName = computed(() => {
    const date = new Date(props.year, props.month - 1);
    return date.toLocaleString('default', { month: 'long' });
});

const paddedDays = computed(() => {
    const firstDayOfMonth = (new Date(props.year, props.month - 1, 1).getDay() + 6) % 7;
    const lastDateOfMonth = new Date(props.year, props.month, 0).getDate();
    const daysArray = Array.from({ length: lastDateOfMonth }, (_, i) => {
        const date = i + 1;
        const currentDate = new Date(props.year, props.month - 1, date);
        const dayOfWeek = currentDate.getDay();
        const isClosed = props.days[date - 1]?.isClosed || false; // Vérifier si le jour est fermé
        const isHoliday = props.days[date - 1]?.isHoliday || false;
        const hasRdv = props.days[date - 1]?.hasRdv || false; // Vérifier si le jour est un jour férié

        return { date, dayOfWeek, isClosed, isHoliday, hasRdv };
    });

    for (let i = 0; i < firstDayOfMonth; i++) {
        daysArray.unshift({ placeholder: true });
    }

    return daysArray;
});

watch(selectedDate, async (newDate) => {
    if (newDate) {
        const response = await axios.get(route('reservation.getRDV'), { params: { date: `${props.year}-${String(props.month).padStart(2, '0')}-${String(newDate).padStart(2, '0')}` } });
        RDV.value = response.data;
    }
});

function selectDate(date) {
    if (date) {
        selectedDate.value = date;
    }
}

function prevMonth() {
    let newMonth = parseInt(props.month) - 1;
    let newYear = parseInt(props.year);

    if (newMonth < 1) {
        newMonth = 12;
        newYear -= 1;
    }
    monthForm.month = newMonth;
    monthForm.year = newYear;
    // Vérification si le mois change avant de naviguer
    if (newMonth !== parseInt(props.month) || newYear !== parseInt(props.year)) {
        processing.value = true;
        errors.value = {};
        monthForm.post(route('reservation.updateAgenda'), {
            onFinish: () => processing.value = false,
            onError: (err) => errors.value = err,
            onSuccess: () => form.reset(),
        });
    }
}

function nextMonth() {
    let newMonth = parseInt(props.month) + 1;
    let newYear = parseInt(props.year);

    if (newMonth > 12) {
        newMonth = 1;
        newYear += 1;
    }
    monthForm.month = newMonth;
    monthForm.year = newYear;
    // Vérification si le mois change avant de naviguer
    if (newMonth !== parseInt(props.month) || newYear !== parseInt(props.year)) {
        processing.value = true;
        errors.value = {};
        monthForm.post(route('reservation.updateAgenda'), {
            onFinish: () => processing.value = false,
            onError: (err) => errors.value = err,
            onSuccess: () => form.reset(),
        });
    }
}

function getDayClass(day, selectedDate) {
    if (!day.date) {
        return 'bg-none text-white font-bold cursor-not-allowed';
    }
    if (day.date === selectedDate) {
        return 'bg-blue-600 text-white font-bold';
    }
    if (day.isClosed) {
        return 'bg-red-500 cursor-not-allowed';
    }
    if (day.isHoliday) {
        return 'bg-gray-500 cursor-not-allowed';
    }
    if (day.hasRdv) {
        return 'bg-white underline decoration-2 font-bold text-blue-500 cursor-pointer';
    }
    return 'bg-white cursor-pointer';
}

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
selectDate(day);
function addDurations(time1, time2) {
    // Extraire les heures et les minutes de la première durée
    const [h1, m1] = time1.split(':').map(Number);
    const totalMinutes1 = h1 * 60 + m1;

    // Extraire les heures et les minutes de la deuxième durée
    const [h2, m2] = time2.split(':').map(Number);
    const totalMinutes2 = h2 * 60 + m2;

    // Additionner les durées en minutes
    const totalMinutes = totalMinutes1 + totalMinutes2;

    // Convertir le total des minutes en format HH:MM
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;

    // Formater les heures et les minutes pour obtenir HH:MM
    const formattedHours = String(hours).padStart(2, '0');
    const formattedMinutes = String(minutes).padStart(2, '0');

    return `${formattedHours}:${formattedMinutes}`;
}
</script>

<template>
    <AppLayout title="reservation">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
        <div class="w-fit mx-auto">
            <button @click="open = !open"
                class="bg-blue-500 p-3 rounded-lg hover:bg-blue-800 font-medium text-white m-5">Afficher
                formulaire</button>
        </div>
        <div v-if="open">
            <form @submit.prevent="submit" class="max-w-lg mx-auto">
                <h2 class="text-2xl text-center mb-4">Créer une Indisponibilité</h2>

                <div class="mb-5">
                    <label for="date" class="text-gray-700 mb-2 block font-medium">date</label>
                    <input type="date" id="date" v-model="form.date" @change="form.validate('date')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"
                        required />
                    <div v-if="form.invalid('date')" class="text-sm text-red-600">
                        {{ form.errors.date }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="plage" class="text-gray-700 mb-2 block font-medium">est plusieurs jours ?</label>
                    <input type="checkbox" id="plage" v-model="form.isPlage" @change="form.validate('isPlage')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" />
                    <div v-if="form.invalid('isPlage')" class="text-sm text-red-600">
                        {{ form.errors.isPlage }}
                    </div>
                </div>

                <div class="mb-5" v-if="form.isPlage">
                    <label for="dateFin" class="text-gray-700 mb-2 block font-medium">date de fin (comprise)</label>
                    <input type="date" id="dateFin" v-model="form.dateFin" @change="form.validate('dateFin')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('dateFin')" class="text-sm text-red-600">
                        {{ form.errors.dateFin }}
                    </div>
                </div>

                <div class="mb-5" v-if="!form.isPlage">
                    <label for="isAllday" class="text-gray-700 mb-2 block font-medium">est toute la journée ?</label>
                    <input type="checkbox" id="isAllday" v-model="form.isAllday" @change="form.validate('isAllday')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" />
                    <div v-if="form.invalid('isAllday')" class="text-sm text-red-600">
                        {{ form.errors.isAllday }}
                    </div>
                </div>

                <div class="mb-5" v-if="!form.isAllday && !form.isPlage">
                    <label for="heure" class="text-gray-700 mb-2 block font-medium">heure début:</label>
                    <input type="time" id="heure" v-model="form.heure" @change="form.validate('heure')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('heure')" class="text-sm text-red-600">
                        {{ form.errors.heure }}
                    </div>
                </div>

                <div class="mb-5" v-if="!form.isAllday && !form.isPlage">
                    <label for="heure_fin" class="text-gray-700 mb-2 block font-medium">heure fin:</label>
                    <input type="time" id="heure_fin" v-model="form.heure_fin" @change="form.validate('heure_fin')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('heure_fin')" class="text-sm text-red-600">
                        {{ form.errors.heure_fin }}
                    </div>
                </div>

                <div class="flex justify-center">
                    <button @click="submit" :disabled="form.processing"
                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                        Ajouter Indisponibilité
                    </button>
                </div>
            </form>
        </div>
        <div
            class="xl:px-32 py-8 flex flex-col lg:flex-row justify-center items-start space-y-4 lg:space-y-0 lg:space-x-4 bg-pink-200">
            <div class="w-full lg:w-1/2 bg-white/70 rounded-xl p-5">
                <h1 class="text-4xl drop-shadow-md mb-8">Agenda</h1>
                <div class="flex justify-between mb-4">
                    <button @click="prevMonth"
                        class="bg-blue-500 text-white p-2 lg:px-4 lg:py-2 rounded-lg hover:bg-blue-800 transition">Mois
                        Précédent</button>
                    <h2 class="text-3xl lg:mb-4 text-center lg:text-left">{{ monthName }} {{ year }}</h2>
                    <button @click="nextMonth"
                        class="bg-blue-500 text-white p-2 lg:px-4 lg:py-2 rounded-lg hover:bg-blue-800 transition">Mois
                        Suivant</button>
                </div>
                <div class="calendar grid grid-cols-7 gap-2">
                    <div class="font-bold text-center">Lun</div>
                    <div class="font-bold text-center">Mar</div>
                    <div class="font-bold text-center">Mer</div>
                    <div class="font-bold text-center">Jeu</div>
                    <div class="font-bold text-center">Ven</div>
                    <div class="font-bold text-center">Sam</div>
                    <div class="font-bold text-center">Dim</div>

                    <div v-for="(day, index) in paddedDays" :key="index || day.placeholder"
                        class="text-center py-4 lg:p-4 border border-black rounded"
                        :class="getDayClass(day, selectedDate)"
                        @click="!day.isClosed && !day.isHoliday ? selectDate(day.date) : null">
                        <span v-if="day.date">{{ day.date }}</span>
                    </div>
                </div>
            </div>
            <div v-if="selectedDate" class="mt-5 lg:w-1/2 p-5 bg-white/70 rounded-xl">
                <h3 class="text-2xl mb-4">Rendez-vous du {{ selectedDate }}</h3>
                <ul v-if="RDV && RDV.length > 0" class="">

                    <li v-for="time in RDV" :key="time.id" class="mr-4 mb-4 font-medium bg-white rounded-lg p-2">
                        <template v-if="typeof time === 'object'">
                            <span class="text-xl" style="font-family: 'Lobster', sans-serif;">{{
                                time.heure.slice(0, 5) + ' - ' + addDurations(time.heure.slice(0, 5),
                                    getDureeById(time.duree_id).duree.slice(0, 5)) }}</span>
                            {{ ' | Nom: '
                                + getUserById(time.user_id).name
                                + ' | Taille: '
                                + getTailleById(getDureeById(time.duree_id).taille_id).nom
                                + ' | Service: '
                                + getServiceById(getDureeById(time.duree_id).service_id).nom
                                + ' | Poil: '
                                + getPoilById(time.type_poil_id).nom
                                + ' | Etat: '
                                + getEtatById(getDureeById(time.duree_id).etat_id).nom
                                + ' | Race: '
                                + time.race
                                + ' | Durée: '
                                + getDureeById(time.duree_id).duree.slice(0, 5)
                            }}
                        </template>
                    </li>
                </ul>
            </div>
        </div>
        <div class="pb-32">
            <h2 class="text-center text-3xl">Indisponibilités:</h2>
            <div v-for="indispo in indispos"
                class="border-2 w-fit mx-auto flex justify-center items-center space-x-4 my-4 bg-white rounded-xl p-2">
                <h2 class="text-2xl text-center mb-1">{{ indispo.date }}</h2>
                <p class="text-center">{{ indispo.heure.slice(0, 5) + ' - ' + indispo.heure_fin.slice(0, 5) }}</p>
                <a class="bg-blue-500 p-2 rounded-lg text-white font-medium hover:bg-blue-800"
                    :href="route('adminReservation.edit', { reservation: indispo })">Modifier</a>
                <a class="bg-red-500 p-2 rounded-lg text-white font-medium hover:bg-red-800"
                    @click="confirmingEntryDeletion(indispo)">Supprimer</a>
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
