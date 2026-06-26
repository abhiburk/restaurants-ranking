<script setup>
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import { ArrowUpRightIcon, SearchIcon } from 'lucide-vue-next';
import RestaurantItems from '@/components/restaurant/RestaurantItems.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import CityBanner from '@/components/city/CityBanner.vue';
import { InputGroup, InputGroupAddon } from '@/components/ui/input-group';
import SearchInput from '@/components/SearchInput.vue';
import { computed } from 'vue';
import Pagination from '@/components/table/Pagination.vue';
import CityController from '@/actions/App/Http/Controllers/CityController';
import TodaysGrowthPercentage from '@/components/custom/TodaysGrowthPercentage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import CitiesPills from '@/components/city/CitiesPills.vue';

defineProps({
    restaurants: Array,
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
    <Head :title="`Top Restaurants in ${city.name}`" />
    <AppLayout>
        <div>
            <CityBanner :city="city" :allTimeVotesToday="allTimeVotesToday" :waitlistCount="waitlistCount" />

            <!-- Restaurants Section -->
            <div v-if="city.is_live">
                <div class="fade-3 my-4 flex flex-wrap gap-2">
                    <Link :href="RestaurantController.index(city.slug)" preserve-state
                        :class="filters.category ?? 'bg-primary replace text-primary-foreground'"
                        class="rounded-full border px-4 py-1.5 text-sm font-medium bg-card text-card-foreground">
                        All
                    </Link>
                    <Link :href="RestaurantController.index(city.slug)" :data="{ category: category.slug }"
                        preserve-state :class="filters.category === category.slug
                            ? 'replace bg-primary text-primary-foreground'
                            : 'bg-card text-card-foreground'
                            "
                        class="rounded-full border  px-4 py-1.5 text-sm font-medium transition-colors"
                        v-for="category in restaurantCategories" :key="category.id">
                        {{ category.name }}
                    </Link>
                </div>

                <!-- Search -->
                <div class="my-6">
                    <InputGroup class="w-full py-5 rounded-2xl border bg-card text-card-foreground">
                        <SearchInput :route="RestaurantController.index(city.slug).url" v-model="filters.search"
                            :extra-params="{ ...filters }" class="w-full text-sm outline-none "
                            placeholder="Search restaurants" />
                        <InputGroupAddon>
                            <SearchIcon />
                        </InputGroupAddon>
                    </InputGroup>
                </div>

                <div class="fade-4 space-y-2.5">
                    <RestaurantItems :restaurants="restaurants" :citySlug="city.slug" />

                    <Pagination :links="restaurants.links" />
                </div>
            </div>
        </div>
        <template #sidebar>
            <CitiesPills :cities="cities" title="Popular Cities" :url="CityController.index()" />
        </template>
    </AppLayout>
</template>
