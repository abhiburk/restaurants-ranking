<script setup>
import CityController from '@/actions/App/Http/Controllers/CityController';
import CitiesPills from '@/components/city/CitiesPills.vue';
import CityItem from '@/components/city/CityItem.vue';
import RestaurantBanner from '@/components/restaurant/RestaurantBanner.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';
import NewRestaurantItems from '@/components/restaurant/NewRestaurantItems.vue';
import { Card, CardContent } from '@/components/ui/card';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import DiscoverController from '@/actions/App/Http/Controllers/DiscoverController';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Search } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';

const page = usePage();
const appName = computed(() => page.props.name);
defineProps({
    totalActiveCities: Number,
    totalActiveRestaurants: Number,
    totalVotesToday: Number,
    activeCities: Array,
    comingSoonCities: Array,
    mostActiveRestaurant: Object,
    recentlyAddedRestaurants: Array
});
</script>

<template>

    <Head :title="`Discover · ${appName}`" />

    <AppLayout fullMain>
        <template #hero>
            <Card class="relative overflow-hidden">
                <CardContent class="py-4">
                    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1600"
                        class="absolute inset-0 h-full w-full object-cover opacity-40" />

                    <!-- Gradient: Minimal Glass -->
                    <div class="absolute inset-0 bg-primary/50 backdrop-blur-xs" />

                    <div class="relative space-y-6">
                        <Badge
                            class="px-3 py-1 text-xs font-medium tracking-widest uppercase bg-white/20 text-primary-foreground border-white/20 backdrop-blur-sm">
                            Community Powered
                        </Badge>
                        <h1 class="max-w-3xl text-4xl font-bold leading-tight tracking-tight md:text-5xl text-primary-foreground">
                            Discover the city's finest,
                            <span class="text-secondary-foreground">
                                ranked by the people
                            </span>
                            who eat there.
                        </h1>
                        <p class="max-w-2xl text-lg leading-relaxed text-primary-foreground/50">
                            Discover authentic restaurants, explore hidden gems,
                            and help your local favourites climb the rankings.
                        </p>
                        <div class="max-w-xl">
                            <Form action="/explore" method="GET">
                                <InputGroup
                                    class="p-2 py-6 border-0 focus-within:ring-0! rounded-2xl bg-card! text-card-foreground">
                                    <InputGroupInput class="border-0 hover:border-0" placeholder="Explore restaurants in your city" name="search" autocomplete="off" />
                                    <InputGroupAddon>
                                        <Search />
                                    </InputGroupAddon>
                                    <InputGroupAddon align="inline-end">
                                        <Button type="submit">
                                            Search
                                        </Button>
                                    </InputGroupAddon>
                                </InputGroup>
                            </Form>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
        <div class="grid gap-6">
            <!-- STATS -->
            <section>
                <Card class="bg-secondary ">
                    <CardContent class="grid grid-cols-3">
                        <Link :href="CityController.index()">
                            <div class="border-r border-primary/10 text-center">
                                <p class="sm:text-3xl text-2xl font-semibold text-foreground">
                                <div class="flex items-center justify-center">
                                    <Deferred data="totalActiveCities" watch>
                                        <template #fallback>
                                            <Skeleton class="w-8 h-9 rounded-md" />
                                        </template>
                                        {{ totalActiveCities }}
                                    </Deferred>
                                </div>
                                </p>
                                <p class="mt-0.5 sm:text-xs text-[10px] text-muted-foreground uppercase">cities</p>
                            </div>
                        </Link>
                        <div class="border-r border-primary/10 text-center">
                            <p class=" sm:text-3xl text-2xl font-semibold text-foreground">
                            <div class="flex items-center justify-center">
                                <Deferred data="totalActiveRestaurants">
                                    <template #fallback>
                                        <Skeleton class="w-8 h-9 rounded-md" />
                                    </template>
                                    {{ totalActiveRestaurants }}
                                </Deferred>
                            </div>
                            </p>
                            <p class="mt-0.5 sm:text-xs text-[10px] text-muted-foreground uppercase">restaurants</p>
                        </div>
                        <div class="text-center">
                            <p class=" sm:text-3xl text-2xl font-semibold text-foreground">
                            <div class="flex items-center justify-center">
                                <Deferred data="totalVotesToday">
                                    <template #fallback>
                                        <Skeleton class="w-8 h-9 rounded-md" />
                                    </template>
                                    {{ totalVotesToday.toLocaleString() }}
                                </Deferred>
                            </div>
                            </p>
                            <p class="mt-0.5 sm:text-xs text-[10px] text-muted-foreground uppercase">votes today</p>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <!-- MOST ACTIVE RESTAURANT OF THE DAY -->
            <section>
                <p
                    class="mb-3 flex items-center justify-between text-xs font-medium tracking-widest text-muted-foreground uppercase">
                    <span>Most Active Restaurant</span>
                </p>
                <Deferred :data="['mostActiveRestaurant']">
                    <template #fallback>
                        <div class="flex flex-wrap gap-1">
                            <Skeleton class="w-full h-70 " />
                        </div>
                    </template>
                    <RestaurantBanner :restaurant="mostActiveRestaurant" />
                </Deferred>
            </section>

            <!-- POPULAR CITIES -->
            <section>
                <p
                    class="mb-3 flex items-center justify-between text-xs font-medium tracking-widest text-muted-foreground uppercase">
                    <span>Popular cities</span>
                    <Link :href="CityController.index()" class="flex hover:underline">
                        View All
                    </Link>
                </p>
                <div>
                    <CityItem :cities="activeCities" />
                </div>
            </section>
        </div>

        <template #sidebar>
            <!-- RECENTLY ADDED RESTAURANTS -->
            <NewRestaurantItems :recentlyAddedRestaurants="recentlyAddedRestaurants" />

            <!-- COMING SOON CITIES -->
            <CitiesPills :cities="comingSoonCities" deferred-data="comingSoonCities" />
        </template>
    </AppLayout>
</template>
