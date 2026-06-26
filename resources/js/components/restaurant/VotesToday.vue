<script setup>

const props = defineProps({
    votes_today: Number,
    votes_yesterday: Number,
    growth_percentage: Number
});

</script>
<template>
    <div class="shrink-0 text-right">
        <p class="font-display text-lg font-semibold tabular-nums">
            {{ votes_today.toLocaleString() }} 
        </p>
        <p class="text-xs text-muted-foreground">votes today</p>
        <!-- <TodaysVoteChange :growth_percentage="growth_percentage"/> -->
    </div>
</template>
