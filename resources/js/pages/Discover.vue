<script setup>
import CityController from '@/actions/App/Http/Controllers/CityController';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import CitiesPills from '@/components/city/CitiesPills.vue';
import CityItem from '@/components/city/CityItem.vue';
import RestaurantBanner from '@/components/restaurant/RestaurantBanner.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRightIcon, BadgeCheckIcon, ChevronRightIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item'
import { Button } from '@/components/ui/button';
import RestaurantAvatar from '@/components/restaurant/RestaurantAvatar.vue';
import moment from 'moment';

const page = usePage();
const appName = computed(() => page.props.name);
defineProps({
    totalActiveCities: Number,
    totalActiveRestaurants: Number,
    totalVotesToday: Number,
    activeCities: Array,
    comingSoonCities: Array,
    mostActiveRestaurant: Object,
    recentlyAddedRestaurant: Array
});
</script>

<template>

    <Head :title="`Discover · ${appName}`" />
    <AppLayout>
        <div>
            <!-- STATS -->
            <div class="fade-2 mb-5 grid grid-cols-3 overflow-hidden rounded-2xl border border-secondary bg-secondary">
                <Link :href="CityController.index()">
                    <div class="border-r border-primary/10 px-3 py-3.5 text-center">
                        <p class="font-display sm:text-3xl text-2xl font-semibold text-foreground">
                            {{ totalActiveCities }}
                        </p>
                        <p class="mt-0.5 sm:text-xs text-[10px] text-muted-foreground uppercase">cities</p>
                    </div>
                </Link>
                <div class="border-r border-primary/10 px-3 py-3.5 text-center">
                    <p class="font-display sm:text-3xl text-2xl font-semibold text-foreground">
                        {{ totalActiveRestaurants }}
                    </p>
                    <p class="mt-0.5 sm:text-xs text-[10px] text-muted-foreground uppercase">restaurants</p>
                </div>
                <div class="px-3 py-3.5 text-center">
                    <p class="font-display sm:text-3xl text-2xl font-semibold text-foreground">
                        {{ totalVotesToday }}
                    </p>
                    <p class="mt-0.5 sm:text-xs text-[10px] text-muted-foreground uppercase">votes today</p>
                </div>
            </div>

            <p
                class="mb-2.5 flex items-center justify-between text-xs font-medium tracking-widest text-muted-foreground uppercase">
                <span>Most Active Restaurant</span>
            </p>
            <!-- MOST ACTIVE RESTAURANT OF THE DAY -->
            <Link :href="RestaurantController.show([
                mostActiveRestaurant.city.slug,
                mostActiveRestaurant.slug,
            ])
                ">
                <RestaurantBanner :restaurant="mostActiveRestaurant" />
            </Link>
            <!-- POPULAR CITIES -->
            <div class="fade-4 mt-5" v-if="activeCities.total > 0">
                <p
                    class="mb-2.5 flex items-center justify-between text-xs font-medium tracking-widest text-muted-foreground uppercase">
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
            <Card v-if="recentlyAddedRestaurant.length" class="bg-transparent shadow-none border-0 p-0 gap-2">
                <CardHeader class="p-0">
                    <CardTitle class="text-xs tracking-widest font-medium text-muted-foreground uppercase">New
                        Restaurants
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex  flex-col gap-2 p-0">
                    <Item v-for="restaurant in recentlyAddedRestaurant" :key="restaurant.id" variant="outline" size="sm"
                        as-child class="rounded-2xl bg-card text-card-foreground">
                        <Link :href="RestaurantController.show([restaurant.city.slug, restaurant.slug])">
                            <ItemMedia>
                                <RestaurantAvatar :restaurant="restaurant" />
                            </ItemMedia>
                            <ItemContent>
                                <ItemTitle class="line-clamp-1">{{ restaurant.name }}</ItemTitle>
                                <ItemDescription class="line-clamp-1 text-xs">{{ restaurant.city.name }}
                                </ItemDescription>
                            </ItemContent>
                            <ItemActions>
                                <small>{{ moment(restaurant.created_at).fromNow() }}</small>
                            </ItemActions>
                        </Link>
                    </Item>
                </CardContent>
            </Card>
            <!-- COMING SOON CITIES -->
            <CitiesPills :cities="comingSoonCities" title="Coming Soon" :url="CityController.comingSoonCities()" />
        </template>
    </AppLayout>
</template>
