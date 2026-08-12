<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';
import 'vue-sonner/style.css';
import { Toaster } from 'vue-sonner';
import TestingStrip from '@/components/TestingStrip.vue';
const isEnvLocal = import.meta.env.VITE_APP_ENV === 'local';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <TestingStrip v-if="isEnvLocal" />
    <AuthLayout :title="title" :description="description">
        <slot />
        <Toaster />
    </AuthLayout>
</template>
