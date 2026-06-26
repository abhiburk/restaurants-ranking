<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item'
import { Badge } from '@/components/ui/badge';
import moment from 'moment';
import ContributorLogIcon from '@/components/custom/ContributorLogIcon.vue';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip'
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import ContributorController from '@/actions/App/Http/Controllers/ContributorController';

const props = defineProps({
    contributor: Object,
    pointsThisMonth: Number,
    maxLevel: Number,
    progress: Object
});

const actionMap = {
    submission_created: {
        label: 'Submission created',
        color: 'blue',
        icon: 'plus-circle',
    },
    submission_approved: {
        label: 'Submission approved',
        color: 'green',
        icon: 'check-circle',
    },
    submission_rejected: {
        label: 'Submission rejected',
        color: 'red',
        icon: 'x-circle',
    },
    submission_reversed: {
        label: 'Submission reversed',
        color: 'red',
        icon: 'minus-circle',
    },
    claim_created: {
        label: 'Claim created',
        color: 'blue',
        icon: 'file-plus',
    },
    claim_approved: {
        label: 'Claim approved',
        color: 'green',
        icon: 'home',
    },
    claim_rejected: {
        label: 'Claim rejected',
        color: 'red',
        icon: 'x-circle',
    },
    quality_bonus: {
        label: 'Quality bonus',
        color: 'amber',
        icon: 'star',
    },
    level_bonus: {
        label: 'Level up',
        color: 'violet',
        icon: 'arrow-up-circle',
    },
}

const colorClasses = {
    green: {
        bg: 'bg-green-100 dark:bg-green-900',
        icon: 'text-green-700 dark:text-green-300',
        pts: 'text-green-700 dark:text-green-400',
    },
    red: {
        bg: 'bg-red-100 dark:bg-red-900',
        icon: 'text-red-700 dark:text-red-300',
        pts: 'text-red-700 dark:text-red-400',
    },
    blue: {
        bg: 'bg-blue-100 dark:bg-blue-900',
        icon: 'text-blue-700 dark:text-blue-300',
        pts: 'text-blue-700 dark:text-blue-400',
    },
    amber: {
        bg: 'bg-amber-100 dark:bg-amber-900',
        icon: 'text-amber-700 dark:text-amber-300',
        pts: 'text-amber-700 dark:text-amber-400',
    },
    violet: {
        bg: 'bg-violet-100 dark:bg-violet-900',
        icon: 'text-violet-700 dark:text-violet-300',
        pts: 'text-violet-700 dark:text-violet-400',
    },
}
</script>

<template>
    <AppLayout :sidebar="false">

        <Head title="Leaderboard" />

        <h1 class="sr-only">Joined Communities</h1>

        <SettingsLayout>
            <div class="flex flex-col gap-4">
                <Item variant="outline" class="bg-card">
                    <ItemMedia>
                        <Avatar class="size-10">
                            <AvatarImage :src="contributor.city.logo" />
                            <AvatarFallback>{{ contributor.city.name.charAt(0) }}</AvatarFallback>
                        </Avatar>
                    </ItemMedia>
                    <ItemContent>
                        <ItemTitle>{{ contributor.city.name }}</ItemTitle>
                        <ItemDescription>Member since {{ moment(contributor.created_at).fromNow() }}</ItemDescription>
                    </ItemContent>
                    <ItemActions>
                        <Badge class="text-xs px-4 py-1.5">
                            <span class="text-xs font-medium">
                                {{ contributor.contributor_level.name }}
                            </span>
                        </Badge>
                    </ItemActions>
                </Item>

                <!-- Stats Row -->
                <div class="grid sm:grid-cols-4 grid-cols-2 gap-2">
                    <Card class="bg-secondary">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <CardContent>
                                        <p class="text-xs text-secondary-foreground m-0 mb-1">Total points</p>
                                        <p class="text-xl font-medium text-foreground m-0">{{ contributor.points }}</p>
                                    </CardContent>
                                </TooltipTrigger>
                                <TooltipContent class="max-w-xs text-sm whitespace-normal space-y-1">
                                    <p>
                                        Total points you have earned across all your contributions — restaurant
                                        submissions and claims.
                                        Points determine your level and leaderboard ranking.
                                    </p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </Card>
                    <Card class="bg-secondary">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <CardContent>
                                        <p class="text-xs text-secondary-foreground m-0 mb-1">This month</p>
                                        <p class="text-xl font-medium text-foreground m-0">{{ pointsThisMonth }}</p>
                                    </CardContent>
                                </TooltipTrigger>
                                <TooltipContent class="max-w-xs text-sm whitespace-normal space-y-1">
                                    <p>
                                        Points earned in the current calendar month. Resets on the 1st of every month. A
                                        good way to track how active you have been recently.
                                    </p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </Card>
                    <Card class="bg-secondary">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <CardContent>
                                        <p class="text-xs text-secondary-foreground m-0 mb-1">Quality score</p>
                                        <div class="flex items-baseline gap-1">
                                            <p class="text-xl font-medium text-foreground m-0">{{
                                                contributor.quality_score }}</p>
                                            <p class="text-xs text-muted-foreground m-0">/ {{
                                                progress.quality_score_required }}</p>
                                        </div>
                                        <!-- <p v-if="progress.quality_score_met"
                                    class="text-xs text-green-600 dark:text-green-400 m-0 mt-1">Met ✓</p>
                                <p v-else class="text-xs text-amber-600 m-0 mt-1">Need {{ (progress.quality_score_required -
                                    contributor.quality_score).toFixed(2) }} more</p> -->
                                    </CardContent>
                                </TooltipTrigger>
                                <TooltipContent class="max-w-xs text-sm whitespace-normal space-y-1">
                                    <p>
                                        A ratio of your approved vs rejected contributions. A score of 0.80 means 80% of
                                        your submissions were approved. Higher score unlocks higher levels and builds
                                        your reputation as a trusted contributor.
                                    </p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </Card>
                    <Card class="bg-secondary">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <CardContent>
                                        <p class="text-xs text-secondary-foreground m-0 mb-1">Level</p>
                                        <div class="flex items-baseline gap-1">
                                            <p class="text-xl font-medium text-foreground m-0">{{
                                                contributor.contributor_level.level }}</p>
                                            <p class="text-xs text-muted-foreground m-0">/ {{ maxLevel }}</p>
                                        </div>
                                    </CardContent>
                                </TooltipTrigger>
                                <TooltipContent class="max-w-xs text-sm whitespace-normal space-y-1">
                                    <p>
                                        Your current rank as a contributor in this city. Levels are unlocked by meeting
                                        both the points threshold and the required quality score. Higher levels reflect
                                        deeper trust and local expertise.
                                    </p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </Card>
                </div>

                <!-- Level Progress -->
                <Card>
                    <CardContent>
                        <div class="flex justify-between items-baseline mb-2.5">
                            <p class="text-sm font-medium text-foreground m-0">
                                {{ progress.is_max_level ? 'Max level reached' : `Progress to ${progress.next_level}` }}
                            </p>
                            <p class="text-xs text-muted-foreground m-0">
                                {{ progress.points_current }} / {{ progress.points_required }} pts
                            </p>
                        </div>
                        <div class="bg-muted rounded-full h-1.5 overflow-hidden">
                            <div class="bg-primary h-full rounded-full transition-all duration-500"
                                :style="{ width: `${progress.percentage}%` }">
                            </div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <p class="text-xs text-muted-foreground m-0">{{ progress.current_level }}</p>
                            <p class="text-xs text-muted-foreground m-0">
                                <template v-if="progress.is_max_level">You're at the top</template>
                                <template v-else-if="!progress.quality_score_met">
                                    <span class="text-amber-600">Quality score blocking level up</span>
                                </template>
                                <template v-else>
                                    {{ progress.next_level }} · {{ progress.points_remaining }} pts to go
                                </template>
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Activity Log -->
                <div class="bg-card border border-border rounded-xl p-5">
                    <p class="text-sm font-medium text-foreground m-0 mb-4">Recent activity</p>

                    <div class="flex flex-col divide-y divide-border">
                        <div v-if="!contributor.contributor_logs.length">
                            <p class="text-sm text-muted-foreground m-0">No activity yet</p>
                        </div>
                        <div v-for="log in contributor.contributor_logs" :key="log.id"
                            class="flex items-center gap-3 py-2.5">
                            <!-- Icon -->
                            <div
                                :class="['w-8 h-8 rounded-lg flex items-center justify-center shrink-0', colorClasses[actionMap[log.action].color].bg]">
                                <ContributorLogIcon :icon="actionMap[log.action].icon"
                                    :class="['w-4 h-4', colorClasses[actionMap[log.action].color].icon]" />
                            </div>

                            <!-- Label + subtitle -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-foreground m-0">
                                    {{ actionMap[log.action].label }}
                                </p>
                                <p class="text-xs text-muted-foreground m-0 mt-0.5">
                                    <template v-if="log.loggable">{{ log.loggable.name }} · </template>
                                    <template v-if="log.note">{{ log.note }} · </template>
                                    {{ moment(log.created_at).fromNow() }}
                                </p>
                            </div>

                            <!-- Points -->
                            <span :class="['text-sm font-medium', colorClasses[actionMap[log.action].color].pts]">
                                {{ log.points > 0 ? `+${log.points}` : log.points }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Leave -->
                <AlertDialog>
                    <Card class="bg-destructive/10">
                        <CardContent class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-foreground m-0">Leave {{ contributor.city.name }}</p>
                                <p class="text-xs text-muted-foreground m-0 mt-0.5">
                                    You will lose your rank and activity history in this city.
                                </p>
                            </div>
                            <AlertDialogTrigger as-child>
                                <Button variant="destructive" size="sm">
                                    Leave
                                </Button>
                            </AlertDialogTrigger>
                        </CardContent>
                    </Card>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Leave {{ contributor.city.name }}?</AlertDialogTitle>
                            <AlertDialogDescription>
                                This will remove you as a contributor in {{ contributor.city.name }}. Your points and
                                activity logs in
                                this city will be lost and cannot be recovered.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                            <!-- <AlertDialogAction>Yes, leave {{ contributor.city.name }}</AlertDialogAction> -->
                            <Form :action="ContributorController.destroy([contributor.id])" method="delete"
                                v-slot="{ errors, processing }">
                                <Button type="submit" class="w-full">
                                    <Spinner v-if="processing" />
                                    Yes, leave {{ contributor.city.name }}
                                </Button>
                            </Form>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>