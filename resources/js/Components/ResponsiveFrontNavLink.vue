<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center p-2 border-b-2 border-white text-xl font-bold leading-5 text-white focus:outline-none focus:border-white transition duration-150 ease-in-out hover:text-white'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xl font-bold leading-5 text-white hover:border-white/75 focus:outline-none focus:text-white/75 focus:border-white/75 transition duration-150 ease-in-out hover:text-white/75';
});
</script>

<template>
    <Link :href="href" :class="classes">
    <slot />
    </Link>
</template>
