<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>

    <Head title="Terms of Service" />

    <AppLayout>
        <section class="space-y-8">

            <div>
                <p class="section-title">
                    Legal
                </p>

                <h1 class="mt-3 text-4xl font-bold tracking-tight">
                    Terms of Service
                </h1>
            </div>

            <div class="rounded-3xl border border-border bg-card p-6 space-y-6">

                <div>
                    <h2 class="font-semibold">
                        Community Guidelines
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        Users must not manipulate rankings, submit fraudulent activity or misuse the platform.
                    </p>
                </div>

                <div>
                    <h2 class="font-semibold">
                        Restaurant Listings
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        FoodRank may modify or remove listings that violate platform policies.
                    </p>
                </div>

                <div>
                    <h2 class="font-semibold">
                        Availability
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        We strive to keep FoodRank available but do not guarantee uninterrupted service.
                    </p>
                </div>

            </div>

        </section>
    </AppLayout>
</template>
