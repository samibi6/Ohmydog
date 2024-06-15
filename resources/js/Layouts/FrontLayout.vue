<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import FrontNavLink from '@/Components/FrontNavLink.vue';
import ResponsiveFrontNavLink from '@/Components/ResponsiveFrontNavLink.vue';
import { ref, watch } from "vue";
import CookieBanner from '@/Components/CookieBanner.vue';
import { onMounted } from 'vue';
import feather from 'feather-icons';

onMounted(() => {
    feather.replace();
});
defineProps({
    title: String,
    page: Object,
    canLogin: Boolean,
});
document.body.classList.add('noscroll-y');
document.querySelector('html').classList.remove('no-scroll');
const isOpen = ref(false);

const logout = () => {
    router.post(route('logout'));
};
const burger = () => {
    isOpen.value = !isOpen.value
};
watch(isOpen, (newValue) => {
    if (newValue) {
        document.querySelector('html').classList.add('no-scroll');
    } else {
        document.querySelector('html').classList.remove('no-scroll');
    }
});
</script>
<style scoped>
#logo {
    background-image: url('/storage/logo.svg');
    background-repeat: no-repeat;
    background-size: 105%;
    background-position: center;
}
</style>
<style>
.no-scroll {
    overflow: hidden;
}

.noscroll-y {
    overflow-x: hidden;
}

html {
    overflow-x: hidden;
}
</style>
<template>
    <cookie-banner></cookie-banner>
    <div v-if="isOpen" class="h-full w-full bg-pink-300/75 backdrop-blur-md fixed z-[100] overflow-hidden">
        <button @click="burger()" class="w-fit block ml-auto m-5">
            <svg class="drop-shadow-lg" xmlns="http://www.w3.org/2000/svg" fill="#fd2577" version="1.1" id="Capa_1"
                width="45" height="45" viewBox="0 0 45 40" preserveAspectRatio="xMidYMid meet">
                <g>
                    <path class="text-red-500" d="M27.948,20.878L40.291,8.536c1.953-1.953,1.953-5.119,0-7.071c-1.951-1.952-5.119-1.952-7.07,0L20.878,13.809L8.535,1.465
		c-1.951-1.952-5.119-1.952-7.07,0c-1.953,1.953-1.953,5.119,0,7.071l12.342,12.342L1.465,33.22c-1.953,1.953-1.953,5.119,0,7.071
		C2.44,41.268,3.721,41.755,5,41.755c1.278,0,2.56-0.487,3.535-1.464l12.343-12.342l12.343,12.343
		c0.976,0.977,2.256,1.464,3.535,1.464s2.56-0.487,3.535-1.464c1.953-1.953,1.953-5.119,0-7.071L27.948,20.878z" />
                </g>
            </svg>
        </button>
        <div class="flex flex-col items-center space-y-5 justify-center">
            <ResponsiveFrontNavLink :href="route('home')" :active="route().current('home')">Accueil
            </ResponsiveFrontNavLink>
            <ResponsiveFrontNavLink :href="route('tarifs')" :active="route().current('tarifs')">
                Tarifs
            </ResponsiveFrontNavLink>
            <ResponsiveFrontNavLink :href="route('galerie')" :active="route().current('galerie')">Galerie
            </ResponsiveFrontNavLink>
            <ResponsiveFrontNavLink :href="route('contact')" :active="route().current('contact')">Contact
            </ResponsiveFrontNavLink>
            <a class="bg-green-500 hover:bg-green-800 w-fit text-white font-bold text-lg py-2 px-4 mt-4 block mx-auto rounded-lg transition"
                :href="$page.props.auth.user ? route('reservation.select') : route('reservation.login')">Prendre
                Rendez-vous</a>
        </div>
        <div v-if="page.props.auth.user" class="flex flex-col items-center justify-center mt-8">
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center justify-center">
                    <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                        <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.name">
                    </div>

                    <div>
                        <div class="font-medium text-base text-white text-center">
                            {{ $page.props.auth.user.name }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 space-y-4 text-center flex flex-col justify-center items-center">
                    <ResponsiveFrontNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                        Compte
                    </ResponsiveFrontNavLink>
                    <ResponsiveFrontNavLink :href="route('user.reservations')"
                        :active="route().current('user.reservations')">
                        Mes Réservations
                    </ResponsiveFrontNavLink>
                    <ResponsiveFrontNavLink v-if="$page.props.jetstream.hasApiFeatures"
                        :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                        API Tokens
                    </ResponsiveFrontNavLink>

                    <!-- Authentication -->
                    <form method="POST" @submit.prevent="logout">
                        <ResponsiveFrontNavLink as="button">
                            <span class="text-red-600 drop-shadow-md">Déconnexion</span>
                        </ResponsiveFrontNavLink>
                    </form>
                </div>
            </div>
            <div v-if="page.props.auth.user.role_id === 1" class="flex items-center justify-center mt-5 w-full">
                <a class="bg-blue-500 hover:bg-blue-800 transition text-white font-bold text-xl px-4 py-3 rounded-full mx-3"
                    :href="route('dashboard')">Administration</a>
            </div>
        </div>
        <div v-else class="flex items-center justify-center w-full mt-10">
            <a class="bg-blue-500 hover:bg-blue-800 transition text-white text-xl font-bold px-4 py-3 rounded-full mx-3"
                :href="route('login')">Connexion</a>
        </div>
    </div>

    <Head :title="title" />
    <nav
        class="bg-gradient-to-r from-white/70 to-pink-300/70 from-25% to-60% sm:from-25% sm:to-60% md:from-10% md:to-30% rounded-full mt-8 h-16 w-[90%] lg:w-9/12 fixed inset-x-1/2 translate-x-[-50%] shadow-lg z-10">
        <a id="logo" :href="route('home')"
            class="bg-white rounded-full w-[15%] min-w-32 h-full absolute z-10 shadow-md">
        </a>
        <div class="h-full opacity-100 backdrop-blur shadow-md rounded-full w-full fixed">
        </div>
        <div class="absolute w-full h-full mx-auto flex items-center">
            <div class="h-full justify-center w-full flex">
                <div
                    class="w-full 2xl:ml-72 ml-36 2xl:pl-24 md:flex flex-nowrap justify-center items-center space-x-5 hidden">
                    <FrontNavLink :href="route('home')" :active="route().current('home')">Accueil</FrontNavLink>
                    <FrontNavLink :href="route('tarifs')" :active="route().current('tarifs')">
                        Tarifs
                    </FrontNavLink>
                    <FrontNavLink :href="route('galerie')" :active="route().current('galerie')">Galerie
                    </FrontNavLink>
                    <FrontNavLink :href="route('contact')" :active="route().current('contact')">Contact
                    </FrontNavLink>

                </div>
                <div class="md:flex h-full items-center ml-auto hidden">
                    <a class="bg-green-500 justify-self-end hover:bg-green-800 w-fit text-white font-bold text-sm py-2 px-4 text-nowrap hidden xl:block rounded-lg transition"
                        :href="$page.props.auth.user ? route('reservation.select') : route('reservation.login')">Prendre
                        Rendez-vous</a>
                </div>

            </div>


            <div v-if="page.props.auth.user" class="md:flex h-full items-center ml-auto hidden">
                <Dropdown align="right">
                    <template #trigger>
                        <button v-if="page.props.jetstream.managesProfilePhotos"
                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                            <img class="h-8 w-8 rounded-full object-cover" :src="page.props.auth.user.profile_photo_url"
                                :alt="page.props.auth.user.name">
                        </button>

                        <span v-else class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center px-3 text-nowrap m-3 py-3.5 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 bg-white/75 hover:bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                {{ page.props.auth.user.name }}

                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            Manage Account
                        </div>

                        <DropdownLink :href="route('profile.show')">
                            Compte
                        </DropdownLink>
                        <DropdownLink :href="route('user.reservations')">
                            Mes Réservations
                        </DropdownLink>
                        <DropdownLink class="xl:hidden"
                            :href="$page.props.auth.user ? route('reservation.select') : route('reservation.login')">
                            Prendre Rendez-vous
                        </DropdownLink>
                        <DropdownLink v-if="page.props.auth.user.role_id === 1" :href="route('dashboard')">
                            Administration
                        </DropdownLink>
                        <DropdownLink v-if="page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                            API Tokens
                        </DropdownLink>

                        <div class="border-t border-gray-200" />

                        <!-- Authentication -->
                        <form @submit.prevent="logout">
                            <DropdownLink as="button">
                                <span class="text-red-500">Déconnexion</span>
                            </DropdownLink>
                        </form>
                    </template>
                </Dropdown>
            </div>
            <div v-else class="md:flex h-full w-fit items-center ml-auto hidden">
                <a class="bg-blue-500 hover:bg-blue-800 transition text-white font-bold px-4 py-3 rounded-full mx-3"
                    :href="route('login')">Connexion</a>
            </div>

            <div class="h-full w-fit items-center ml-auto md:hidden content-center mr-5 mt-1">
                <button @click="burger()">
                    <svg class="drop-shadow-lg" viewBox="0 0 100 80" width="40" height="40" color="#ec4899">
                        <rect width="100" height="20" rx="10" fill="currentcolor"></rect>
                        <rect y="30" width="100" height="20" rx="10" fill="currentcolor"></rect>
                        <rect y="60" width="100" height="20" rx="10" fill="currentcolor"></rect>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
    <header v-if="$slots.header" class="bg-pink-100 shadow pt-32">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot name="header" />
        </div>
    </header>
    <main class="bg-pink-200 z-0">
        <slot />
    </main>
    <footer
        class="bg-pink-300 p-12 flex flex-col space-y-4 sm:space-y-0 sm:flex-row justify-around items-center text-white">
        <ul class="space-y-4 flex flex-col items-center sm:items-start">
            <li class="flex drop-shadow-lg">

                <a href="tel:0470681419"
                    class="flex space-x-2 hover:underline hover:scale-110 hover:bg-black/30 rounded-full p-2 transition hover:shadow-xl font-medium"><i
                        data-feather="phone" class=""></i><span>0470 68 14
                        19</span></a>
            </li>
            <li class="flex drop-shadow-lg">

                <a href="mailto:contact@oh-my-dog.be"
                    class="flex space-x-2 hover:underline hover:scale-110 hover:bg-black/30 rounded-full p-2 transition hover:shadow-xl font-medium"><i
                        data-feather="mail" class=""></i><span>contact@oh-my-dog.be</span></a>
            </li>
            <li class="flex text-center sm:text-left drop-shadow-lg"><a href="https://maps.app.goo.gl/zJG5nfkCqTxfw2yJ9"
                    target="_blank"
                    class="flex space-x-2 hover:underline hover:scale-110 hover:bg-black/30 rounded-full p-2 transition hover:shadow-xl font-medium"><i
                        data-feather="map-pin" class=""></i>
                    <address>
                        Rue de Petit-Leez 156,<br />5031 Gembloux
                    </address>
                </a>
            </li>
        </ul>
        <a :href="route('mentionsLegales')"
            class="hover:underline transition font-bold drop-shadow-md hover:scale-110 text-md hover:shadow-xl hover:bg-black/30 rounded-full p-3">Mentions
            Légales</a>
        <ul class="flex items-center space-x-6">
            <li><a href="https://www.instagram.com/ohmydog_salondetoilettage?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                    target="_blank" class=""><i data-feather="instagram"
                        class="w-14 h-14 hover:scale-125 transition hover:bg-black/30 p-1 rounded-2xl hover:shadow-xl text-white drop-shadow-lg"></i></a>
            </li>
            <li><a href="https://www.facebook.com/profile.php?id=61554833541608" target="_blank"><i
                        data-feather="facebook"
                        class="w-14 h-14 hover:scale-125 transition hover:bg-black/30 p-1 rounded-full hover:shadow-xl text-white drop-shadow-lg"></i></a>
            </li>
        </ul>
    </footer>
</template>
