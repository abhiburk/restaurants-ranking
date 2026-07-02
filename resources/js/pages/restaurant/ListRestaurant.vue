<script setup>
import { Deferred, Head, usePage } from '@inertiajs/vue3';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import RestaurantItems from '@/components/restaurant/RestaurantItems.vue';
import CityBanner from '@/components/city/CityBanner.vue';
import SearchInput from '@/components/SearchInput.vue';
import { computed } from 'vue';
import CityController from '@/actions/App/Http/Controllers/CityController';
import AppLayout from '@/layouts/AppLayout.vue';
import CitiesPills from '@/components/city/CitiesPills.vue';
import FilterPills from '@/components/restaurant/FilterPills.vue';
import { Skeleton } from '@/components/ui/skeleton';

defineProps({
    restaurants: Object,
    city: Object,
    restaurantCategories: Array,
    allTimeVotesToday: String,
    waitlistCount: Number,
    cities: Array,
});

const page = usePage();
const filters = computed(() => page.props.filters);
</script>

<template>

    <Head :title="`Top Restaurants in ${city?.name}`" />
    <AppLayout>
        <div class="fade-3 grid gap-4">
            <Deferred :data="['allTimeVotesToday', 'waitlistCount']">
                <template #fallback>
                    <div class="flex flex-wrap gap-1">
                        <Skeleton class="w-full h-70 " />
                    </div>
                </template>
                <CityBanner :city="city" :allTimeVotesToday="allTimeVotesToday" :waitlistCount="waitlistCount" />
            </Deferred>

            <!-- Restaurants Section -->
            <div v-if="city?.is_live" class="fade-4 space-y-4">
                <FilterPills :items="restaurantCategories" :selected="filters.category"
                    :href="RestaurantController.index(city.slug)" :only="['restaurants', 'filters']" />

                <!-- Search -->
                <div>
                    <SearchInput :route="RestaurantController.index(city.slug).url" v-model="filters.search"
                        :extra-params="{ ...filters }" class="w-full text-sm outline-none "
                        placeholder="Search restaurants" :options="{ only: ['restaurants', 'filters'] }" />
                </div>
                <RestaurantItems :restaurants="restaurants" />
            </div>
        </div>
        <template #sidebar>
            <CitiesPills :cities="cities" title="Popular Cities" :url="CityController.index()" />
        </template>
    </AppLayout>
</template>
