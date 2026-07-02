<script setup lang="ts">
import { Deferred, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Item, ItemActions, ItemContent, ItemDescription, ItemMedia, ItemTitle } from '../ui/item';
import RestaurantAvatar from './RestaurantAvatar.vue';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import moment from 'moment';
import { Skeleton } from '../ui/skeleton';

defineProps({
    recentlyAddedRestaurants: Array
})
</script>

<template>
    <Deferred data="recentlyAddedRestaurants">
        <template #fallback>
            <Skeleton class="w-35 h-4" />
            <div class="flex flex-wrap gap-2">
                <Skeleton v-for="i in 5" class="w-full h-16" />
            </div>
        </template>
        <Card v-if="recentlyAddedRestaurants?.length" class="bg-transparent shadow-none border-0 p-0 gap-2">
            <CardHeader class="p-0">
                <CardTitle class="text-xs tracking-widest font-medium text-muted-foreground uppercase flex items-center justify-between">
                    New Restaurants
                    <!-- <Link :href="DiscoverController.explore()" class="flex hover:underline">
                        View All
                    </Link> -->
                </CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col gap-2 p-0">
                <Item v-for="restaurant in recentlyAddedRestaurants" :key="restaurant.id" variant="outline" size="sm"
                    as-child class="rounded-2xl bg-card text-card-foreground">
                    <Link :href="RestaurantController.show([restaurant.city.slug, restaurant.slug])">
                        <ItemMedia>
                            <RestaurantAvatar :restaurant="restaurant" />
                        </ItemMedia>
                        <ItemContent>
                            <ItemTitle class="line-clamp-1">{{ restaurant.name }}</ItemTitle>
                            <ItemDescription class="line-clamp-1 text-xs">{{ restaurant.city.name }}</ItemDescription>
                        </ItemContent>
                        <ItemActions>
                            <small>{{ moment(restaurant.created_at).fromNow() }}</small>
                        </ItemActions>
                    </Link>
                </Item>
            </CardContent>
        </Card>
    </Deferred>
</template>