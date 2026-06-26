<script setup>
import ManageRestaurantController from '@/actions/App/Http/Controllers/Restaurant/ManageRestaurantController';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    restaurant: {
        type: Object,
        required: false,
    },
});

const page = usePage();

const isActiveTab = (href) => {
    return page.url == href;
};

const profileTabs = [
    {
        name: 'Overview',
        href: ManageRestaurantController.show(page.props.restaurant.id).url,
    },
    {
        name: 'Edit Profile',
        href: ManageRestaurantController.edit(page.props.restaurant.id).url,
    },
];
</script>

<template>
    <div class="mx-auto w-full max-w-7xl px-4 py-6">
        <div class="mb-6 border-b">
            <nav class="flex space-x-4" aria-label="Tabs">
                <Link
                    v-for="tab in profileTabs"
                    :key="tab.name"
                    :href="tab.href"
                    :class="[
                        '-mb-px border-b-2 px-3 py-2 text-sm font-medium transition-colors',
                        isActiveTab(tab.href)
                            ? 'border-primary text-primary'
                            : 'border-transparent text-muted-foreground hover:border-muted-foreground/50 hover:text-foreground',
                    ]"
                >
                    {{ tab.name }}
                </Link>
            </nav>
        </div>

        <div>
            <slot></slot>
        </div>
    </div>
</template>


