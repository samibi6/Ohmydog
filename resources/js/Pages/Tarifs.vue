<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
import { computed } from 'vue';
import { useHead } from '@unhead/vue';
const props = defineProps({
    tarifs: Array,
    tailles: Array,
    services: Array,
    canLogin: Boolean,
    canRegister: Boolean,
    user: [Object, null],
});

useHead({
    title: 'Oh my dog - Tarifs',
    meta: [
        { name: 'description', content: 'Consultez les tarifs de Oh my dog pour nos services de toilettage canin. Nous proposons des prix compétitifs pour garder votre compagnon à quatre pattes heureux et en bonne santé. Nous sommes situés à Grand-Leez (Gembloux).' },
        { name: 'keywords', content: 'Oh my dog, tarifs, prix, toilettage canin, Grand-Leez, Gembloux, chien, toilettage, animaux de compagnie' },
        { name: 'author', content: 'Oh my dog' },
        { name: 'robots', content: 'index, follow' },
        { property: 'og:title', content: 'Tarifs - Oh my dog' },
        { property: 'og:description', content: 'Consultez les tarifs de Oh my dog pour nos services de toilettage canin. Nous proposons des prix compétitifs pour garder votre compagnon à quatre pattes heureux et en bonne santé. Nous sommes situés à Grand-Leez (Gembloux).' },
        { property: 'og:type', content: 'website' },
        { property: 'og:url', content: 'https://oh-my-dog.be/tarifs' }, // Remplacez par l'URL de votre page de tarifs
        { property: 'og:image', content: '/storage/capture.png' }, // URL relative de votre image pour les réseaux sociaux
        { property: 'og:locale', content: 'fr_BE' }, // Si votre site est en français
        { property: 'og:site_name', content: 'Oh my dog' },
    ]
})

// Créez une carte des tailles pour un accès facile par ID
const taillesMap = computed(() => {
    const map = {};
    props.tailles.forEach(taille => {
        map[taille.id] = taille.nom;
    });
    return map;
});

// Créez une carte des services pour un accès facile par ID
const servicesMap = computed(() => {
    const map = {};
    props.services.forEach(service => {
        map[service.id] = service.nom;
    });
    return map;
});

// Créez un tableau de données avec les tailles en X et les services en Y
const tableau = computed(() => {
    const result = {};

    props.services.forEach(service => {
        result[service.id] = { name: service.nom, tarifs: {} };
        props.tailles.forEach(taille => {
            result[service.id].tarifs[taille.id] = null; // Initialise à null ou à un autre placeholder
        });
    });

    props.tarifs.forEach(tarif => {
        if (result[tarif.service_id] && result[tarif.service_id].tarifs[tarif.taille_id] !== undefined) {
            result[tarif.service_id].tarifs[tarif.taille_id] = tarif.prix;
        }
    });

    return result;
});

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
    <FrontLayout title="Tarifs" :page="$page" :canLogin="canLogin">
        <div class="z-0 md:h-screen pt-40 pb-24 md:p-0 overflow-hidden flex items-center justify-center">
            <div class="flex flex-col bg-white/60 rounded-3xl p-4 md:w-3/4 md:h-1/2 lg:w-1/2 lg:p-6">
                <h2 class="text-5xl drop-shadow-md pb-4 text-center lg:text-left">Tarifs</h2>
                <div class="flex-grow bg-red-500">
                    <table class="table-auto border-collapse w-full h-full bg-white">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border">Service / Taille</th>
                                <th v-for="taille in tailles" :key="taille.id" class="px-4 py-2 border">{{ taille.nom }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="service in services" :key="service.id">
                                <td class="px-4 py-2 border">{{ service.nom }}</td>
                                <td v-for="taille in tailles" :key="taille.id" class="px-4 py-2 border text-center">
                                    {{ tableau[service.id].tarifs[taille.id] !== null ?
                                        tableau[service.id].tarifs[taille.id] + '€' : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
