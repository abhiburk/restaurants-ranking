<script setup lang="ts">
import { computed } from 'vue';
import type { ChartConfig } from '@/components/ui/chart';
import { VisXYContainer, VisAxis, VisGroupedBar } from '@unovis/vue';
import {
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
    ChartTooltipContent,
    componentToString,
} from '@/components/ui/chart';
import { Card, CardDescription, CardHeader, CardTitle } from '../ui/card';
import CardContent from '../ui/card/CardContent.vue';
import CardFooter from '../ui/card/CardFooter.vue';

interface ChartDay {
    date: string;
    label: string;
    votes: number;
    is_today: boolean;
}

const props = defineProps<{
    data: ChartDay[];
}>();

const chartData = computed(() =>
    props.data.map((d, i) => ({
        index: i,
        label: d.is_today ? 'Today' : d.label,
        votes: d.votes,
        is_today: d.is_today,
    })),
);

const chartConfig = {
    votes: {
        label: 'Votes',
        color: "var(--primary)",
    },
} satisfies ChartConfig;

const bestDay = computed(
    () => [...props.data].sort((a, b) => b.votes - a.votes)[0],
);

const totalVotes = computed(() =>
    props.data.reduce((sum, d) => sum + d.votes, 0),
);
</script>

<template>
    <Card>
        <CardHeader class="flex items-end justify-between border-b">
            <CardTitle>Last 7 days</CardTitle>
            <CardDescription>
                {{ totalVotes.toLocaleString() }} total votes
            </CardDescription>
        </CardHeader>
        <CardContent>
            <ChartContainer :config="chartConfig" class="h-36 w-full">
                <VisXYContainer :data="chartData">
                    <!-- Single VisBar instead of VisGroupedBar -->
                    <VisGroupedBar :x="(d: any) => d.index" :y="(d: any) => d.votes" :rounded-corners="4" :color="(d: any) =>
                        d.is_today ? 'var(--primary)' : 'var(--secondary)'" />

                    <!-- Force all 7 ticks to show -->
                    <VisAxis type="x" :x="(d: any) => d.index" :tick-format="(i: number) => chartData[i]?.label ?? ''"
                        :num-ticks="7" :grid-line="false" :tick-line="false" :domain-line="false"
                        :tick-text-font-size="11" :tick-text-color="'var(--muted-foreground)'" />
                    <ChartCrosshair :template="componentToString(
                        chartConfig,
                        ChartTooltipContent,
                        {
                            labelFormatter: (i: number) =>
                                chartData[i]?.label ?? '',

                        },
                    )
                        " />
                    <ChartTooltip />
                </VisXYContainer>
            </ChartContainer>
        </CardContent>
        <CardFooter v-if="bestDay" class="flex items-center justify-between border-t text-muted-foreground">
            <p class="text-sm">Best day this week</p>
            <p class="text-sm font-medium">
                {{ bestDay.is_today ? 'Today' : bestDay.label }} &sdot; {{ bestDay.votes.toLocaleString() }} votes
            </p>
        </CardFooter>
    </Card>
</template>
