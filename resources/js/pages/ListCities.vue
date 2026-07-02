<script setup>
import CityController from '@/actions/App/Http/Controllers/CityController';
import CitiesPills from '@/components/city/CitiesPills.vue';
import CityItem from '@/components/city/CityItem.vue';
import SearchInput from '@/components/SearchInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    activeCities: Array,
    comingSoonCities: Array,
    filters: Object
});

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>

    <Head :title="`Discover · ${appName}`" />
    <AppLayout>
        <div class="fade-3 grid gap-4">
            <SearchInput :route="CityController.index().url" v-model="filters.search"
                class="w-full text-sm outline-none " placeholder="Search cities" :options="{ only: ['activeCities'] }" />
            <CityItem :cities="activeCities" paginate="true" />
        </div>

        <template #sidebar>
            <!-- COMING SOON CITIES -->
            <CitiesPills :cities="comingSoonCities" title="Coming Soon" :url="CityController.comingSoonCities()"
                :deferred-data="['comingSoonCities']" />
        </template>
    </AppLayout>
</template>
