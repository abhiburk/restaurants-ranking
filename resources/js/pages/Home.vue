<script setup>

import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Search } from 'lucide-vue-next';
import AppFullWidthLayout from '@/layouts/AppFullWidthLayout.vue';

const page = usePage();
const appName = computed(() => page.props.name);
defineProps({

});
</script>

<template>

    <Head :title="`Home · ${appName}`" />
    <AppFullWidthLayout>
        <section
            class="flex h-[calc(100vh-65px)] w-full  justify-center bg-radial from-blue-50/70 via-blue-50/30 to-background px-4 dark:from-blue-950/30 dark:via-blue-950/10 dark:to-background">
            <div class="w-full max-w-2xl text-center mt-40">
                <!-- Rest of your content -->
                <div class="space-y-4">
                    <div class="flex items-center justify-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-md font-medium text-green-600 dark:text-green-400">
                            Live · 2,841 votes today
                        </span>
                    </div>
                    <h1
                        class=" text-3xl font-bold tracking-wider text-foreground md:text-5xl md:leading-tight font-figtree ">
                        Find Restaurants
                    </h1>
                    <p class="text-md leading-relaxed text-muted-foreground max-w-2xl mx-auto">
                        Search authentic restaurants & explore hidden gems in your city.
                    </p>
                </div>
                <div class="mt-6 w-full">
                    <Form action="/explore" method="GET">
                        <InputGroup class="rounded-2xl px-4 py-7 border border-border bg-card shadow-lg">
                            <InputGroupInput class="border-0" placeholder="Explore restaurants in your city"
                                name="search" autocomplete="off" />
                            <InputGroupAddon>
                                <Search />
                            </InputGroupAddon>
                            <InputGroupAddon align="inline-end">
                                <Button type="submit" class="bg-primary hover:bg-primary/90 text-primary-foreground">
                                    Search
                                </Button>
                            </InputGroupAddon>
                        </InputGroup>
                    </Form>
                </div>
                <!-- Trust Badges -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-xs text-muted-foreground">
                    <span class="flex items-center gap-1">✅ Real votes</span>
                    <span class="flex items-center gap-1">🔒 Anonymous</span>
                    <span class="flex items-center gap-1">🌙 Resets daily</span>
                </div>
            </div>
        </section>
    </AppFullWidthLayout>
</template>
