<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
const props = defineProps(['tailles', 'poils', 'etats', 'services', 'durees', 'prixs']);

// Initialisation du formulaire avec useForm
const form = useForm({
    service_id: '',
    taille_id: '',
    poil_id: '',
    etat_id: '',
    race: '',
    duree: '',
    duree_id: ''
});

function getNomByIdAndProp(id, propName) {
    const propArray = props[propName]; // Accéder au prop par son nom
    const prop = propArray.find(prop => prop.id === id);
    return prop ? prop.nom : '';
};

function getDureeByIds(taille_id, service_id, etat_id) {
    const dureeObj = props.durees.find(duree =>
        duree.taille_id === taille_id &&
        duree.service_id === service_id &&
        duree.etat_id === etat_id
    );
    return dureeObj ? dureeObj.duree : null;
};

function getDureeIdByIds(taille_id, service_id, etat_id) {
    const dureeObj = props.durees.find(duree =>
        duree.taille_id === taille_id &&
        duree.service_id === service_id &&
        duree.etat_id === etat_id
    );
    return dureeObj ? dureeObj.id : null;
};

function getPrixByIds(taille_id, service_id) {
    const prixObj = props.prixs.find(prix =>
        prix.taille_id === taille_id &&
        prix.service_id === service_id
    );
    return prixObj ? prixObj.prix : null;
};

const processing = ref(false);
const errors = ref({});
const step = ref(1);

// Fonction pour sélectionner une option
const selectOption = (field, value) => {
    form[field] = value;
    errors.value[field] = '';
    step.value++;  // Clear previous error message
    let nomPoil = getNomByIdAndProp(form.poil_id, 'poils').toLowerCase();
    let nomTaille = getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase();
    if ((nomPoil.includes('dur') || nomPoil.includes('ras') || nomPoil.includes('court')) && step.value === 3) {
        form['etat_id'] = 1;
        step.value++;
    } else if (nomTaille.includes('chat') && step.value === 2) {
        form['poil_id'] = 2;
        step.value++;
    } else if (nomTaille.includes('chat') && step.value === 4) {
        form['service_id'] = 1;
        form['race'] = 'chat';
        step.value++;
    }
    if (step.value === 5) {
        form['duree'] = getDureeByIds(form.taille_id, form.service_id, form.etat_id);
        form['duree_id'] = getDureeIdByIds(form.taille_id, form.service_id, form.etat_id);
    }
};

const back = () => {
    step.value--;
    let nomPoil = getNomByIdAndProp(form.poil_id, 'poils').toLowerCase();
    let nomTaille = getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase();
    if ((nomPoil.includes('dur') || nomPoil.includes('ras') || nomPoil.includes('court')) && step.value === 3) {
        step.value--;
    } else if (nomTaille.includes('chat') && step.value === 2) {
        form['race'] = '';
        step.value--;
    } else if (nomTaille.includes('chat') && step.value === 4) {
        step.value--;
    }
};

function afficherService(service) {
    // Logique pour décider si le service doit être affiché
    let nomPoil = getNomByIdAndProp(form.poil_id, 'poils').toLowerCase();
    let nomService = service.nom.toLowerCase();
    if (nomPoil.includes('dur') && (nomService.includes('coupe') || nomService.includes('tonte'))) {
        return false;
    } else if (nomPoil.includes('ras') && (nomService.includes('coupe') || nomService.includes('tonte') || nomService.includes('trim'))) {
        return false;
    } else if (nomService.includes('trim') && !nomPoil.includes('dur')) {
        return false;
    } else {
        return true;
    }
}

function afficherEtat(etat) {
    let nomTaille = getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase();
    let nomEtat = etat.nom.toLowerCase();
    if (nomTaille.includes('chat') && (nomEtat.includes('pas') || nomEtat.includes('aucun'))) {
        return false;
    } else {
        return true;
    }
}

// Fonction de soumission du formulaire
const submit = async () => {
    processing.value = true;
    errors.value = {};

    // Ajoutez des logs pour déboguer
    console.log('Route:', route('reservation.process'));
    console.log('Form data:', form.data());

    try {
        await form.post(route('reservation.process'), {
            onFinish: () => processing.value = false,
            onError: (err) => errors.value = err,
            onSuccess: () => form.reset(),
        });
        console.log('Form submission finished');
    } catch (error) {
        console.error('Form submission error:', error);
    }
};
</script>
<template>
    <FrontLayout title="Réservation" :page="$page">
        <div class="max-w-lg mx-auto pt-40 pb-28 min-h-screen">
            <h2 class="text-4xl text-center drop-shadow-lg pb-10">Réservation</h2>

            <!-- Sélection de la taille -->
            <div v-if="step === 1" class="mb-5">
                <label class="text-gray-800 mb-2 block font-bold">Sélectionnez une taille :</label>
                <div v-for="taille in tailles" :key="taille.id"
                    class="flex items-center justify-between mb-2 bg-white/75 rounded-lg p-3">
                    <div class="flex flex-wrap space-x-4 items-center">
                        <span>{{ taille.nom }}</span><span v-if="taille.exemple" class="italic text-sm">{{ '(' +
                            taille.exemple + ')' }}</span>
                    </div>
                    <button @click="selectOption('taille_id', taille.id)"
                        class="text-white bg-blue-500 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2">
                        Sélectionner
                    </button>
                </div>
                <div v-if="errors.taille_id" class="text-sm text-red-600">
                    {{ errors.taille_id }}
                </div>
            </div>
            <div v-if="form.taille_id && step > 1" class="mb-5">
                {{ getNomByIdAndProp(form.taille_id, 'tailles') }}
            </div>

            <div v-if="step === 2" class="mb-5">
                <label class="text-gray-800 mb-2 block font-bold">Sélectionnez un poil :</label>
                <div v-for="poil in poils" :key="poil.id"
                    class="flex items-center justify-between mb-2 bg-white/75 rounded-lg p-3">
                    <span>{{ poil.nom }}</span><span v-if="poil.illustration"><img
                            class="h-36 aspect-square object-cover" :src="'/storage/' + poil.illustration"
                            alt="Illustration type poil"></span>
                    <button @click="selectOption('poil_id', poil.id)"
                        class="text-white bg-blue-500 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2">
                        Sélectionner
                    </button>
                </div>
                <div v-if="errors.poil_id" class="text-sm text-red-600">
                    {{ errors.poil_id }}
                </div>
                <button @click="back()"
                    class="text-white bg-red-500 hover:bg-red-700 font-medium rounded-lg text-sm px-4 py-2">
                    Retour
                </button>
            </div>
            <div v-if="form.poil_id && step > 2 && !getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase().includes('chat')"
                class="mb-5">
                {{ getNomByIdAndProp(form.poil_id, 'poils') }}
            </div>

            <div v-if="step === 3" class="mb-5">
                <label class="text-gray-800 mb-2 block font-bold">Sélectionnez un etat :</label>
                <div v-for="etat in etats" :key="etat.id">
                    <div class="flex items-center justify-between mb-2 bg-white/75 rounded-lg p-3"
                        v-if="afficherEtat(etat)">
                        <div class="flex flex-wrap space-x-4 items-center">
                            <span>{{ etat.nom }}</span><span v-if="etat.description" class="italic text-sm">{{
                                '(' +
                                etat.description
                                + ')'
                            }}</span>
                        </div>
                        <button @click="selectOption('etat_id', etat.id)"
                            class="text-white bg-blue-500 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2">
                            Sélectionner
                        </button>
                    </div>
                </div>
                <div v-if="errors.etat_id" class="text-sm text-red-600">
                    {{ errors.etat_id }}
                </div>
                <button @click="back()"
                    class="text-white bg-red-500 hover:bg-red-700 font-medium rounded-lg text-sm px-4 py-2">
                    Retour
                </button>
            </div>
            <div v-if="form.etat_id && step > 3 && !getNomByIdAndProp(form.poil_id, 'poils').toLowerCase().includes('dur')"
                class="mb-5">
                {{ getNomByIdAndProp(form.etat_id, 'etats') }}
            </div>

            <!-- Sélection du service (visible après sélection de la taille) -->
            <div v-if="step === 4" class="mb-5">
                <label class="text-gray-800 mb-2 block font-bold">Sélectionnez un service :</label>
                <div v-for="service in services" :key="service.id">
                    <div class="flex items-center justify-between mb-2 bg-white/75 rounded-lg p-3"
                        v-if="afficherService(service)">
                        <div class="flex flex-wrap space-x-4 items-center">
                            <span>{{ service.nom }}</span><span v-if="service.description" class="italic text-sm">{{ '('
                                +
                                service.description
                                +
                                ')'
                                }}</span>
                        </div>
                        <button @click="selectOption('service_id', service.id)"
                            class="text-white bg-blue-500 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2">
                            Sélectionner
                        </button>
                    </div>
                </div>
                <div v-if="errors.service_id" class="text-sm text-red-600">
                    {{ errors.service_id }}
                </div>
                <button @click="back()"
                    class="text-white bg-red-500 hover:bg-red-700 font-medium rounded-lg text-sm px-4 py-2">
                    Retour
                </button>
            </div>
            <div v-if="form.service_id && step > 4 && !getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase().includes('chat')"
                class="mb-5">
                {{ getNomByIdAndProp(form.service_id, 'services') }}<br>
                {{ 'Durée estimée: ' + form.duree.slice(0, 5) }}<br>
                {{ 'Prix estimé: ' + getPrixByIds(form.taille_id, form.service_id) + '€' }}
            </div>
            <div
                v-if="form.service_id && step > 4 && getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase().includes('chat')">
                {{ 'Durée estimée: ' + form.duree.slice(0, 5) }}<br>
                {{ 'Prix estimé: ' + (form.duree.slice(0, 5) === '00:30' ? '15' : '25') + '€' }}
            </div>
            <div v-if="step === 5" class="mb-5">
                <label v-if="!getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase().includes('chat')" for="race"
                    class="text-gray-800 mb-2 block font-bold">Entrez la race et le nom de votre chien ainsi qu'une
                    éventuelle remarque</label>
                <div class="space-y-4 bg-white/75 p-4 rounded-lg flex flex-col">
                    <textarea v-if="!getNomByIdAndProp(form.taille_id, 'tailles').toLowerCase().includes('chat')"
                        id="race" v-model="form.race"></textarea>
                    <div v-if="errors.race" class="text-sm text-red-600">
                        {{ errors.race }}
                    </div>
                    <button @click="submit" :disabled="processing"
                        class=" text-white bg-blue-500 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 w-fit">
                        Confirmer, vers agenda
                    </button>
                    <button @click="back()"
                        class="text-white bg-red-500 hover:bg-red-700 font-medium rounded-lg text-sm px-4 py-2 w-fit">
                        Retour
                    </button>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
