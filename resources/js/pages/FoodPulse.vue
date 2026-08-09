<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'
import { Skeleton } from '@/components/ui/skeleton'
import { Waves, Wifi } from 'lucide-vue-next'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Trophy, Flame, Target, Medal, ArrowUp, Star, Activity, Bell } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController'

// ── Types ─────────────────────────────────────────────────────────
interface City {
    id: string
    name: string
    slug: string
}

interface FeedEventData {
    restaurant_name?: string
    area?: string
    milestone?: number
    total_votes?: number
    votes_last_2hrs?: number
    streak_days?: number
    pulse_score?: number
    city_name?: string
    label?: string
    rank?: number
    date?: string
    category?: string
}

interface FeedEvent {
    id: string
    type: 'daily_winner' | 'trending' | 'vote_milestone' | 'streak_milestone' | 'rank_change' | 'new_entry' | 'city_pulse'
    data: FeedEventData
    restaurant_slug: string | null
    is_pinned: boolean
    occurred_at: string
}

interface PulseResponse {
    pulse_score: number
    feed: FeedEvent[]
}

interface IconConfig {
    icon: any // Lucide component
    bg: string
    color: string
}

// ── Props ─────────────────────────────────────────────────────────
const props = defineProps<{
    city: City
}>()

// ── State ─────────────────────────────────────────────────────────
const feed = ref<FeedEvent[]>([])
const pulseScore = ref<number>(0)
const loading = ref<boolean>(true)
const error = ref<boolean>(false)
const lastUpdated = ref<string | null>(null)
const pollTimer = ref<ReturnType<typeof setInterval> | null>(null)

// ── Computed ──────────────────────────────────────────────────────
const pulseLabel = computed<string>(() => {
    if (pulseScore.value >= 80) return 'On fire'
    if (pulseScore.value >= 60) return 'Very active'
    if (pulseScore.value >= 40) return 'Picking up'
    return 'Quiet'
})

const pulseColor = computed<string>(() => {
    if (pulseScore.value >= 80) return 'text-orange-700'
    if (pulseScore.value >= 60) return 'text-yellow-700'
    if (pulseScore.value >= 40) return 'text-green-700'
    return 'text-gray-700'
})

const pulseBarColor = computed<string>(() => {
    if (pulseScore.value >= 80) return 'bg-orange-700'
    if (pulseScore.value >= 60) return 'bg-yellow-700'
    if (pulseScore.value >= 40) return 'bg-green-700'
    return 'bg-gray-700'
})

// ── Methods ───────────────────────────────────────────────────────
async function fetchFeed(): Promise<void> {
    error.value = false

    try {
        const res = await fetch(`/pulse`)
        const json = await res.json() as PulseResponse

        pulseScore.value = json.pulse_score ?? 0
        feed.value = json.feed ?? []
        lastUpdated.value = new Date().toLocaleTimeString('en-IN', {
            hour: '2-digit',
            minute: '2-digit',
        })
    } catch {
        error.value = true
    } finally {
        loading.value = false
    }
}

function iconConfig(type: FeedEvent['type']): IconConfig {
    const map: Record<FeedEvent['type'], IconConfig> = {
        daily_winner: {
            icon: Trophy,
            bg: '#FBF5E0',
            color: '#B8860B'
        },
        trending: {
            icon: Flame,
            bg: '#EEF4FF',
            color: '#1A5FA0'
        },
        vote_milestone: {
            icon: Target,
            bg: '#E1F5EE',
            color: '#2D6A4F'
        },
        streak_milestone: {
            icon: Medal,
            bg: '#FBF5E0',
            color: '#B8860B'
        },
        rank_change: {
            icon: ArrowUp,
            bg: '#F0FBF5',
            color: '#2D6A4F'
        },
        new_entry: {
            icon: Star,
            bg: '#EEEDFE',
            color: '#534AB7'
        },
        city_pulse: {
            icon: Activity,
            bg: '#FAECE7',
            color: '#D85A30'
        },
    }
    return map[type] ?? {
        icon: Bell,
        bg: 'var(--muted)',
        color: 'var(--muted-foreground)'
    }
}

function formatTitle(event: FeedEvent): string {
    const d = event.data
    switch (event.type) {
        case 'daily_winner':
            return `${d.restaurant_name} won yesterday`
        case 'trending':
            return `${d.restaurant_name} is trending right now`
        case 'vote_milestone':
            return `${d.restaurant_name} crossed ${formatVotes(d.milestone ?? 0)} votes`
        case 'streak_milestone':
            return `${d.restaurant_name} is on a ${d.streak_days} day winning streak`
        case 'rank_change':
            return `${d.restaurant_name} entered the top ${d.rank}`
        case 'new_entry':
            return `${d.restaurant_name} just joined FoodRank`
        case 'city_pulse':
            return `${d.city_name} is ${d.label?.toLowerCase()}`
        default:
            return 'Activity in your city'
    }
}

function pillText(event: FeedEvent): string | null {
    const d = event.data
    switch (event.type) {
        case 'trending':
            return d.votes_last_2hrs ? `${d.votes_last_2hrs.toLocaleString()} votes in 2 hrs` : null
        case 'streak_milestone':
            return d.streak_days ? `${d.streak_days} days at #1` : null
        case 'new_entry':
            return 'New on FoodRank'
        case 'rank_change':
            return d.rank ? `Now #${d.rank} in ${props.city.name}` : null
        default:
            return null
    }
}

function voteLabel(type: FeedEvent['type']): string {
    switch (type) {
        case 'daily_winner': return 'final votes'
        case 'vote_milestone': return 'votes today'
        case 'new_entry': return 'first votes'
        default: return 'today'
    }
}

function formatVotes(n: number): string {
    if (n >= 1000) return `${(n / 1000).toFixed(1).replace(/\.0$/, '')}k`
    return n.toLocaleString()
}

// ── Lifecycle ─────────────────────────────────────────────────────
onMounted(() => {
    fetchFeed()
    pollTimer.value = setInterval(fetchFeed, 60_000)
})

onBeforeUnmount(() => {
    if (pollTimer.value) clearInterval(pollTimer.value)
})
</script>

<template>
    <div class="space-y-3">

        <Card>
            <CardContent>
                <template v-if="loading && !feed.length">
                    <Skeleton class="h-4 w-48" />
                    <Skeleton class="h-2 w-48 mt-2" />
                </template>

                <div v-else class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex flex-col gap-1">
                            <!-- <p class="text-sm font-medium">
                                {{ city.name }} food pulse
                            </p> -->
                            <p class="text-xs text-muted-foreground">
                                Live activity &sdot; updates every minute
                            </p>
                        </div>

                        <div class="text-right flex items-center">
                            <p class="font-display font-semibold tabular-nums leading-none text-xl"
                                :class="{ [pulseColor]: true }">
                                {{ pulseScore }}&nbsp;
                            </p>
                            <p class="text-xs"> / 100</p>
                        </div>
                    </div>

                    <Progress :model-value="pulseScore" :indicator-class="pulseBarColor" />

                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs">Quiet</span>
                        <span class="text-xs font-medium" :class="{ [pulseColor]: true }">
                            {{ pulseLabel }}
                        </span>
                        <span class="text-xs">On fire</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="flex items-center justify-between mt-6">
            <p
                class="flex items-center justify-between text-xs font-medium tracking-widest text-muted-foreground uppercase">
                What's happening now
            </p>
            <p v-if="lastUpdated && !error" class="text-xs font-medium tracking-widest text-muted-foreground uppercase">
                Updated {{ lastUpdated }}
            </p>
        </div>

        <Card class="overflow-hidden">
            <CardContent>
                <template v-if="loading && !feed.length">
                    <div class="space-y-2">
                        <Skeleton v-for="i in 4" :key="i" class="h-16 w-full" />
                    </div>
                </template>

                <!-- Empty state -->
                <div v-else-if="!feed.length" class="flex flex-col items-center justify-center text-center gap-2">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-secondary">
                        <Waves />
                    </div>
                    <p class="text-sm font-medium text-primary">No activity yet today</p>
                    <p class="text-xs text-muted-foreground">
                        Events appear as restaurants receive votes
                    </p>
                </div>

                <!-- Events list -->
                <template v-else>
                    <Link v-for="event in feed" :key="event.id"
                        :href="RestaurantController.show([event.city_slug, event.restaurant_slug])"
                        class="flex items-start gap-3 border-b last:border-b-0 transition-colors duration-150 py-4 px-2">
                        <!-- Icon -->
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                            :style="{ background: iconConfig(event.type).bg }">
                            <component :is="iconConfig(event.type).icon" :size="20"
                                :color="iconConfig(event.type).color" />
                        </div>

                        <div class="flex-1 min-w-0 ">
                            <!-- Title + Pinned -->
                            <p class="text-sm font-medium leading-tight">
                                {{ formatTitle(event) }}
                                <Badge v-if="event.is_pinned" variant="secondary">Pinned</Badge>
                            </p>

                            <!-- Meta -->
                            <p class="text-xs text-muted-foreground">
                                <span v-if="event.data.city_name">{{ event.data.city_name }} · </span>
                                {{ event.occurred_at }}
                            </p>

                            <!-- Context Pill -->
                            <span v-if="pillText(event)"
                                class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full mt-1.5"
                                :style="{ background: iconConfig(event.type).bg, color: iconConfig(event.type).color }">
                                {{ pillText(event) }}
                            </span>

                        </div>

                        <!-- Vote count -->
                        <div v-if="event.data.total_votes || event.data.milestone" class="text-right shrink-0">
                            <p class="font-display font-semibold tabular-nums leading-none">
                                {{ formatVotes(event.data.total_votes ?? event.data.milestone ?? 0) }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ voteLabel(event.type) }}
                            </p>
                        </div>
                    </Link>
                </template>
            </CardContent>
        </Card>

        <!-- ── Error state ────────────────────────────────────── -->
        <Card v-if="error" class=" bg-secondary">
            <CardContent class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Wifi :size="20" />
                    <p class="text-xs text-muted-foreground">Could not load feed</p>
                </div>
                <Button variant="link" size="sm" class="text-xs" @click="fetchFeed">
                    Retry
                </Button>
            </CardContent>
        </Card>

    </div>
</template>