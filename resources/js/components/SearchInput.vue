<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';
import { ref, watch } from 'vue';
import { InputGroupInput } from './ui/input-group';

type SearchParams = Record<string, any>;

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        route: string;
        placeholder?: string;
        paramName?: string;
        extraParams?: SearchParams;
        debounceMs?: number;
        class?: string
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
        },
    );
}, props.debounceMs);

watch(value, (val) => {
    emit('update:modelValue', val);
    performSearch(val);
});
</script>

<template>
    <InputGroupInput v-model="value" :placeholder="placeholder" class="max-w-sm" :class="class" />
</template>
