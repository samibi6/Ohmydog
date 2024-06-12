<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
import { computed, ref } from 'vue';
import { ImgComparisonSlider } from '@img-comparison-slider/vue';
import { useHead } from '@unhead/vue';

const props = defineProps({
    galleries: Array,
    canLogin: Boolean,
    canRegister: Boolean,
    user: [Object, null],
});

useHead({
    title: 'Oh my dog - Galerie photo',
    meta: [
        { name: 'description', content: 'Découvrez la galerie photo de Oh my dog et admirez nos précédents travaux de toilettage canin. Nous sommes situés à Grand-Leez (Gembloux) et nous aimons partager notre passion pour les animaux avec nos clients.' },
        { name: 'keywords', content: 'Oh my dog, galerie photo, toilettage canin, Grand-Leez, Gembloux, chien, chat, toilettage, animaux de compagnie' },
        { name: 'author', content: 'Oh my dog' },
        { name: 'robots', content: 'index, follow' },
        { property: 'og:title', content: 'Oh my dog - Galerie photo' },
        { property: 'og:description', content: 'Découvrez la galerie photo de Oh my dog et admirez nos précédents travaux de toilettage canin. Nous sommes situés à Grand-Leez (Gembloux) et nous aimons partager notre passion pour les animaux avec nos clients.' },
        { property: 'og:type', content: 'website' },
        { property: 'og:url', content: 'https://oh-my-dog.be/galerie' }, // Remplacez par l'URL de votre page de galerie photo
        { property: 'og:image', content: '/storage/capture.png' }, // URL relative de votre image pour les réseaux sociaux
        { property: 'og:locale', content: 'fr_BE' }, // Si votre site est en français
        { property: 'og:site_name', content: 'Oh my dog' },
    ]
})

let modalImgBefore = ref(null);
let modalImgAfter = ref(null);
let modalDescription = ref(null);
let modalId = ref(null);

const openImage = (gallery) => {
    modalId.value = gallery.id;
    modalImgBefore.value = gallery.imgBefore;
    modalImgAfter.value = gallery.imgAfter;
    modalDescription.value = gallery.description;
    document.body.classList.add('overflow-hidden');
    console.log(modalImgAfter)
    document.querySelector('.modal').classList.remove('hidden');
}

const closeModal = () => {
    modalId.value = null;
    modalImgBefore.value = null;
    modalImgAfter.value = null;
    modalDescription.value = null;
    document.body.classList.remove('overflow-hidden');
    document.querySelector('.modal').classList.add('hidden');
}

function getNextGalleryId() {
    // Trouver l'index de la galerie actuelle
    const currentIndex = props.galleries.findIndex(gallery => gallery.id === modalId.value);

    // Si la galerie actuelle est la dernière, retourner le premier ID (circular navigation)
    if (currentIndex === props.galleries.length - 1) {
        modalId.value = props.galleries[0].id;
        modalImgBefore.value = props.galleries[0].imgBefore;
        modalImgAfter.value = props.galleries[0].imgAfter;
        modalDescription.value = props.galleries[0].description;
    }
    else {
        modalId.value = props.galleries[currentIndex + 1].id;
        modalImgBefore.value = props.galleries[currentIndex + 1].imgBefore;
        modalImgAfter.value = props.galleries[currentIndex + 1].imgAfter;
        modalDescription.value = props.galleries[currentIndex + 1].description;
    }
}
function getPrevGalleryId() {
    // Trouver l'index de la galerie actuelle
    const currentIndex = props.galleries.findIndex(gallery => gallery.id === modalId.value);

    // Si la galerie actuelle est la dernière, retourner le premier ID (circular navigation)
    if (currentIndex === 0) {
        modalId.value = props.galleries[props.galleries.length - 1].id;
        modalImgBefore.value = props.galleries[props.galleries.length - 1].imgBefore;
        modalImgAfter.value = props.galleries[props.galleries.length - 1].imgAfter;
        modalDescription.value = props.galleries[props.galleries.length - 1].description;
    }
    else {
        modalId.value = props.galleries[currentIndex - 1].id;
        modalImgBefore.value = props.galleries[currentIndex - 1].imgBefore;
        modalImgAfter.value = props.galleries[currentIndex - 1].imgAfter;
        modalDescription.value = props.galleries[currentIndex - 1].description;
    }
}

</script>
<style>
#logo {
    background-image: url('/storage/logo.svg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 999;
    display: flex;
}

.modal.hidden {
    display: none;
}
</style>
<template>
    <FrontLayout title="Galerie" :page="$page" :canLogin="canLogin">
        <div class="z-0 pt-52 pb-24 overflow-hidden flex justify-center min-h-screen">
            <h2 class="text-5xl absolute top-36 drop-shadow-md">Galerie photo</h2>
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 content-start">
                <div v-for="(gallery, index) in galleries" :key="index" @click="openImage(gallery)">
                    <img class="aspect-square w-52 object-cover object-center cursor-pointer hover:opacity-80 transition-opacity"
                        :src="'/storage/' + gallery.imgAfter" :alt="gallery.description" />
                </div>
            </div>
        </div>
        <div class="modal hidden bg-white/60 backdrop-blur-md h-full w-full flex-col items-center justify-center"
            ref="modal" @click.self="closeModal">
            <button @click="closeModal" class="fixed top-10 right-10 h-fit w-fit">
                <i data-feather="x-circle" class="w-14 h-14 text-red-500"></i>
            </button>
            <div class="w-full  md:w-3/6 px-5 flex justify-center items-center text-white space-x-4">
                <button @click="getPrevGalleryId" class="h-fit">
                    <i data-feather="arrow-left-circle" class="w-10 h-10"></i>
                </button>
                <template v-if="modalImgBefore">
                    <ImgComparisonSlider>
                        <img slot="first" class="aspect-square object-cover object-center"
                            :src="'/storage/' + modalImgBefore" />
                        <img slot="second" class="aspect-square object-cover object-center"
                            :src="'/storage/' + modalImgAfter" />
                    </ImgComparisonSlider>
                </template>
                <template v-else>
                    <div>
                        <img :src="'/storage/' + modalImgAfter" class="block aspect-square object-cover object-center"
                            @click="closeModal" />
                    </div>
                </template>
                <button @click="getNextGalleryId" class="h-fit">
                    <i data-feather="arrow-right-circle" class="w-10 h-10"></i>
                </button>
            </div>
            <p class="text-white text-center pt-5 font-medium text-lg">{{ modalDescription }}</p>
        </div>
    </FrontLayout>
</template>
