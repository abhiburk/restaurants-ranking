<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderFullWidthLayout.vue';
import type { BreadcrumbItem } from '@/types';
import 'vue-sonner/style.css'
import TestingStrip from '@/components/TestingStrip.vue';

const isEnvLocal = import.meta.env.VITE_APP_ENV === 'local';

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
    <TestingStrip v-if="isEnvLocal" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
             <slot />
         </div>
    </AppLayout>
</template>
