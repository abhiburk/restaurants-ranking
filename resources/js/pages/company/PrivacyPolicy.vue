<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>

    <Head title="Privacy Policy" />

    <AppLayout>
        <section class="space-y-8">

            <div>
                <p class="section-title">
                    Legal
                </p>

                <h1 class="mt-3 text-4xl font-bold tracking-tight">
                    Privacy Policy
                </h1>
            </div>

            <div class="rounded-3xl border border-border bg-card p-6 space-y-6">

                <div>
                    <h2 class="font-semibold">
                        Information We Collect
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        We may collect account information, voting activity, location information and device information
                        necessary to operate FoodRank.
                    </p>
                </div>

                <div>
                    <h2 class="font-semibold">
                        How We Use Information
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        Information is used to provide rankings, prevent abuse and improve platform quality.
                    </p>
                </div>

                <div>
                    <h2 class="font-semibold">
                        Contact
                    </h2>

                    <p class="mt-2 text-muted-foreground">
                        Contact us if you have any questions regarding your data.
                    </p>
                </div>

            </div>

        </section>
    </AppLayout>
</template>
