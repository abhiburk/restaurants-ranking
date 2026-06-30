<script setup>
import CityController from '@/actions/App/Http/Controllers/CityController';
import CitiesPills from '@/components/city/CitiesPills.vue';
import CityItem from '@/components/city/CityItem.vue';
import RestaurantBanner from '@/components/restaurant/RestaurantBanner.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';
import NewRestaurantItems from '@/components/restaurant/NewRestaurantItems.vue';
import { Card, CardContent } from '@/components/ui/card';

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
    <AppLayout>
        <div class="grid gap-6">
            <!-- STATS -->
            <Card class="bg-secondary ">
                <CardContent class="grid grid-cols-3">
                    <Link :href="CityController.index()">
                        <div class="border-r border-primary/10 text-center">
                            <p class="font-display sm:text-3xl text-2xl font-semibold text-foreground">
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
                        <p class="font-display sm:text-3xl text-2xl font-semibold text-foreground">
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
                        <p class="font-display sm:text-3xl text-2xl font-semibold text-foreground">
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

            <!-- MOST ACTIVE RESTAURANT OF THE DAY -->
            <div>
                <p
                    class="mb-3 flex items-center justify-between text-xs font-medium tracking-widest text-muted-foreground uppercase">
                    <span>Most Active Restaurant</span>
                </p>
                <RestaurantBanner :restaurant="mostActiveRestaurant" />
            </div>

            <!-- POPULAR CITIES -->
            <div>
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
            </div>
        </div>

        <template #sidebar>
            <!-- RECENTLY ADDED RESTAURANTS -->
            <NewRestaurantItems :recentlyAddedRestaurants="recentlyAddedRestaurants" />

            <!-- COMING SOON CITIES -->
            <CitiesPills :cities="comingSoonCities" />
        </template>
    </AppLayout>
</template>
