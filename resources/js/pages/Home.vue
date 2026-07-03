<script setup>

import { Deferred, Form, Head, usePage, usePoll } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Search } from 'lucide-vue-next';
import AppFullWidthLayout from '@/layouts/AppFullWidthLayout.vue';
import NumberFlow from '@number-flow/vue'
import Timer from '@/components/Timer.vue';

const page = usePage();
const appName = computed(() => page.props.name);
const props = defineProps({
    totalVotesToday: {
        type: Number,
        default: 0
    }
});

// 10 seconds polling to update the totalVotesToday count
usePoll(1000 * 10, {
    onStart() { console.log('Total Votes Today Polling started') },
    onFinish() { console.log('Total Votes Today Polling finished') },
    onError(errors) { console.error(errors) },
    only: ['totalVotesToday']
})
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
                            Live ·
                            <Deferred data="['totalVotesToday']">
                                <template #fallback>
                                    <NumberFlow :format="{ notation: 'compact' }" :value="totalVotesToday" />
                                </template>
                            </Deferred>
                            votes today
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
                <!-- Timer Section -->
                <div class="mt-6 flex items-center justify-center gap-2 text-sm text-muted-foreground">
                    <span>🔄 Rankings reset in</span>
                    <span class="font-mono font-semibold text-foreground tabular-nums" id="countdownTimer">
                        <Timer />
                    </span>
                </div>
            </div>
        </section>
    </AppFullWidthLayout>
</template>
