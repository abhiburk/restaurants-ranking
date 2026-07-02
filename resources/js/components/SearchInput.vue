<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';
import { ref, watch } from 'vue';
import { InputGroup, InputGroupAddon, InputGroupInput } from './ui/input-group';
import { SearchIcon } from '@lucide/vue';
import { CircleX } from 'lucide-vue-next';

type SearchParams = Record<string, any>;

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        route: string;
        placeholder?: string;
        paramName?: string;
        extraParams?: SearchParams;
        debounceMs?: number;
        class?: string,
        options?: {
            only?: string[];
        };
    }>(),
    {
        placeholder: 'Search...',
        paramName: 'search',
        extraParams: () => ({}),
        debounceMs: 500,
    },
);

const emit = defineEmits(['update:modelValue']);

const value = ref(props.modelValue ?? '');

/* Sync when coming from server */
watch(
    () => props.modelValue,
    (v) => (value.value = v ?? ''),
);

const performSearch = debounce((val: string) => {
    router.get(
        props.route,
        {
            ...props.extraParams,
            [props.paramName]: val || undefined,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
            only: props.options?.only
        },
    );
}, props.debounceMs);

watch(value, (val) => {
    emit('update:modelValue', val);
    performSearch(val);
});
</script>

<template>
    <InputGroup class="w-full py-6 rounded-2xl border bg-card text-card-foreground">
        <InputGroupInput v-model="value" :placeholder="placeholder" :class="class" />
        <InputGroupAddon>
            <SearchIcon />
        </InputGroupAddon>
        <InputGroupAddon align="inline-end" v-if="value">
            <CircleX v-on:click="() => value = ''" class="cursor-pointer" />
        </InputGroupAddon>
    </InputGroup>
</template>
