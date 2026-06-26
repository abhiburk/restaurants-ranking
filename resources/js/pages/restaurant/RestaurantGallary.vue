<script setup>
import RestaurantShare from '@/components/restaurant/RestaurantShare.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    restaurant: Object,
});
</script>

<template>
    <AppLayout>
        <div class="mx-auto w-full space-y-4 ">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
                <div v-for="photo in restaurant.media" :key="photo.id" class="group relative overflow-hidden rounded-3xl">
                    <img :src="photo.original_url" :alt="photo.name" class="aspect-4/3 w-full object-cover transition-all duration-500 group-hover:scale-105" />
                </div>
            </div>
        </div>
        <template #sidebar>
            <section>
                <RestaurantShare :restaurant="restaurant" />
            </section>
        </template>
    </AppLayout>
</template>
