<script setup>
import { Zap } from 'lucide-vue-next';
import { Badge } from '../ui/badge';

defineProps({
    streak: Object,
});
</script>

<template>
    <Badge
        v-if="streak >= 2"
        variant="secondary"
        class="x-2.5 border-green-300 bg-gradient-to-r from-green-50 to-green-100  text-green-800"
    >
        ⚡
        <span v-if="streak >= 1"> {{ streak }} day streak </span>
    </Badge>
</template>
