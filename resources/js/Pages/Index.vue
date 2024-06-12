<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
import { router } from '@inertiajs/vue3';
import { useHead } from '@unhead/vue'
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    user: null,
});
useHead({
    title: 'Oh my dog - Salon de Toilettage Canin à Grand-Leez (Gembloux)',

    meta: [
        { name: 'description', content: 'Oh my dog - Salon de Toilettage Canin à Grand-Leez (Gembloux). Offrez à votre compagnon le meilleur toilettage avec nos services de qualité.' },
        { name: 'keywords', content: 'Oh my dog, accueil, toilettage canin, salon de toilettage, Grand-Leez, Gembloux, chien, chat, toilettage, animaux de compagnie' },
        { name: 'author', content: 'Oh my dog' },
        { name: 'robots', content: 'index, follow' },
        { property: 'og:title', content: 'Oh my dog - Salon de Toilettage Canin à Grand-Leez (Gembloux)' },
        { property: 'og:description', content: 'Oh my dog - Salon de Toilettage Canin à Grand-Leez (Gembloux). Offrez à votre compagnon le meilleur toilettage avec nos services de qualité.' },
        { property: 'og:type', content: 'website' },
        { property: 'og:url', content: 'https://oh-my-dog.be/' }, // Remplacez par l'URL de votre site
        { property: 'og:image', content: '/storage/capture.png' }, // URL de votre image pour les réseaux sociaux
        { property: 'og:locale', content: 'fr_BE' }, // Si votre site est en français
        { property: 'og:site_name', content: 'Oh my dog' },
    ]
})
</script>
<style>
#logo {
    background-image: url('/storage/logo.svg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}
</style>
<template>
    <FrontLayout title="Accueil" :page="$page" :canLogin="canLogin">
        <div class="z-0 h-screen overflow-hidden">
            <video
                class="w-screen h-screen z-0 grayscale-[80%] brightness-[1] opacity-[60%] blur-[3px] object-cover hidden lg:block relative top-0"
                autoplay loop muted>
                <source src="/storage/home-video-w.mp4" type="video/mp4">
            </video>
            <video
                class="w-screen z-0 grayscale-[80%] brightness-[1] opacity-[60%] blur-[3px] relative lg:hidden object-cover h-screen top-0"
                autoplay loop muted>
                <source src="/storage/home-video-m.mp4" type="video/mp4">
            </video>
            <div
                class="bg-white/50 shadow-md backdrop-blur-[2px] rounded-3xl absolute inset-x-1/2 translate-x-[-50%] top-[30%] mx-auto w-[90vw] sm:w-[60vw] md:w-[60vw] lg:w-[50vw] xl:w-[35vw] p-5">
                <div id="logo"
                    class="text-black h-[15vw] sm:w-3/4 sm:h-[8vw] md:h-[10vw] lg:h-[8vw] xl:h-[5vw] mx-auto rounded-3xl bg-white/40">
                </div>
                <p
                    class="mt-4 text-xl sm:text-2xl lg:text-2xl text-center font-medium sm:leading-8 lg:leading-12 mx-auto block w-11/12 text-gray-800">
                    Accueille
                    vos
                    compagnons
                    à
                    4 pattes
                    dans un
                    espace
                    chaleureux
                    où je
                    privilégie
                    le bien
                    être
                    de votre
                    animal avant tout !!</p>
                <a class="bg-green-500 hover:bg-green-800 w-fit text-white font-bold text-lg py-2 px-4 mt-4 block mx-auto rounded-lg transition"
                    :href="$page.props.auth.user ? route('reservation.select') : route('reservation.login')">Prendre
                    Rendez-vous</a>
            </div>
        </div>
        <div
            class="w-screen py-52 px-8 lg:p-32 xl:p-52 bg-white/60 flex flex-col-reverse lg:flex-row justify-center items-center">
            <div class="lg:w-1/2 xl:w-72 mt-5 lg:mr-5 lg:mt-0">
                <img src="/storage/IMG-APropos.jpg" class="aspect-square object-cover object-center w-72 h-72" alt="">
            </div>
            <div class="flex flex-col w-fit xl:w-2/5 items-center space-y-8">
                <h2 class="text-5xl drop-shadow-md">À propos</h2>

                <p class="text-lg font-medium text-justify">Passionnée par les animaux, je suis diplômée en tant
                    qu'assistante en
                    soins
                    animaliers et j'ai
                    ensuite suivi une formation d'esthétique canine, avant d'ouvrir mon salon de toilettage. Je
                    serai
                    donc aux petits soins pour votre animal afin qu'il soit beau tout en privilégiant son bien être
                    !<br />
                    Je suis impatiente de le découvrir !</p>
            </div>
        </div>
    </FrontLayout>
</template>
