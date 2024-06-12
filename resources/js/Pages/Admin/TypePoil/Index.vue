<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import heic2any from 'heic2any';

const props = defineProps(['typePoils']);

let confirmEntryDelete = ref(false);
let typePoilDelete = ref(null);
let open = ref(false);

const form = usePrecognitionForm("post", route("typePoil.store"), {
    nom: '',
    illustration: null,
});

form.setValidationTimeout(300);

const handleFileChange = async (event, field) => {
    const file = event.target.files[0];
    const fileName = file.name.toLowerCase();
    if (file && (fileName.endsWith('.heic') || fileName.endsWith('.heif'))) {
        try {
            const convertedBlob = await heic2any({
                blob: file,
                toType: 'image/jpeg',
            });
            form[field] = new File([convertedBlob], file.name.replace(/\.[^/.]+$/, '.jpg'), {
                type: 'image/jpeg',
            });
            form.validate(field);
        } catch (error) {
            console.error('Error converting HEIC to JPEG:', error);
        }
    } else {
        form[field] = file;
        form.validate(field);
    }
};

const submit = () => {
    form.post(route('typePoil.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
};

function confirmingEntryDeletion(typePoil) {
    typePoilDelete.value = typePoil.nom;
    deleteForm.id = typePoil.id;
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
    deleteForm.delete(route('typePoil.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};

</script>

<template>
    <AppLayout title="Type poil">
        <template #header>

        </template>
        <div class="w-fit mx-auto">
            <button @click="open = !open"
                class="bg-blue-500 p-3 rounded-lg hover:bg-blue-800 font-medium text-white m-5">Afficher
                formulaire</button>
        </div>
        <div v-if="open">
            <form @submit.prevent="submit" class="max-w-lg mx-auto">
                <h2 class="text-2xl text-center mb-4">Créer un type de poil</h2>

                <div class="mb-5">
                    <label for="nom" class="text-gray-700 mb-2 block font-medium">Nom du type de poil</label>
                    <input type="text" id="nom" v-model="form.nom" @change="form.validate('nom')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('nom')" class="text-sm text-red-600">
                        {{ form.errors.nom }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="illustration" class="text-gray-700 mb-2 block font-medium">image d'illustration
                        (optionnel)</label>
                    <input type="file" id="illustration" @change="handleFileChange($event, 'illustration')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full" />
                    <div v-if="form.invalid('illustration')" class="text-sm text-red-600">
                        {{ form.errors.illustration }}
                    </div>
                </div>



                <div class="flex justify-center">
                    <button :disabled="form.processing"
                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                        Ajouter le type de poil
                    </button>
                </div>
            </form>
        </div>
        <div class="py-4 pb-10">
            <div v-for="typePoil in typePoils"
                class="border-2 w-fit mx-auto flex bg-white p-2 space-x-4 mb-4 items-center rounded-xl">
                <img v-if="typePoil.illustration" class="aspect-square object-cover h-24"
                    :src="'/storage/' + typePoil.illustration" alt="">
                <h2 class="text-2xl text-center mb-4">{{ typePoil.nom }}</h2>
                <a class="bg-blue-500 p-2 rounded-lg text-white font-medium hover:bg-blue-800"
                    :href="route('typePoil.edit', { typePoil })">Modifier</a>
                <a class="bg-red-500 p-2 rounded-lg text-white font-medium hover:bg-red-800"
                    @click="confirmingEntryDeletion(typePoil)">Supprimer</a>
            </div>
        </div>


        <DialogModal :show="confirmEntryDelete" @close="closeModal">
            <template #title>
                Supprimer type de poil
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer le type de poil {{ typePoilDelete }} ?
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
