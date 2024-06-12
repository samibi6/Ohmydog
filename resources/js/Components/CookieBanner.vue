<script setup>
import { ref, onMounted } from 'vue';

const showBanner = ref(false);

const checkCookie = () => {
    if (!localStorage.getItem('cookiesAccepted')) {
        showBanner.value = true;
    }
};

const acceptCookies = () => {
    localStorage.setItem('cookiesAccepted', 'true');
    showBanner.value = false;
};

onMounted(() => {
    checkCookie();
});
</script>
<template>
    <div v-if="showBanner"
        class="fixed bottom-0 left-0 w-screen bg-gray-800 text-white p-4 text-center flex flex-wrap items-center justify-center z-[100]">
        <p class="m-2">Ce site utilise des cookies uniquement pour le bon fonctionnement du site. En continuant, vous
            acceptez
            l'utilisation des cookies.</p>
        <button @click="acceptCookies"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-800 transition m-2">Accepter</button>
    </div>
</template>
