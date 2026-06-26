<script setup>
import CityController from '@/actions/App/Http/Controllers/CityController';
import CitiesPills from '@/components/city/CitiesPills.vue';
import CityItem from '@/components/city/CityItem.vue';
import SearchInput from '@/components/SearchInput.vue';
import Pagination from '@/components/table/Pagination.vue';
import { Card, CardContent } from '@/components/ui/card';
import { InputGroup, InputGroupAddon } from '@/components/ui/input-group';
import AppLayout from '@/layouts/AppLayout.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Search, SearchIcon } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps({
    cities: Array,
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
            <div>
                <InputGroup class="w-full border-secondary bg-card text-card-foreground py-5 rounded-2xl border">
                    <SearchInput :route="CityController.index().url" v-model="filters.search"
                        class="w-full text-sm outline-none " placeholder="Search cities" />
                    <InputGroupAddon>
                        <SearchIcon />
                    </InputGroupAddon>
                </InputGroup>
            </div>

            <CityItem :cities="cities" />

            <Pagination :links="cities.links" />
        </div>

        <template #sidebar>
            <!-- COMING SOON CITIES -->
            <CitiesPills :cities="comingSoonCities" title="Coming Soon" :url="CityController.comingSoonCities()" />
        </template>
    </AppLayout>
</template>
