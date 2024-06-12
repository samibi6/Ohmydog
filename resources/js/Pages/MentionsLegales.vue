<script setup>
import FrontLayout from "@/Layouts/FrontLayout.vue";
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
</script>
<template>
    <FrontLayout title="Accueil" :page="$page" :canLogin="canLogin">
        <div class="pt-40 px-8 md:px-28 pb-32 min-h-screen space-y-5 bg-white/50">
            <h2 class="text-5xl text-center mb-16">Mentions Légales</h2>
            <h3 class="text-2xl">Politique de confidentialité</h3>
            <p>Les données stockées dans ma boîte mail sont protégées par un mot de passe et
                les informations envoyées sont protégées par un encryptage SSL. Vos
                informations seront utilisées uniquement pour répondre à votre demande. Oh
                my dog stocke également des informations dans une base de données pour
                mener à bien le fonctionnement de la réservation en ligne. Les informations
                collectées comprennent la date et l'heure du rendez-vous, le nom, le prénom,
                le numéro de téléphone, l'adresse mail et les informations sur le chien pour assurer la bonne
                prise en compte du rendez-vous.</p>

            <h3 class="text-2xl">Droit d'auteur</h3>
            <p>Le site « oh-my-dog.be » constitue une création protégée par le droit d'auteur.
                Tous les contenus, y compris les textes, exercices, correctifs, photos, et autres
                éléments de ce site sont protégés par le droit d'auteur. Toute copie,
                adaptation, traduction, arrangement, communication au public, location et
                autre exploitation, modification de tout ou partie de ce site sous quelle que
                forme et par quel que moyen que ce soit, électronique, mécanique ou autre,
                réalisée dans un but lucratif, est strictement interdite sans l'autorisation
                préalable. Cependant, le contenu de ce site est destiné à être exploité à
                volonté dans un cadre privé pour les clients de Oh my dog.</p>

            <h3 class="text-2xl">Marques et noms commerciaux</h3>
            <p>Les dénominations, logos et autres signes utilisés sur ce site sont des marques
                et/ou noms commerciaux légalement protégés. Tout usage de ceux-ci ou de
                signes ressemblants est strictement interdit sans un accord préalable et écrit.</p>

            <h3 class="text-2xl">Responsabilité quant au contenu</h3>
            <p>Oh my dog apporte le plus grand soin à la création et à la mise à jour de ce site
                mais ne peut toutefois pas garantir l'exactitude de l'information qui s'y trouve.
                Les informations contenues dans ce site pourront faire l'objet de
                modifications sans préavis. Les informations données sur ce site ne sauraient
                engager la responsabilité de Oh my dog, qui ne pourra être tenue pour
                responsable de toute omission, erreur ou lacune pouvant se glisser dans ses pages ainsi que des
                conséquences, quelle qu'elles soient, pouvant résulter de
                l'utilisation des informations et indications fournies.</p>

            <h3 class="text-2xl">Coordonnées</h3>
            <p>Vous pouvez contacter Oh my dog par email à contact@oh-my-dog.be ou par
                téléphone au 0470 68 14 19.</p>

            <h3 class="text-2xl">Hébergeur</h3>
            <p>O2switch.fr</p>
            <p>Chem. des Pardiaux, 63000 Clermont-Ferrand, France.</p>
            <p>Téléphone: +33 4 44 44 60 40</p>
        </div>
    </FrontLayout>
</template>
