<script setup>
import { Deferred, Link } from '@inertiajs/vue3';
import { Skeleton } from '../ui/skeleton';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    selected: {
        type: String,
        default: null,
    },
    href: {
        type: String,
        required: true,
    },
    param: {
        type: String,
        default: 'category',
    },
    preserveState: {
        type: Boolean,
        default: true,
    },
    only: {
        type: Array,
        default: () => [],
    },
});

const chipClass = (active) => [
    'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
    active
        ? 'bg-primary text-primary-foreground'
        : 'bg-card text-card-foreground hover:bg-accent',
];
</script>

<template>
    <div class="fade-3 my-4 flex flex-wrap gap-2">
        <Deferred :data="['restaurantCategories']">
            <template #fallback>
                <div class="flex flex-wrap gap-2">
                    <Skeleton v-for="i in 5" class="w-22 h-8 rounded-3xl px-3 py-2" />
                </div>
            </template>

            <Link :href="href" :preserve-state="preserveState" :class="chipClass(!selected)" :only="only">
                All
            </Link>

            <Link v-for="item in items" :key="item.id" :href="href" :data="{ [param]: item.slug }" :only="only"
                :preserve-state="preserveState" :class="chipClass(selected === item.slug)">
                {{ item.name }}
            </Link>
        </Deferred>
    </div>
</template>