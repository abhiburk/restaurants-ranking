<script setup>
import CityController from '@/actions/App/Http/Controllers/CityController';
import CitiesPills from '@/components/city/CitiesPills.vue';
import CityItem from '@/components/city/CityItem.vue';
import RestaurantBanner from '@/components/restaurant/RestaurantBanner.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Deferred, Form, Head, Link, usePage, usePoll } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';
import NewRestaurantItems from '@/components/restaurant/NewRestaurantItems.vue';
import { Card, CardContent } from '@/components/ui/card';
import NumberFlow from '@number-flow/vue'

const page = usePage();
const appName = computed(() => page.props.name);
defineProps({
    totalActiveCities: Number,
    totalActiveRestaurants: Number,
    totalVotesToday: {
        type: Number,
        default: 0
    },
    activeCities: Array,
    comingSoonCities: Array,
    mostActiveRestaurant: Object,
    recentlyAddedRestaurants: Array
});

usePoll(1000 * 10, {
    onStart() { console.log('Total Votes Today Polling started') },
    onFinish() { console.log('Total Votes Today Polling finished') },
    onError(errors) { console.error(errors) },
    only: ['totalVotesToday']
})
</script>

<template>

    <Head :title="`Discover · ${appName}`" />

    <AppLayout>
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
                                    <NumberFlow :format="{ notation: 'compact' }" :value="totalVotesToday" />
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
