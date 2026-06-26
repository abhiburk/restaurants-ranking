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
                <InputGroup class="w-full border-secondary bg-card text-card-foreground py-5 rounded-2xl border">
                    <SearchInput :route="CityController.comingSoonCities().url" v-model="filters.search"
                        class="w-full text-sm outline-none " placeholder="Search cities" />
                    <InputGroupAddon>
                        <SearchIcon />
                    </InputGroupAddon>
                </InputGroup>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <Link v-for="city in comingSoonCities" :key="city.id" :href="RestaurantController.index(city.slug)"
                    class="group w-full overflow-hidden rounded-2xl border border-border bg-card text-left transition-all hover:shadow-sm">
                    <CityBanner :city="city" />

                    <div class="p-4">
                        <p class="text-sm leading-relaxed text-muted-foreground">
                            We’re launching in {{ city.name }} soon. Join the waitlist to be the first to know when we
                            go live.
                        </p>
                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2 rounded-full bg-secondary px-3 py-2">
                                <div class="h-2 w-2 rounded-full bg-amber-500"></div>
                                <span class="text-xs font-semibold text-secondary-foreground">
                                    {{ city.city_wishlists_count }} interested
                                </span>
                            </div>
                            <Button size="sm">
                                Notify Me
                            </Button>
                        </div>
                    </div>
                </Link>

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
