<script setup>
import { Badge } from '../ui/badge';

defineProps({
    is_trending: Object
})
</script>

<template>
    <Badge
        v-if="is_trending"
        variant="secondary"
        class=" border-yellow-300 bg-gradient-to-r from-yellow-50 to-yellow-100  text-yellow-800"
    >
        <span>🔥 Trending</span>
    </Badge>
</template>