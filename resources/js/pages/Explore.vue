<script setup>
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowUpRightIcon, CircleX, SearchIcon } from 'lucide-vue-next';
import RestaurantItems from '@/components/restaurant/RestaurantItems.vue';

import { InputGroup, InputGroupAddon } from '@/components/ui/input-group';
import SearchInput from '@/components/SearchInput.vue';
import { computed } from 'vue';
import Pagination from '@/components/table/Pagination.vue';
import CityController from '@/actions/App/Http/Controllers/CityController';
import AppLayout from '@/layouts/AppLayout.vue';
import CitiesPills from '@/components/city/CitiesPills.vue';
import DiscoverController from '@/actions/App/Http/Controllers/DiscoverController';

defineProps({
    restaurants: Array,
    cities: Array,
    filters: Object
});

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>

    <Head :title="`Explore ${appName}`" />
    <AppLayout>
        <div class="fade-3 grid gap-4">
            <div>
                <SearchInput :route="DiscoverController.explore().url" v-model="filters.search" :extra-params="{ ...filters }" placeholder="Search restaurants" />
            </div>

            <div class="fade-4 space-y-2.5">
                <RestaurantItems :restaurants="restaurants" />

                <Pagination :links="restaurants.links" />
            </div>
        </div>
        <template #sidebar>
            <CitiesPills :cities="cities" title="Popular Cities" :url="CityController.index()" />
        </template>
    </AppLayout>
</template>
