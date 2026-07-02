<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import 'vue-sonner/style.css'
import { Toaster } from 'vue-sonner';
import Footer from './Footer.vue';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
    sidebar?: boolean;
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    sidebar: () => true,
    fullMain: () => false,
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <!-- Full Width Hero -->
         <div class="mx-auto flex w-full flex-col px-4 py-4 md:max-w-6xl" v-if="fullMain">
             <slot name="hero" />
         </div>
        <!-- min-h-screen -->
        <div class="mx-auto flex  w-full flex-col px-4 py-4 md:max-w-6xl xl:flex-row">
            <!-- Main Content -->
            <main class="min-w-0 flex-1" :class="!sidebar ? '' : 'xl:pr-6' ">
                <slot />
            </main>

            <!-- Sidebar -->
            <aside v-if="sidebar" class="mt-6 w-full shrink-0 xl:mt-0 xl:w-100">
                <div class="sticky top-6 space-y-5 ">
                    <slot name="sidebar" />
                    <Footer />
                </div>
            </aside>
            <Toaster position="top-center" />
        </div>
    </AppLayout>
</template>
