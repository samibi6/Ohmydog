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
        <div class="w-screen h-screen flex items-center  justify-center">
            <div class="bg-white/50 rounded-lg p-5">
                <h2 class="mb-5 text-2xl">Veuillez créer un compte ou vous connecter pour accéder à la
                    réservation en ligne</h2>
                <div class="flex space-x-5">
                    <a :href="route('register')"
                        class="bg-green-500 p-3 hover:bg-green-800 transition text-white font-bold rounded-md">Créer un
                        compte</a>
                    <a :href="route('login')"
                        class="bg-blue-500 p-3 hover:bg-blue-800 transition text-white font-bold rounded-md">Se
                        connecter</a>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
