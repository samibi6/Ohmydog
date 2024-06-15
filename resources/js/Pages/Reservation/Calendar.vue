<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

const props = defineProps(['days', 'month', 'year', 'service_id', 'taille_id', 'poil_id', 'etat_id', 'race', 'duree', 'duree_id']);
const selectedDate = ref(null);
const availableTimes = ref([]);
const processing = ref(false);
const errors = ref({});

const form = useForm({
    poil_id: props.poil_id,
    race: props.race,
    duree_id: props.duree_id,
    date: '',
    heure: '',
});

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
        const isHoliday = props.days[date - 1]?.isHoliday || false; // Vérifier si le jour est un jour férié
        const isFull = props.days[date - 1]?.isFull || false;
        const isPast = props.days[date - 1]?.isPast || false;
        const isTooLate = props.days[date - 1]?.isTooLate || false; // Vérifier si le jour est un jour férié

        return { date, dayOfWeek, isClosed, isHoliday, isFull, isPast, isTooLate };
    });

    for (let i = 0; i < firstDayOfMonth; i++) {
        daysArray.unshift({ placeholder: true });
    }

    return daysArray;
});

watch(selectedDate, async (newDate) => {
    if (newDate) {
        const response = await axios.get(route('reservation.availableTimes'), { params: { date: `${props.year}-${String(props.month).padStart(2, '0')}-${String(newDate).padStart(2, '0')}`, duree: props.duree } });
        availableTimes.value = response.data;
    }
});

function selectDate(date) {
    if (date) {
        selectedDate.value = date;
        form.date = `${props.year}-${String(props.month).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
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
        monthForm.post(route('reservation.updateCalendar'), {
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
        monthForm.post(route('reservation.updateCalendar'), {
            onFinish: () => processing.value = false,
            onError: (err) => errors.value = err,
            onSuccess: () => form.reset(),
        });
    }
}

function selectTime(time) {
    form.heure = time;
    submit();
}

const submit = () => {
    processing.value = true;
    errors.value = {};

    form.post(route('reservation.store'), {
        onFinish: () => processing.value = false,
        onError: (err) => errors.value = err,
        onSuccess: () => form.reset(),
    });
};

function getDayClass(day) {
    if (day.date === selectedDate.value) {
        return 'bg-blue-600 text-white font-bold';
    }
    if (day.isClosed) {
        return 'bg-red-500 cursor-not-allowed';
    }
    if (day.isHoliday) {
        return 'bg-orange-500 cursor-not-allowed';
    }
    if (day.isPast) {
        return 'bg-gray-500 cursor-not-allowed';
    }
    if (day.isFull && day.isTooLate) {
        return 'bg-gray-500 cursor-not-allowed';
    }
    if (day.isFull) {
        return 'bg-gray-500 cursor-not-allowed';
    }
    if (day.isTooLate) {
        return 'bg-gray-500 cursor-not-allowed';
    }
    return 'bg-white cursor-pointer';
}

// const dateParam = `${props.year}-${String(props.month).padStart(2, '0')}-${String(newDate).padStart(2, '0')}`;
</script>

<template>
    <div
        class="xl:px-32 py-8 flex flex-col lg:flex-row justify-center items-start space-y-4 lg:space-y-0 lg:space-x-4 bg-pink-200 min-h-screen">
        <div class="w-full lg:w-1/2 bg-white/70 rounded-xl p-5">
            <div>
                <a :href="route('reservation.retour')"
                    class="bg-red-500 block w-fit hover:bg-red-800 text-white font-medium transition px-4 py-2 rounded-lg mb-10">Retour
                    au
                    menu</a>
            </div>
            <h1 class="text-3xl drop-shadow-md mb-8">Calendrier de Réservation</h1>
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
                    class="text-center py-4 lg:p-4 border border-black rounded" :class="getDayClass(day)"
                    @click="!day.isClosed && !day.isHoliday && !day.isFull && !day.isPast && !day.isTooLate ? selectDate(day.date) : null">
                    <span v-if="day.date">{{ day.date }}</span>
                </div>
            </div>
        </div>
        <div v-if="selectedDate" class="mt-5 lg:w-1/2 p-5 bg-white/70 rounded-xl">
            <h3 class="text-3xl mb-6">Sélectionnez une disponibilité pour le {{ selectedDate }}</h3>
            <ul class="flex flex-wrap">
                <li v-for="time in availableTimes" :key="time.id" class="mr-4 mb-4"><button
                        class="bg-blue-500 text-white px-5 py-4 hover:bg-blue-800 transition rounded"
                        @click="selectTime(time)" :disabled="processing">{{ time }}</button>
                </li>
            </ul>
        </div>
    </div>
</template>
