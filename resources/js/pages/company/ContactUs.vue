<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>

    <Head title="Contact Us" />

    <AppLayout>
        <section class="space-y-8">

            <div>
                <p class="section-title">
                    Support
                </p>

                <h1 class="mt-3 text-4xl font-bold tracking-tight">
                    Contact Us
                </h1>

                <p class="mt-4 text-muted-foreground">
                    We'd love to hear from you.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">

                <div class="rounded-3xl border border-border bg-card p-6">
                    <h2 class="font-semibold">
                        General Enquiries
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        hello@foodrank.in
                    </p>
                </div>

                <div class="rounded-3xl border border-border bg-card p-6">
                    <h2 class="font-semibold">
                        Restaurant Owners
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        partners@foodrank.in
                    </p>
                </div>

            </div>

        </section>
    </AppLayout>
</template>
