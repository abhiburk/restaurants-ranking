<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { router } from '@inertiajs/vue3';
import Pagination from './Pagination.vue';

const props = defineProps<{
    items: {
        from: number | null;
        to: number | null;
        total: number;
        per_page: number;
        links: any[];
    };
    route: any;
}>();

function changePerPage(value: string) {
    router.get(
        props.route.url,
        {
            ...props.route.params,
            per_page: value,
            page: 1,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}
</script>

<template>
    <div class="flex items-center justify-between text-sm">
        <div class="text-muted-foreground">
            <span v-if="items.total">
                Showing {{ items.from }} to {{ items.to }} of {{ items.total }}
            </span>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden items-center gap-2 lg:flex">
                <Label for="rows-per-page" class="text-sm font-medium">
                    Rows per page
                </Label>
                <Select
                    :model-value="String(items.per_page)"
                    @update:model-value="changePerPage"
                >
                    <SelectTrigger id="rows-per-page" size="sm" class="w-20">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent side="top">
                        <SelectItem
                            v-for="pageSize in [10, 20, 30, 40, 50]"
                            :key="pageSize"
                            :value="`${pageSize}`"
                        >
                            {{ pageSize }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <!-- Pagination -->
            <Pagination :links="items.links" />
        </div>
    </div>
</template>
