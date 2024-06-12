<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps(['users']);
console.log(props.users)
</script>

<template>
    <AppLayout title="Users">
        <template #header>

        </template>
        <div class="py-4 pb-10">
            <div v-for="user in users"
                class="border-2 w-fit mx-auto flex bg-white p-2 space-x-4 mb-4 items-center rounded-xl">
                <h2 class="text-2xl text-center">{{ user.name }}</h2>
                <p class="text-center">{{ user.tel }}</p>
                <p class="text-center">{{ user.email }}</p>
            </div>
        </div>
    </AppLayout>
</template>
