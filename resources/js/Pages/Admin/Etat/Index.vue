<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['etats']);

let confirmEntryDelete = ref(false);
let etatDelete = ref(null);
let open = ref(false);

const form = usePrecognitionForm("post", route("etat.store"), {
    nom: '',
    description: '',
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

function confirmingEntryDeletion(etat) {
    etatDelete.value = etat.nom;
    deleteForm.id = etat.id;
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
    deleteForm.delete(route('etat.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};

</script>

<template>
    <AppLayout title="Etat">
        <template #header>

        </template>
        <div class="w-fit mx-auto">
            <button @click="open = !open"
                class="bg-blue-500 p-3 rounded-lg hover:bg-blue-800 font-medium text-white m-5">Afficher
                formulaire</button>
        </div>
        <div v-if="open">
            <form @submit.prevent="submit" class="max-w-lg mx-auto">
                <h2 class="text-2xl text-center mb-4">Créer un etat</h2>

                <div class="mb-5">
                    <label for="nom" class="text-gray-700 mb-2 block font-medium">Nom de l'etat</label>
                    <input type="text" id="nom" v-model="form.nom" @change="form.validate('nom')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('nom')" class="text-sm text-red-600">
                        {{ form.errors.nom }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="description" class="text-gray-700 mb-2 block font-medium">description de l'etat</label>
                    <input type="text" id="description" v-model="form.description"
                        @change="form.validate('description')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('description')" class="text-sm text-red-600">
                        {{ form.errors.description }}
                    </div>
                </div>



                <div class="flex justify-center">
                    <button :disabled="form.processing"
                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                        Ajouter l'etat
                    </button>
                </div>
            </form>
        </div>
        <div class="py-4 pb-10">
            <div v-for="etat in etats"
                class="border-2 w-fit mx-auto flex bg-white p-2 space-x-4 mb-4 items-center rounded-xl">
                <h2 class="text-2xl text-center mb-4">{{ etat.nom }}</h2>
                <p class="text-center">{{ etat.description }}</p>
                <a class="bg-blue-500 p-2 rounded-lg text-white font-medium hover:bg-blue-800"
                    :href="route('etat.edit', { etat })">Modifier</a>
                <a class="bg-red-500 p-2 rounded-lg text-white font-medium hover:bg-red-800"
                    @click="confirmingEntryDeletion(etat)">Supprimer</a>
            </div>
        </div>


        <DialogModal :show="confirmEntryDelete" @close="closeModal">
            <template #title>
                Supprimer l'etat
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer l'etat {{ etatDelete }} ?
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
