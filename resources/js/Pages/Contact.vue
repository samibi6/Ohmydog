<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
import { router } from '@inertiajs/vue3';
import { useHead } from '@unhead/vue';
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
    title: 'Oh my dog - Contactez-nous',
    metaInfo: {
        meta: [
            { name: 'description', content: 'Contactez Oh my dog pour toutes vos questions et demandes de rendez-vous. Nous sommes situés à Grand-Leez (Gembloux) et sommes là pour prendre soin de votre compagnon à quatre pattes.' },
            { name: 'keywords', content: 'Oh my dog, contact, nous contacter, salon de toilettage, Grand-Leez, Gembloux, chien, chat, toilettage, animaux de compagnie' },
            { name: 'author', content: 'Oh my dog' },
            { name: 'robots', content: 'index, follow' },
            { property: 'og:title', content: 'Contactez-nous - Oh my dog' },
            { property: 'og:description', content: 'Contactez Oh my dog pour toutes vos questions et demandes de rendez-vous. Nous sommes situés à Grand-Leez (Gembloux) et sommes là pour prendre soin de votre compagnon à quatre pattes.' },
            { property: 'og:type', content: 'website' },
            { property: 'og:url', content: 'https://oh-my-dog.be/contact' }, // Remplacez par l'URL de votre page de contact
            { property: 'og:image', content: '/storage/capture.png' }, // URL relative de votre image pour les réseaux sociaux
            { property: 'og:locale', content: 'fr_BE' }, // Si votre site est en français
            { property: 'og:site_name', content: 'Oh my dog' },
        ]
    }
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
    <FrontLayout title="Contact" :page="$page" :canLogin="canLogin">
        <div class="z-0 overflow-hidden flex items-center justify-center pt-32 pb-20 lg:pt-48 lg:pb-32">
            <div class="bg-white/60 rounded-3xl w-fit p-4 xl:p-10 xl:pr-32 shadow-lg">
                <h2 class="text-5xl drop-shadow-md pb-10 lg:pb-0 text-center lg:text-left">Contact</h2>
                <div class="flex flex-col-reverse lg:flex-row justify-center items:center lg:space-x-4 xl:space-x-20">
                    <div class="pt-8 hidden lg:block">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2534.132058418072!2d4.760957692340473!3d50.56890890564652!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c19d0024c5bb35%3A0x833c792008b5b669!2sOh%20my%20dog!5e0!3m2!1sfr!2sbe!4v1717757273058!5m2!1sfr!2sbe"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="p-4 hidden lg:pt-10 lg:hidden sm:flex justify-center">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2534.132058418072!2d4.760957692340473!3d50.56890890564652!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c19d0024c5bb35%3A0x833c792008b5b669!2sOh%20my%20dog!5e0!3m2!1sfr!2sbe!4v1717757273058!5m2!1sfr!2sbe"
                            width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="lg:pt-10 sm:hidden flex justify-center">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2534.132058418072!2d4.760957692340473!3d50.56890890564652!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c19d0024c5bb35%3A0x833c792008b5b669!2sOh%20my%20dog!5e0!3m2!1sfr!2sbe!4v1717757273058!5m2!1sfr!2sbe"
                            width="300" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                    <div class="pb-10 lg:pb-0 flex flex-col justify-center">
                        <div class="flex flex-col xl:flex-row items-center space-y-4 xl:space-y-0 xl:space-x-16">
                            <a href="https://www.facebook.com/profile.php?id=61554833541608" target="_blank"
                                class="flex items-center text-2xl font-bold space-x-1 hover:scale-125 transition hover:bg-black/30 px-1 py-2  rounded-2xl hover:shadow-xl text-black hover:text-white drop-shadow-lg"><i
                                    data-feather="facebook" class="w-10 h-10"></i>
                                <span>Facebook</span></a>
                            <a href="https://www.instagram.com/ohmydog_salondetoilettage?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                                target="_blank"
                                class="flex items-center text-2xl font-bold space-x-2 hover:scale-125 transition hover:bg-black/30 px-1 py-2 rounded-2xl hover:shadow-xl text-black hover:text-white drop-shadow-lg"><i
                                    data-feather="instagram" class="w-10 h-10"></i>
                                <span>Instagram</span></a>
                        </div>
                        <ul class="space-y-10 text-xl mt-10 flex flex-col items-center sm:items-start font-medium">
                            <li
                                class="flex drop-shadow-lg bg-pink-300 px-5 py-5 rounded-full w-full justify-center xl:justify-start">

                                <a href="tel:0470681419" class="flex space-x-4 hover:underline"><i data-feather="phone"
                                        class=""></i><span>0470 68
                                        14
                                        19</span></a>
                            </li>
                            <li
                                class="flex drop-shadow-lg bg-pink-300 px-5 py-5 rounded-full w-full justify-center xl:justify-start">

                                <a href="mailto:contact@oh-my-dog.bee" class="flex space-x-4 hover:underline"><i
                                        data-feather="mail" class=""></i><span>contact@oh-my-dog.be</span></a>
                            </li>
                            <li
                                class="flex drop-shadow-lg bg-pink-300 px-5 py-5 rounded-full w-full justify-center xl:justify-start">
                                <a href="https://maps.app.goo.gl/zJG5nfkCqTxfw2yJ9" target="_blank"
                                    class="flex space-x-4 hover:underline"><i data-feather="map-pin" class=""></i>
                                    <address>
                                        Rue de Petit-Leez 156,<br class="lg:hidden" /> 5031 Gembloux
                                    </address>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
