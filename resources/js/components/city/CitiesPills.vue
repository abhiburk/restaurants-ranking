<script setup lang="ts">
import { Badge } from '../ui/badge';
import { Link } from '@inertiajs/vue3';
import { ArrowRightIcon } from 'lucide-vue-next';
import CityController from '@/actions/App/Http/Controllers/CityController';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import { Card, CardContent, CardHeader } from '../ui/card';
import CardTitle from '../ui/card/CardTitle.vue';

defineProps({
    cities: Object,
    title: String,
    url: String
});
</script>

<template>
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
        <CardContent class="flex  flex-col gap-2 p-0">
            <div class="fade-5" v-if="cities.length">
                <div class="flex flex-wrap gap-2 ">
                    <Link :href="RestaurantController.index(city.slug)" v-for="city in cities">
                        <Badge variant="secondary" class="px-3 py-2 hover:bg-muted">
                            {{ city.name }}
                        </Badge>
                    </Link>
                </div>
            </div>
        </CardContent>
    </Card>
</template>