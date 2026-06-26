<script setup>
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import {
    Item,
    ItemContent,
    ItemDescription,
    ItemGroup,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item';
import { Link } from '@inertiajs/vue3';
import { Empty, EmptyDescription, EmptyHeader, EmptyTitle } from '../ui/empty';
import { Avatar, AvatarFallback, AvatarImage } from '../ui/avatar';
import VotesToday from '../restaurant/VotesToday.vue';
import TodaysGrowthPercentage from '../custom/TodaysGrowthPercentage.vue';

defineProps({
    cities: Object,
});

</script>

<template>
    <div>
        <ItemGroup class="gap-4">
            <Item v-for="city in cities.data" :key="city.id" variant="outline" as-child
                class="rounded-2xl bg-card text-card-foreground">
                <Link :href="RestaurantController.index(city.slug)">
                    <Avatar class="size-10">
                        <AvatarImage :src="city.logo_url" :alt="city.name" />
                        <AvatarFallback>
                            {{ city.name.charAt(0) }}
                        </AvatarFallback>
                    </Avatar>
                    <ItemContent>
                        <ItemTitle class="line-clamp-1">
                            {{ city.name }}
                        </ItemTitle>
                        <ItemDescription class="line-clamp-1">
                            {{ city.state.name }} &sdot; {{ city.restaurants_count }} restaurants
                        </ItemDescription>
                    </ItemContent>
                    <ItemContent class="flex-none text-center">
                        <!-- <ItemDescription> -->
                            <div class="shrink-0 text-right">
                                <p class="font-display text-md font-semibold tabular-nums">
                                    {{ city.votes_today.toLocaleString() }} 
                                </p>
                                <p class="text-xs text-muted-foreground">votes today</p>
                                <TodaysGrowthPercentage :growth_percentage="city.growth_percentage" class="text-xs"/>
                            </div>
                        <!-- </ItemDescription> -->
                    </ItemContent>
                </Link>
            </Item>
            <Empty v-if="!cities.data?.length">
                <EmptyHeader>
                    <EmptyTitle>No cities found</EmptyTitle>
                    <EmptyDescription>
                        We couldn't find any cities matching your search.
                    </EmptyDescription>
                </EmptyHeader>
            </Empty>
        </ItemGroup>
    </div>
</template>
