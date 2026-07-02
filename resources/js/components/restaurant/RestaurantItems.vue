<script setup>
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import {
    Item,
    ItemContent,
    ItemDescription,
    ItemGroup,
    ItemTitle,
} from '@/components/ui/item';
import { Deferred, Link } from '@inertiajs/vue3';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyTitle,
} from '../ui/empty';
import Avatar from '../ui/avatar/Avatar.vue';
import AvatarFallback from '../ui/avatar/AvatarFallback.vue';
import TodaysGrowthPercentage from '../custom/TodaysGrowthPercentage.vue';
import { BadgeCheck } from 'lucide-vue-next';
import { Skeleton } from '../ui/skeleton';
import Pagination from '../table/Pagination.vue';

defineProps({
    restaurants: Array,
});
</script>

<template>
   <Deferred :data="['restaurants']">
        <template #fallback>
            <div class="flex flex-wrap gap-4">
                <Skeleton v-for="i in 5" class="w-full h-20" />
            </div>
        </template>
        <ItemGroup class="gap-4">
            <Item v-for="restaurant in restaurants.data" :key="restaurant.id" variant="outline" as-child
                class="rounded-2xl bg-card text-card-foreground">
                <Link :href="RestaurantController.show([restaurant.city.slug, restaurant.slug])
                    ">
                    <!-- <RestaurantAvatar :restaurant="restaurant" /> -->
                    <Avatar class="size-12">
                        <AvatarFallback>{{
                            restaurant.rank
                            }}</AvatarFallback>
                    </Avatar>
                    <ItemContent>
                        <ItemTitle class="line-clamp-1 flex items-center gap-2">
                            {{ restaurant.name }}
                            <BadgeCheck class="sm:h-4 sm:w-4 h-5 w-5" color="green" v-if="restaurant.user_id" />
                            <!-- <TrendingBadge :is_trending="restaurant.is_trending" /> -->
                        </ItemTitle>
                        <ItemDescription class="line-clamp-1">
                            {{ restaurant.address }} &sdot;
                            {{ restaurant.category.name }}
                        </ItemDescription>
                    </ItemContent>
                    <ItemContent class="flex-none text-center">
                        <!-- <ItemDescription> -->
                        <div class="shrink-0 text-right">
                            <p class="font-display text-md font-semibold tabular-nums">
                                {{ restaurant.votes_today.toLocaleString() }}
                            </p>
                            <p class="text-xs">votes today</p>
                            <TodaysGrowthPercentage :growth_percentage="restaurant.growth_percentage" class="text-xs" />
                        </div>
                        <!-- </ItemDescription> -->
                    </ItemContent>
                </Link>
            </Item>

            <Empty v-if="!restaurants?.data?.length">
                <EmptyHeader>
                    <div class="text-5xl">
                        🍽️
                    </div>
                    <EmptyTitle>No restaurants found</EmptyTitle>
                    <EmptyDescription>
                        We couldn't find any restaurants matching your search.
                        Try another keyword or browse nearby restaurants.
                    </EmptyDescription>
                </EmptyHeader>
            </Empty>
        </ItemGroup>
        <Pagination :links="restaurants?.links" :only="['restaurants']" />
    </Deferred>
</template>