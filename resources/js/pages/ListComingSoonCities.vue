<script setup>
import CityController from '@/actions/App/Http/Controllers/CityController';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import CityBanner from '@/components/city/CityBanner.vue';
import SearchInput from '@/components/SearchInput.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Empty, EmptyDescription, EmptyHeader, EmptyTitle } from '@/components/ui/empty';
import { InputGroup, InputGroupAddon } from '@/components/ui/input-group';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { SearchIcon } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps({
    comingSoonCities: Array
});

const page = usePage();
const appName = computed(() => page.props.name);
const filters = computed(() => page.props.filters);
</script>

<template>

    <Head :title="`Discover · ${appName}`" />
    <AppLayout>
        <section>
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] font-semibold text-muted-foreground">
                        COMING SOON
                    </p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-foreground">
                        Your city could be next.
                    </h2>
                </div>
                <!-- <div
                    class="hidden sm:flex items-center rounded-full bg-secondary px-4 py-2 text-sm font-semibold text-secondary-foreground">
                    24 cities requested
                </div> -->
            </div>

            <!-- Description -->
            <p class="max-w-130 text-sm leading-relaxed text-muted-foreground">
                Join the waitlist, discover upcoming launches or request your own city.
            </p>

            <!-- Search -->
            <div class="mt-6">
                <SearchInput :route="CityController.comingSoonCities().url" v-model="filters.search" class="w-full text-sm outline-none " placeholder="Search cities" />
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <div v-for="city in comingSoonCities" :key="city.id" :href="RestaurantController.index(city.slug)"
                    class="group w-full overflow-hidden rounded-2xl border border-border bg-card text-left transition-all hover:shadow-sm">
                    <CityBanner :city="city" />
                </div>

                <Empty v-if="!comingSoonCities.length">
                    <EmptyHeader>
                        <EmptyTitle>No cities found</EmptyTitle>
                        <EmptyDescription>
                            We couldn't find any cities matching your search.
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>
            </div>
        </section>
        <template #sidebar>
            <!-- Bottom CTA -->
            <Card>
                <CardContent>
                    <div class="flex flex-col gap-4 ">
                        <div>
                            <h3 class="text-lg font-bold tracking-tight">
                                Don’t see your city?
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                Request a city and help us decide where {{ appName }} launches next.
                            </p>
                        </div>
                        <Button disabled>
                            Comming Soon
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </template>
    </AppLayout>
</template>
