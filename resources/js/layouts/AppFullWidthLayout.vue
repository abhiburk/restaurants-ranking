<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderFullWidthLayout.vue';
import type { BreadcrumbItem } from '@/types';
import 'vue-sonner/style.css'
import { Toaster } from 'vue-sonner';
import Footer from './Footer.vue';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
    sidebar?: boolean;
    fullMain?: boolean;
    fullscreen?: boolean;
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    sidebar: () => true,
    fullMain: () => false,
    fullscreen: () => false,
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
             <slot />
         </div>
    </AppLayout>
</template>
