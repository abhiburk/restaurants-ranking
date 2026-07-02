<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>
    <div class="flex items-center">
        <div class="">
            <AppLogoIcon class="size-6 fill-current text-white dark:text-black" />
        </div>
        <div class="ml-2 grid flex-1 text-left text-sm">
            <span class="truncate leading-tight font-bold brand-text text-lg lowercase font-comfortaa">{{ appName }}</span>
        </div>
    </div>
</template>
