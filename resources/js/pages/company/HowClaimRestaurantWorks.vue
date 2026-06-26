<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>

    <Head title="Claim a Restaurant" />

    <AppLayout>
        <section class="space-y-8">

            <div>
                <p class="section-title">
                    Restaurant Owners
                </p>

                <h1 class="mt-3 text-4xl font-bold tracking-tight">
                    Claim Your Restaurant
                </h1>

                <p class="mt-4 max-w-2xl text-muted-foreground">
                    If your restaurant is already listed on FoodRank, you can request ownership and gain access to
                    restaurant tools and insights.
                </p>
            </div>

            <div class="rounded-3xl border border-border bg-card p-6">
                <h2 class="font-semibold">
                    Benefits
                </h2>

                <ul class="mt-4 space-y-3 text-muted-foreground">
                    <li>• Manage restaurant information</li>
                    <li>• Upload photos and updates</li>
                    <li>• View rankings and voting activity</li>
                    <li>• Download QR codes for customer voting</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-border bg-secondary/40 p-6">
                <p class="text-sm">
                    Claims are manually reviewed to ensure restaurant ownership.
                </p>
            </div>

        </section>
    </AppLayout>
</template>
