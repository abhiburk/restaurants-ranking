<script setup lang="ts">
import { Deferred, Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import Card from '@/components/ui/card/Card.vue';
import { CardContent, CardFooter } from '@/components/ui/card';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Building, ChartLine, CheckCircleIcon } from 'lucide-vue-next';
import UndrawAgreement from '@/components/svg/UndrawAgreement.vue';
import UndrawAddPost from '@/components/svg/UndrawAddPost.vue';
import UndrawApprove from '@/components/svg/UndrawApprove.vue';
import ContributorController from '@/actions/App/Http/Controllers/ContributorController';
import { Skeleton } from '@/components/ui/skeleton';
import NumberFlow from '@number-flow/vue'

const page = usePage();
const appName = computed(() => page.props.name);

const props = defineProps({
    totalRestaurants: {
        type: Number,
        default: 0
    },
    totalContributors: {
        type: Number,
        default: 0
    },
    totalCities: {
        type: Number,
        default: 0
    },
    topThreeContributors: {
        type: Array,
        default: () => []
    }
});

const displayOrder = computed(() => {
    const sorted = [...props.topThreeContributors].sort((a, b) => b.restaurants_submissions_count - a.restaurants_submissions_count);
    return [sorted[1], sorted[0], sorted[2]];
});

const medalIcon = (index) => {
    const medals = ['🥈', '🥇', '🥉'];
    return medals[index] || '🏅';
};

const rankLabel = (index) => {
    const ranks = ['#2', '#1', '#3'];
    return ranks[index] || '#—';
};
</script>

<template>

    <Head title="About" />

    <AppLayout :sidebar="false">
        <div class="space-y-10">
            <!-- Hero -->
            <section>
                <Card class="relative overflow-hidden bg-linear-to-br from-amber-50 via-white to-orange-50">
                    <CardHeader>
                        <Badge class="text-xs px-4 py-1.5" variant="secondary">
                            <span>⚡ Community-Powered Discovery</span>
                        </Badge>
                    </CardHeader>
                    <CardContent>
                        <div
                            class="absolute top-20 right-0 w-full h-72 bg-amber-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse">
                        </div>
                        <div class="relative">
                            <div class="space-y-8">
                                <h1
                                    class="text-4xl md:text-6xl font-extrabold tracking-tight text-stone-800 leading-tight">
                                    Become a
                                    <span
                                        class="gold-gradient bg-clip-text text-transparent bg-linear-to-r from-amber-600 to-orange-600">
                                        Local Scout
                                    </span>
                                </h1>
                                <p class="text-xl text-stone-600 leading-relaxed max-w-2xl">
                                    Help us map the most authentic restaurants in your neighborhood.
                                    Earn badges, credits, and recognition — and shape the future of local dining.
                                </p>
                                <div class="flex flex-wrap gap-4 ">
                                    <Button size="lg" as-child>
                                        <Link :href="ContributorController.create()">Join the community</Link>
                                    </Button>
                                </div>
                            </div>
                            <div class="hidden lg:block absolute bottom-0 right-6 w-72 opacity-90">
                                <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png"
                                    class="drop-shadow-2xl">
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <div class="flex flex-wrap gap-6 text-sm text-stone-500">
                            <div class="flex items-center gap-2">
                                <CheckCircleIcon class="text-green-600" />
                                Verify restaurants
                            </div>
                            <div class="flex items-center gap-2">
                                <ChartLine class="text-amber-600" />
                                Unlock exclusive perks
                            </div>
                            <div class="flex items-center gap-2">
                                <Building />
                                Hyperlocal impact
                            </div>
                        </div>
                    </CardFooter>
                </Card>
            </section>

            <!-- Stats -->
            <section>
                <div class="overflow-hidden rounded-3xl border border-secondary bg-secondary">
                    <div class="grid grid-cols-3 divide-x divide-primary/10">
                        <div class="p-5 text-center">
                            <p class=" sm:text-3xl text-2xl font-semibold text-foreground">
                                <div class="flex items-center justify-center">
                                    <Deferred data="totalRestaurants">
                                        <template #fallback>
                                            <Skeleton class="w-8 h-9 rounded-md" />
                                        </template>
                                        <NumberFlow :format="{ notation: 'compact' }" :value="totalRestaurants" />
                                    </Deferred>
                                </div>
                            </p>
                            <p class="text-sm text-muted-foreground">
                                restaurants added
                            </p>
                        </div>
                        <div class="p-5 text-center">
                            <p class=" sm:text-3xl text-2xl font-semibold text-foreground">
                                <div class="flex items-center justify-center">
                                    <Deferred data="totalContributors">
                                        <template #fallback>
                                            <Skeleton class="w-8 h-9 rounded-md" />
                                        </template>
                                        <NumberFlow :format="{ notation: 'compact' }" :value="totalContributors" />
                                    </Deferred>
                                </div>
                            </p>
                            <p class="text-sm text-muted-foreground">
                                contributors
                            </p>
                        </div>
                        <div class="p-5 text-center">
                            <p class=" sm:text-3xl text-2xl font-semibold text-foreground">
                                <div class="flex items-center justify-center">
                                    <Deferred data="totalCities">
                                        <template #fallback>
                                            <Skeleton class="w-8 h-9 rounded-md" />
                                        </template>
                                        <NumberFlow :format="{ notation: 'compact' }" :value="totalCities" />
                                    </Deferred>
                                </div>
                            </p>
                            <p class="text-sm text-muted-foreground">
                                cities covered
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works -->
            <section>
                <div class="text-center space-y-4">
                    <Badge class="bg-secondary text-secondary-foreground px-4 py-2 text-sm font-medium ">
                        ⚡ Simple & Transparent
                    </Badge>
                    <h2 class="text-4xl font-bold tracking-tight">
                        How {{ appName }} Contributors Work
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        Help discover great restaurants in your city in just a few simple steps.
                    </p>
                </div>
                <div class="mt-12 grid gap-6 lg:grid-cols-3">
                    <!-- Step 1 -->
                    <Card>
                        <CardHeader>
                            <Badge
                                class="bg-secondary text-secondary-foreground px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">
                                Step 1
                            </Badge>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <h3 class="text-2xl font-semibold"> Become a Contributor </h3>
                                <p class="text-muted-foreground">
                                    Apply with your city and areas you know best.
                                </p>
                                <div class="aspect-4/3 overflow-hidden rounded-2xl flex items-center justify-center">
                                    <UndrawAgreement />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Step 2 -->
                    <Card>
                        <CardHeader>
                            <Badge
                                class="bg-secondary text-secondary-foreground px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">
                                Step 2
                            </Badge>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <h3 class="text-2xl font-semibold">Add Restaurants</h3>
                                <p class="text-muted-foreground">
                                    Submit hidden gems and local favourites to our platform.
                                </p>
                                <div class="aspect-4/3 overflow-hidden rounded-2xl flex items-center justify-center">
                                    <UndrawAddPost />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Step 3 -->
                    <Card>
                        <CardHeader>
                            <Badge
                                class="bg-secondary text-secondary-foreground px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">
                                Step 3
                            </Badge>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <h3 class="text-2xl font-semibold">Review & Recognition</h3>
                                <p class="text-muted-foreground">
                                    Approved listings help you earn badges and recognition.
                                </p>
                                <div class="aspect-4/3 overflow-hidden rounded-2xl flex items-center justify-center">
                                    <UndrawApprove />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <!-- Benefits -->
            <section>
                <div class="overflow-hidden rounded-4xl border bg-secondary border-secondary">
                    <div class="border-b border-primary/10 px-8 py-6">
                        <h2 class="text-3xl font-bold">
                            Why Join {{ appName }} Contributors?
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            Be part of building India's most trusted food discovery platform.
                        </p>
                    </div>
                    <div class="grid divide-y divide-primary/10 md:grid-cols-4 md:divide-x md:divide-y-0">
                        <div class="p-6">
                            <div class="text-3xl">🏅</div>
                            <h3 class="mt-4 font-semibold">
                                Recognition
                            </h3>
                            <p class="mt-2 text-sm text-muted-foreground">
                                Earn badges and contributor status.
                            </p>
                        </div>
                        <div class="p-6">
                            <div class="text-3xl">📍</div>
                            <h3 class="mt-4 font-semibold">
                                Local Impact
                            </h3>
                            <p class="mt-2 text-sm text-muted-foreground">
                                Help great restaurants get discovered.
                            </p>
                        </div>
                        <div class="p-6">
                            <div class="text-3xl">🚀</div>
                            <h3 class="mt-4 font-semibold">
                                Early Access
                            </h3>
                            <p class="mt-2 text-sm text-muted-foreground">
                                Become part of the founding community.
                            </p>
                        </div>
                        <div class="p-6">
                            <div class="text-3xl">👑</div>
                            <h3 class="mt-4 font-semibold">
                                Leaderboards
                            </h3>
                            <p class="mt-2 text-sm text-muted-foreground">
                                Compete with contributors from other cities.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Top Contributors -->
            <section>
                <div class="text-center">
                    <Badge class="bg-secondary text-secondary-foreground px-4 py-2 text-sm font-medium ">
                        🏆 Community Leaders
                    </Badge>
                    <h2 class="mt-4 text-4xl font-bold tracking-tight text-foreground">
                        Top Contributors
                    </h2>
                    <p class="mx-auto mt-3 max-w-2xl text-lg text-muted-foreground">
                        Recognizing community members helping discover great restaurants.
                    </p>
                </div>
                
                <div class="mt-12 grid gap-5 md:grid-cols-3">
                    <Card v-for="(contributor, index) in displayOrder" :key="contributor?.id"
                        class="flex flex-col justify-center transition-all duration-300 hover:shadow-lg" :class="{
                            'md:mt-8': index === 0 || index === 2,
                            'bg-secondary text-secondary-foreground scale-105 shadow-xl': index === 1,
                            'hover:scale-105': index !== 1,
                        }">
                        <CardContent>
                            <div class="text-center space-y-4">
                                <!-- Rank Badge -->
                                <!-- <div class="text-xs font-medium text-muted-foreground">
                                    {{ rankLabel(index) }}
                                </div> -->

                                <!-- Medal -->
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full text-3xl"
                                    :class="{
                                        'bg-primary text-primary-foreground': index === 1,
                                        'bg-secondary text-secondary-foreground': index !== 1,
                                    }">
                                    {{ medalIcon(index) }}
                                </div>

                                <!-- Name & City -->
                                <div>
                                    <h3 class="text-xl font-semibold">{{ contributor?.user.name }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ contributor?.city.name }}</p>
                                </div>

                                <!-- Stats -->
                                <div class="rounded-2xl p-4" :class="{
                                    'bg-white text-foreground': index === 1,
                                    'bg-secondary': index !== 1,
                                }">
                                    <div class="text-3xl font-bold">{{ contributor?.restaurants_submissions_count }}
                                    </div>
                                    <div class="text-sm text-muted-foreground">Restaurants Added</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <!-- CTA -->
            <section>
                <Card class="bg-primary text-primary-foreground flex justify-center items-center text-center py-10">
                    <CardHeader class="flex justify-center items-center">
                        <Badge class="bg-secondary text-secondary-foreground text-sm font-medium ">
                            🏅 Founding Contributors
                        </Badge>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-5">
                        <h2 class="max-w-3xl text-4xl font-bold tracking-tight md:text-5xl">
                            Your City Knows Great Food. Help The World Discover It.
                        </h2>
                        <p class="max-w-2xl text-lg leading-relaxed text-muted-foreground">
                            Become one of the first FoodRank contributors and help uncover the restaurants that deserve
                            recognition.
                        </p>
                    </CardContent>
                    <CardFooter>
                        <Button size="lg" as-child variant="secondary">
                            <Link :href="ContributorController.create()">Join the community</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
