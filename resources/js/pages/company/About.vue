<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>

    <Head title="About" />

    <AppLayout>
        <section class="space-y-8">
            <div>
                <p class="section-title">
                    About
                </p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight">
                    FoodRank
                </h1>
                <p class="mt-4 max-w-2xl text-muted-foreground">
                    FoodRank helps people discover the most loved restaurants in their city through real customer visits
                    and community voting.
                </p>
            </div>
            <div class="rounded-3xl border border-border bg-card p-6">
                <h2 class="text-xl font-semibold">
                    Our Mission
                </h2>
                <p class="mt-3 leading-relaxed text-muted-foreground">
                    We believe great restaurants deserve recognition based on real customer experiences, not fake
                    reviews or paid promotions. FoodRank creates transparent rankings powered by local communities.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-3xl border border-border bg-card p-5">
                    <h3 class="font-semibold">
                        Discover
                    </h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Find trending restaurants near you.
                    </p>
                </div>
                <div class="rounded-3xl border border-border bg-card p-5">
                    <h3 class="font-semibold">
                        Vote
                    </h3>

                    <p class="mt-2 text-sm text-muted-foreground">
                        Support your favorite local places.
                    </p>
                </div>

                <div class="rounded-3xl border border-border bg-card p-5">
                    <h3 class="font-semibold">
                        Rank
                    </h3>

                    <p class="mt-2 text-sm text-muted-foreground">
                        See how restaurants perform in real time.
                    </p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
