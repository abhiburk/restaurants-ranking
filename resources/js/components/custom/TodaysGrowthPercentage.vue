<script setup lang="ts">
import { ArrowDown, ArrowUp } from 'lucide-vue-next';

defineProps({
    growth_percentage: {
        type: Number,
        default: 0
    },
    class: String
});

</script>

<template>
    <div class="flex items-center justify-end gap-1" :class="class">
        <ArrowUp v-if="growth_percentage > 0" class="h-2 w-2 sm:h-3 sm:w-3 text-green-800" />
        <ArrowDown v-else-if="growth_percentage < 0" class="h-2 w-2 sm:h-3 sm:w-3  text-red-800" />

        <span class="font-semibold" :class="{
            'text-green-800': growth_percentage > 0,
            'text-red-800': growth_percentage < 0,
            'text-card-foreground': growth_percentage === 0,
        }">
            {{ growth_percentage > 0 ? `+${growth_percentage}%` : `${growth_percentage}%` }}
        </span>
    </div>
</template>