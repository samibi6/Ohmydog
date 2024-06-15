<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { ref } from "vue";
import heic2any from 'heic2any';

const props = defineProps(['galleries']);

let confirmEntryDelete = ref(false);
let gallerieDelete = ref(null);
let open = ref(false);

const form = usePrecognitionForm("post", route("gallerie.store"), {
    imgAfter: '',
    imgBefore: null,
    description: '',
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
                quality: 0.3,
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
    form.post(route('gallerie.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
};

function confirmingEntryDeletion(gallerie) {
    gallerieDelete.value = gallerie.description;
    deleteForm.id = gallerie.id;
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
    deleteForm.delete(route('gallerie.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};

</script>

<template>
    <AppLayout title="Gallerie">
        <template #header>

        </template>
        <div class="w-fit mx-auto">
            <button @click="open = !open"
                class="bg-blue-500 p-3 rounded-lg hover:bg-blue-800 font-medium text-white m-5">Afficher
                formulaire</button>
        </div>
        <div v-if="open">
            <form @submit.prevent="submit" class="max-w-lg mx-auto">
                <h2 class="text-2xl text-center mb-4">Ajouter image à la gallerie</h2>

                <div class="mb-5">
                    <label for="description" class="text-gray-700 mb-2 block font-medium">description de l'image</label>
                    <textarea id="description" rows="5" v-model="form.description"
                        @change="form.validate('description')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
            </textarea>
                    <div v-if="form.invalid('description')" class="text-sm text-red-600">
                        {{ form.errors.description }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="imgBefore" class="text-gray-700 mb-2 block font-medium">Photo Avant (optionnel)</label>
                    <input type="file" id="imgBefore" @change="handleFileChange($event, 'imgBefore')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full" />
                    <div v-if="form.invalid('imgBefore')" class="text-sm text-red-600">
                        {{ form.errors.imgBefore }}
                    </div>
                </div>

                <div class="mb-5">
                    <label for="imgAfter" class="text-gray-700 mb-2 block font-medium">Photo Après</label>
                    <input type="file" id="imgAfter" @change="handleFileChange($event, 'imgAfter')"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                    <div v-if="form.invalid('imgAfter')" class="text-sm text-red-600">
                        {{ form.errors.imgAfter }}
                    </div>
                </div>



                <div class="flex justify-center">
                    <button :disabled="form.processing"
                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                        Ajouter l'image
                    </button>
                </div>
            </form>
        </div>
        <div class="py-4 pb-10">
            <div v-for="gallerie in galleries"
                class="border-2 w-fit mx-auto flex bg-white p-2 space-x-4 mb-4 items-center rounded-xl">
                <img v-if="gallerie.imgBefore" class="h-24 aspect-square object-cover"
                    :src="'/storage/' + gallerie.imgBefore" alt="">
                <img class="h-24 aspect-square object-cover" :src="'/storage/' + gallerie.imgAfter" alt="">
                <h2 class="text-2xl text-center mb-4">{{ gallerie.description }}</h2>
                <a class="bg-blue-500 p-2 rounded-lg text-white font-medium hover:bg-blue-800"
                    :href="route('gallerie.edit', { gallerie })">Modifier</a>
                <a class="bg-red-500 p-2 rounded-lg text-white font-medium hover:bg-red-800"
                    @click="confirmingEntryDeletion(gallerie)">Supprimer</a>
            </div>
        </div>


        <DialogModal :show="confirmEntryDelete" @close="closeModal">
            <template #title>
                Supprimer l'image
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer l'image {{ gallerieDelete }} ?
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
