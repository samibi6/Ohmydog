<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['prixs', 'tailles', 'services']);

let confirmEntryDelete = ref(false);
let prixDelete = ref(null);
let open = ref(false);

const form = usePrecognitionForm("post", route("prix.store"), {
    service_id: '',
    taille_id: '',
    prix: '',
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

function confirmingEntryDeletion(prix) {
    prixDelete.value = prix.nom;
    deleteForm.id = prix.id;
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
    deleteForm.delete(route('prix.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};

function trouverNomService(id) {
    // Recherche de l'objet avec l'id correspondant
    let objetTrouve = props.services.find(objet => objet.id === id);

    // Vérification si l'objet a été trouvé
    if (objetTrouve) {
        return objetTrouve.nom; // Retourne le nom de l'objet trouvé
    } else {
        return "Objet non trouvé"; // Si aucun objet correspondant n'est trouvé
    }
}

function trouverNomTaille(id) {
    // Recherche de l'objet avec l'id correspondant
    let objetTrouve = props.tailles.find(objet => objet.id === id);

    // Vérification si l'objet a été trouvé
    if (objetTrouve) {
        return objetTrouve.nom; // Retourne le nom de l'objet trouvé
    } else {
        return "Objet non trouvé"; // Si aucun objet correspondant n'est trouvé
    }
}

</script>

<template>
    <AppLayout title="Prix">
        <template #header>

        </template>
        <div class="w-fit mx-auto">
            <button @click="open = !open"
                class="bg-blue-500 p-3 rounded-lg hover:bg-blue-800 font-medium text-white m-5">Afficher
                formulaire</button>
        </div>
        <div v-if="open">
            <form @submit.prevent="submit" class="max-w-lg mx-auto">
                <h2 class="text-2xl text-center mb-4">Créer un prix</h2>

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
                    <label for="prix" class="text-gray-700 mb-2 block font-medium">Prix</label>
                    <input type="number" id="prix" v-model="form.prix" @change="form.validate('prix')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('prix')" class="text-sm text-red-600">
                        {{ form.errors.prix }}
                    </div>
                </div>



                <div class="flex justify-center">
                    <button :disabled="form.processing"
                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                        Ajouter le prix
                    </button>
                </div>
            </form>
        </div>
        <div v-if="prixs.length === 0 && services.length != 0 && tailles.length != 0">
            <a class="bg-blue-500 p-5" :href="route('prix.generate')">Générer liste prix</a>
        </div>

        <div class="py-4 pb-10">
            <div v-for="prix in prixs"
                class="border-2 w-fit mx-auto flex bg-white p-2 space-x-4 mb-4 items-center rounded-xl">
                <h2 class="text-2xl text-center mb-1">{{ trouverNomService(prix.service_id) }}</h2>
                <p class="text-center">{{ trouverNomTaille(prix.taille_id) }}</p>
                <p class="text-center">{{ prix.prix + " €" }}</p>
                <a class="bg-blue-500 p-2 rounded-lg text-white font-medium hover:bg-blue-800"
                    :href="route('prix.edit', { prix })">Modifier</a>
                <a class="bg-red-500 p-2 rounded-lg text-white font-medium hover:bg-red-800"
                    @click="confirmingEntryDeletion(prix)">Supprimer</a>
            </div>
        </div>


        <DialogModal :show="confirmEntryDelete" @close="closeModal">
            <template #title>
                Supprimer prix
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer la prix {{ prixDelete }} ?
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
