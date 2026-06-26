<script setup lang="ts">
import { Badge, BadgeCheck, Eye, EyeIcon } from 'lucide-vue-next';
import TodaysGrowthPercentage from '../custom/TodaysGrowthPercentage.vue';
import Card from '../ui/card/Card.vue';
import { CardContent } from '../ui/card/index.js';

defineProps({
    restaurant: Object,
})
</script>

<template>
    <Card class="overflow-hidden pt-0">
        <div class="relative h-40 w-full overflow-hidden md:h-48">
            <img :src="restaurant.banner_url" :alt="restaurant.name"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
            <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/20 to-transparent"></div>

            <div class="absolute top-4 right-4 flex items-center gap-2">
                <div v-if="restaurant.views > 0"
                    class="flex items-center gap-1.5 rounded-full bg-primary/50 text-primary-foreground px-3 py-1 backdrop-blur-sm">
                    <EyeIcon class="h-3 w-3" />
                    <span class="text-xs font-medium ">{{ restaurant.views + 1 }}</span>
                </div>
                <div
                    class="flex items-center gap-1.5 rounded-full bg-primary/50 text-primary-foreground px-3 py-1 backdrop-blur-sm">
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></div>
                    <span class="text-xs font-medium ">LIVE</span>
                </div>
            </div>

            <div class="absolute bottom-4 left-6 text-white flex items-center gap-2 ">
                <!-- <RestaurantAvatar :restaurant="restaurant" /> -->
                <div>
                    <h1 class="text-lg font-bold md:text-2xl flex items-center gap-2">
                        {{ restaurant.name }} 
                        <BadgeCheck class="h-5 w-5" fill="green" v-if="restaurant.user_id"/>
                    </h1>
                    <p class="mt-0.5 text-sm ">
                        {{ restaurant.address }}
                    </p>
                </div>
            </div>
        </div>

        <CardContent>
            <!-- Stats Section -->
            <div class="flex items-center justify-between gap-6 text-dark-foreground">
                <div>
                    <p class="text-[10px] tracking-wide  uppercase truncate">
                        {{ restaurant.city.state?.name ?? 'NA' }}
                    </p>
                    <p class="font-display sm:text-2xl text-sm font-bold truncate">
                        {{ restaurant.city.name ?? 'NA' }}
                    </p>
                </div>
                <!-- <div class="h-6 w-px bg-primary/10"></div> -->
                <div>
                    <p class="text-[10px] tracking-wide uppercase truncate">
                        <strong>{{ restaurant.votes_today.toLocaleString() }}</strong> votes today
                    </p>
                    <p class="font-display sm:text-2xl text-sm font-bold">
                        <TodaysGrowthPercentage :growth_percentage="restaurant.growth_percentage" />
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>