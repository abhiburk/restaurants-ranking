<script setup lang="ts">
import { Badge } from '../ui/badge';
import { Deferred, Link } from '@inertiajs/vue3';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import { Card, CardContent, CardHeader } from '../ui/card';
import CardTitle from '../ui/card/CardTitle.vue';
import { Skeleton } from '../ui/skeleton';

defineProps({
    cities: Array,
    title: String,
    url: String,
    deferredData: {
        type: Array,
        default: () => ['cities'],
    },
});
</script>

<template>
    <Deferred :data="deferredData">
        <template #fallback>
            <div class="flex items-center justify-between gap-2">
                <Skeleton class="w-16 h-3" />
                <Skeleton class="w-16 h-3" />
            </div>
            <div class="flex flex-wrap gap-2">
                <Skeleton v-for="i in 5" class="w-22 h-8 rounded-3xl" />
            </div>
        </template>
        <Card class="bg-transparent shadow-none border-0 p-0 gap-2">
            <CardHeader class="p-0">
                <CardTitle
                    class="flex items-center justify-between text-xs font-medium tracking-widest text-muted-foreground uppercase">
                    <span>{{ title ?? 'Cities' }}</span>
                    <Link :href="url" class="flex hover:underline">
                        View All
                    </Link>
                </CardTitle>
            </CardHeader>

            <CardContent class="flex flex-col gap-2 p-0">
                <div class="flex flex-wrap gap-2 " v-if="cities.length">
                    <Link :href="RestaurantController.index(city.slug)" v-for="city in cities">
                        <Badge variant="secondary" class="px-3 py-2 hover:bg-muted">
                            {{ city.name }}
                        </Badge>
                    </Link>
                </div>
            </CardContent>

        </Card>
    </Deferred>
</template>