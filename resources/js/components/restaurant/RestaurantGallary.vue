<script setup>
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import { Link } from '@inertiajs/vue3';

defineProps({
    restaurant: Object,
});
</script>

<template>
    <section>
        <!-- overflow-x-hidden hover:overflow-x-auto -->
        <div class="scrollbar-none flex snap-x snap-mandatory gap-3 pb-2 overflow-x-auto dark:scheme-dark">
            <template v-for="(photo, index) in restaurant.media ?? []" :key="index">
                <div class="group relative shrink-0 overflow-hidden rounded-3xl">
                    <img :src="photo.original_url" :alt="photo.name" class="h-40 w-56 rounded-3xl object-cover transition-all duration-500 group-hover:scale-[1.03]" />
                    
                    <Link :href="RestaurantController.photos([restaurant.city.slug, restaurant.slug])" v-if="index === restaurant.media.length - 1"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-black/45 backdrop-blur-[2px]">
                        <span class="text-3xl font-bold text-white"> + </span>
                        <span class="mt-1 text-sm text-white/90">
                            More photos
                        </span>
                    </Link>
                </div>
            </template>
        </div>
    </section>
</template>