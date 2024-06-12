<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-pink-100/75">
        <div class="sm:mt-[-100px]">
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-[-20px] px-6 py-4 bg-white/75 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
