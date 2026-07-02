<script setup lang="ts">
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    only?: string[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}>();

function go(url: string | null) {
    if (!url) return;

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        only: props.only ?? null,
    });
}

function isEllipsis(label: string) {
    return label.includes('...');
}
</script>

<template>
    <div class="flex flex-col gap-6">
        <Pagination v-if="links?.length > 3">
            <PaginationContent>
                <!-- Previous -->
                <PaginationPrevious
                    :disabled="!links[0].url"
                    @click="go(links[0].url)"
                />

                <!-- Pages -->
                <template
                    v-for="(link, index) in links.slice(1, -1)"
                    :key="index"
                >
                    <PaginationItem @click="go(link.url)" v-if="!isEllipsis(link.label)" :value="link.label" :is-active="link.active" />
                    <PaginationEllipsis v-else />
                </template>

                <!-- Next -->
                <PaginationNext
                    :disabled="!links[links.length - 1].url"
                    @click="go(links[links.length - 1].url)"
                />
            </PaginationContent>
        </Pagination>
    </div>
</template>
