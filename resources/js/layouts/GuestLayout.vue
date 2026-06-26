<script setup lang="ts">
import 'vue-sonner/style.css';
import { Toaster } from 'vue-sonner';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Footer from './Footer.vue';
import Header from './Header.vue';
defineProps({
    class: String,
});

const page = usePage();
const appName = computed(() => page.props.name);

</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-stone-50 p-6 dark:bg-black">
        <div class="w-full max-w-2xl">
            <Header />
            <slot />
            <Footer />
        </div>
        <Toaster />
    </div>
</template>
