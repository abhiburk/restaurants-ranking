<script setup>
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import {
    CheckCircleIcon,
    EyeIcon,
    Flag,
    TrendingDown,
    TrendingUp,
} from 'lucide-vue-next';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import { Button } from '@/components/ui/button';
import FingerprintJS from '@fingerprintjs/fingerprintjs';
import VoteController from '@/actions/App/Http/Controllers/Restaurant/VoteController';
import { onMounted, ref } from 'vue';
import { Spinner } from '@/components/ui/spinner';

import RestaurantVoteHistoryChart from '@/components/restaurant/RestaurantVoteHistoryChart.vue';
import Timer from '@/components/Timer.vue';
import RestaurantBanner from '@/components/restaurant/RestaurantBanner.vue';
import confetti from "@hiseb/confetti";
import {
    Drawer,
    DrawerContent,
    DrawerFooter,
    DrawerTrigger,
} from '@/components/ui/drawer'
import StartInput from '@/components/custom/StartInput.vue';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from '@/components/ui/card';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import moment from 'moment';
import RestaurantGallary from '@/components/restaurant/RestaurantGallary.vue';
import RestaurantShare from '@/components/restaurant/RestaurantShare.vue';
import { Item, ItemActions, ItemContent, ItemDescription, ItemMedia, ItemTitle } from '@/components/ui/item';
import RestaurantAvatar from '@/components/restaurant/RestaurantAvatar.vue';
import { usePermissions } from '@/composables/usePermissions';
import { permissions } from '@/constants/permissions';

defineProps({
    restaurant: Object,
    stats: Object,
    voted: Object,
    vote_source: String,
    nearbyRestaurants: Array,
});

const { can } = usePermissions();

const visitorId = ref('');
const locationCoordinates = ref({ latitude: null, longitude: null });

const fpPromise = FingerprintJS.load();
(async () => {
    const fp = await fpPromise;
    const result = await fp.get();
    visitorId.value = result.visitorId;
})();

const handleVoteSuccess = () => {
    confetti({
        particleCount: 50,
        spread: 70,
    });
};

const locationError = ref(''); // Error message

// Check for location permission on component mount
onMounted(() => {
    verifyLocationPermission();
});

const verifyLocationPermission = () => {
    if (navigator.permissions) {
        navigator.permissions.query({ name: 'geolocation' }).then((permissionStatus) => {
            console.log(permissionStatus);
            if (permissionStatus.state === 'prompt') {
                getUserLocation(); // Fetch location if permission is already granted
            } else if (permissionStatus.state === 'denied') {
                locationError.value = 'Location access denied. Please enable it in your browser settings to share your location.';
            } else if (permissionStatus.state === 'granted') {
                getUserLocation();
            }
        }).catch((error) => {
            console.error('Error checking location permission:', error);
        });
    }
}

// Method to fetch user's location
const getUserLocation = () => {
    locationError.value = '';

    if (!navigator.geolocation) {
        locationError.value = 'Geolocation is not supported by your browser.';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            locationCoordinates.value = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
            };
            console.log(locationCoordinates);
        },
        (error) => {
            locationError.value = 'Unable to retrieve your location.';
            console.error('Error fetching location:', error);
        }
    );
};

</script>

<template>

    <Head title="Welcome"> </Head>
    <AppLayout>
        <div class="mx-auto w-full space-y-5">
            <RestaurantBanner :restaurant="restaurant" />

            <!-- KMs Card -->
            <!-- <div class="flex items-center justify-between rounded-2xl border border-border bg-card px-4 py-3">
                <div class="flex items-center gap-3">
                    <EyeIcon class="h-5 w-5 text-primary" />

                    <p class="text-sm">
                        <span class="font-semibold">
                            {{ restaurant.views+1 }}
                        </span>
                        people viewed this restaurant
                    </p>
                </div>

                <div class="rounded-full bg-secondary px-3 py-1 text-xs">
                    Eligible to vote
                </div>
            </div> -->

            <!-- Voting Card -->
            <section v-if="!voted">
                <Card>
                    <CardHeader>
                        <CardTitle>
                            <h3 class="sm:text-2xl text-xl font-bold tracking-tight">
                                Enjoyed your visit?
                            </h3>
                        </CardTitle>
                        <CardDescription class="flex items-center gap-2">
                            Your anonymous vote helps decide today’s rankings and supports the local food scene.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex gap-4 flex-row items-center justify-between">
                            <div class="text-center sm:text-left">
                                <p class="text-xs uppercase tracking-[0.14em] text-muted-foreground">
                                    Ranking Resets In
                                </p>
                                <Timer class="mt-2 text-3xl font-bold tracking-tight" />
                            </div>
                            <Drawer>
                                <DrawerTrigger>
                                    <Button>
                                        Vote Now
                                    </Button>
                                </DrawerTrigger>
                                <DrawerContent>
                                    <Form method="post" @success="handleVoteSuccess" #default="{ processing }" :action="VoteController.store([
                                        restaurant?.city.slug,
                                        restaurant?.slug,
                                    ])
                                        " :transform="(data) => ({
                                            ...data,
                                            visitor_id: visitorId,
                                            vote_source: vote_source,
                                            latitude: locationCoordinates.latitude || null,
                                            longitude: locationCoordinates.longitude || null,
                                        })
                                            ">

                                        <!-- Vote Experience Card -->
                                        <div class="mx-auto w-full max-w-2xl overflow-y-auto max-h-[80vh] h-[75vh]">
                                            <div class="p-4 pb-0 grid gap-7">
                                                <!-- Header -->
                                                <div>
                                                    <p
                                                        class="text-xs uppercase tracking-[0.22em] text-muted-foreground">
                                                        Quick Experience
                                                    </p>
                                                    <h3
                                                        class="mt-2 text-2xl font-semibold tracking-tight text-foreground">
                                                        How was your visit?
                                                    </h3>
                                                    <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
                                                        Your feedback helps improve daily rankings and helps
                                                        others discover the best restaurants.
                                                        <!-- Account registration is optional and not
                                                        required to vote. Your vote is anonymous, but we
                                                        use a browser fingerprint to ensure one vote per
                                                        person. We do not store any personally
                                                        identifiable information unless the user is
                                                        registered. -->
                                                    </p>
                                                </div>

                                                <!-- Rating -->
                                                <div>
                                                    <Label class="mb-3 block text-sm font-medium text-foreground">
                                                        Overall Experience
                                                    </Label>
                                                    <div class="flex items-center gap-2">
                                                        <StartInput />
                                                    </div>
                                                </div>

                                                <!-- Tags -->
                                                <div>
                                                    <Label class="mb-3 block text-sm font-medium text-foreground">
                                                        What stood out?
                                                    </Label>

                                                    <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto">
                                                        <Label v-for="amenity in restaurant.amenities ?? []"
                                                            :key="amenity" class="group">
                                                            <Checkbox :value="amenity" class="peer sr-only"
                                                                name="amenities[]" />
                                                            <div
                                                                class="inline-flex cursor-pointer items-center rounded-full border border-secondary bg-secondary px-4 py-2 text-sm font-medium text-secondary-foreground transition-all duration-200 peer-data-[state=checked]:border-primary peer-data-[state=checked]:bg-primary peer-data-[state=checked]:text-primary-foreground hover:opacity-90">
                                                                {{ amenity }}
                                                            </div>
                                                        </Label>
                                                    </div>
                                                </div>

                                                <!-- Comment -->
                                                <div>
                                                    <Label class="mb-3 block text-sm font-medium text-foreground">
                                                        Share your thoughts
                                                    </Label>
                                                    <Textarea rows="2" name="comment"
                                                        placeholder="What did you like about this place?" />
                                                </div>
                                            </div>
                                            <DrawerFooter>
                                                <Button size="lg" type="submit" :disabled="processing">
                                                    <Spinner class="animate-spin" v-if="processing" />
                                                    Submit
                                                </Button>
                                            </DrawerFooter>
                                        </div>
                                    </Form>
                                </DrawerContent>
                            </Drawer>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <!-- Vote Success Card -->
            <section v-else>
                <Card class="bg-linear-to-br from-green-50 via-green-50 to-green-100/80
                    dark:from-green-950/20
                    dark:via-card
                    dark:to-green-900/10
                    border-green-200/50
                    dark:border-green-900/50">
                    <CardHeader>
                        <CardTitle>
                            <h3 class="text-2xl font-bold tracking-tight text-green-800 flex items-center gap-3">
                                <CheckCircleIcon :size="25" class="inline-block" />
                                Thank you for voting!
                            </h3>
                        </CardTitle>
                        <CardDescription>
                            Thank you for sharing your feedback! Your vote has been counted and will be reflected in the
                            rankings. We appreciate your contribution to the community and hope you have a great day!
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-center sm:text-left">
                                <p class="text-xs uppercase tracking-[0.14em] text-muted-foreground">
                                    Ranking Resets In
                                </p>
                                <Timer class="mt-2 text-3xl font-bold tracking-tight" />
                            </div>
                            <!-- Voted at -->
                            <div class="text-center sm:text-left">
                                <p class="text-xs uppercase tracking-[0.14em] text-muted-foreground">
                                    You voted
                                </p>
                                <p class="mt-2 text-sm font-medium tracking-tight text-foreground">
                                    {{ moment(voted.created_at).format("LLLL") }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <div class="mx-auto space-y-5 mt-5">
                <!-- Stats Grid -->
                <section class="grid grid-cols-2 gap-4">
                    <!-- Rank -->
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <p class="text-xs uppercase tracking-[0.14em] truncate">
                                Current Rank
                            </p>
                            <h2 class="text-6xl font-bold tracking-tight leading-none">#{{ stats?.rank }}</h2>
                            <p class="text-sm">
                                All time <strong>#{{ stats?.all_time.best_rank || '—' }}</strong>
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Movement -->
                    <Card class="bg-secondary text-secondary-foreground">
                        <CardContent class="flex flex-col gap-2">
                            <p class="text-xs uppercase tracking-[0.14em] truncate">
                                Rank Movement
                            </p>
                            <h2 :class="{
                                'text-green-800': stats?.rank_change > 0,
                                'text-red-800': stats?.rank_change < 0,
                                'text-gray-500': stats?.rank_change === 0,
                            }" class="text-6xl font-bold tracking-tight leading-none">
                                <span class="flex items-center gap-1">
                                    <span v-if="stats?.rank_change > 0">↑</span>
                                    <span v-else-if="stats?.rank_change < 0">↓</span>
                                    {{ stats?.rank_movement_label }}
                                </span>
                            </h2>
                            <p class="text-sm">
                                Since morning
                            </p>
                        </CardContent>
                    </Card>

                </section>

                <!-- Additional Insights -->
                <section class="grid gap-4 grid-cols-3">
                    <!-- Last Hour -->
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <p class="text-xs uppercase tracking-[0.14em]">
                                Last Hour
                            </p>
                            <p class="text-xl font-bold tracking-tight" :class="{
                                'text-green-800':
                                    stats?.hourly_change.direction === 'up',
                                'text-red-800':
                                    stats?.hourly_change.direction ===
                                    'down',
                                'text-gray-500':
                                    stats?.hourly_change.direction ===
                                    'neutral',
                            }">
                                <template v-if="stats?.hourly_change?.direction === 'up'">
                                    <span class="flex items-center gap-1">
                                        +{{ stats?.hourly_change?.percent }}%
                                        <TrendingUp :size="20" />
                                    </span>
                                </template>
                                <template v-else-if="stats?.hourly_change?.direction === 'down'">
                                    <span class="flex items-center gap-1">
                                        -{{ stats?.hourly_change?.percent }}%
                                        <TrendingDown :size="20" />
                                    </span>
                                </template>
                                <template v-else>
                                    <span class="flex items-center gap-1">
                                        0%
                                    </span>
                                </template>
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ stats?.hourly_change?.last_hour_votes }} votes
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Best Day -->
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <p class="text-xs uppercase tracking-[0.14em]">
                                Best Day
                            </p>
                            <h3 class="text-xl font-bold tracking-tight">
                                {{ stats?.all_time.best_day_label ?? '—' }}
                            </h3>
                            <p class="text-xs text-muted-foreground">
                                {{ stats?.all_time.best_day_votes.toLocaleString() }} votes
                            </p>
                        </CardContent>
                    </Card>

                    <!-- All Time -->
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <p class="text-xs uppercase tracking-[0.14em]">
                                All Time
                            </p>
                            <h3 class="text-xl font-bold tracking-tight">
                                {{ stats?.all_time.total_votes || '—' }}
                            </h3>
                            <p class="text-xs text-muted-foreground">total votes</p>
                        </CardContent>
                    </Card>
                </section>

                <!-- Gallery -->
                <RestaurantGallary v-if="restaurant.media.length" :restaurant="restaurant" />

                <!-- Restaurant Meta -->
                <section>
                    <Card>
                        <CardHeader>
                            <CardDescription>
                                <p class="text-sm uppercase tracking-[0.14em]">
                                    About
                                </p>
                            </CardDescription>
                            <CardTitle>
                                <h2 class="text-3xl font-bold tracking-tight">
                                    {{ restaurant.name }}
                                </h2>
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="flex flex-col gap-6">
                            <p class="text-sm leading-relaxed text-muted-foreground">
                                {{ restaurant.description }}
                            </p>
                            <div class=" grid grid-cols-2 gap-3">

                                <div class="rounded-2xl bg-secondary p-4">
                                    <p class="text-sm">Opening & Closing Times</p>

                                    <h4 class="mt-2 text-sm font-semibold">
                                        {{ restaurant?.open_hours }} - {{ restaurant?.close_hours }}
                                    </h4>
                                </div>
                                <div class="rounded-2xl bg-secondary p-4">
                                    <p class="text-sm ">Category</p>

                                    <h4 class="mt-2 text-sm font-semibold">
                                        {{ restaurant?.category?.name ?? '—' }}
                                    </h4>
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="flex flex-wrap gap-2">
                                <div v-for="amenity in restaurant.amenities ?? []"
                                    class="rounded-full bg-secondary px-4 py-2 text-sm font-medium">
                                    {{ amenity }}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </section>

                <!-- Last 7 Days Chart -->
                <RestaurantVoteHistoryChart v-if="stats?.chart" :data="stats.chart" />
            </div>
        </div>

        <template #sidebar>
            <!-- RECENTLY ADDED RESTAURANTS -->
            <Card v-if="nearbyRestaurants.length" class="bg-transparent shadow-none border-0 p-0 gap-2">
                <CardHeader class="p-0">
                    <CardTitle class="text-xs tracking-widest font-medium text-muted-foreground uppercase">
                        Also Nearby Restaurants
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex  flex-col gap-2 p-0">
                    <Item v-for="restaurant in nearbyRestaurants" :key="restaurant.id" variant="outline" size="sm"
                        as-child class="rounded-2xl bg-card text-card-foreground">
                        <Link :href="RestaurantController.show([restaurant.city.slug, restaurant.slug])">
                            <ItemMedia>
                                <RestaurantAvatar :restaurant="restaurant" />
                            </ItemMedia>
                            <ItemContent>
                                <ItemTitle class="line-clamp-1">{{ restaurant.name }}</ItemTitle>
                                <ItemDescription class="line-clamp-1 text-xs">
                                    {{ restaurant.city.name }} &sdot; {{ restaurant.address }}
                                </ItemDescription>
                            </ItemContent>
                            <ItemActions>
                                <!-- Distance in KMs -->
                                <small>{{ restaurant.distance.toFixed(0) }} Km</small>
                            </ItemActions>
                        </Link>
                    </Item>
                </CardContent>
            </Card>
            <Card class="overflow-hidden pt-0" v-if="restaurant.latitude && restaurant.longitude">
                <iframe width="100%" loading="lazy" class="block h-48"
                    :src="`https://maps.google.com/maps?width=100%&height=600&hl=en&q=${restaurant.latitude},${restaurant.longitude}&ie=UTF8&t=&z=14&iwloc=B&output=embed`"></iframe>
                <CardContent>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                {{ restaurant.address }}
                            </p>
                        </div>
                        <a :href="`https://www.google.com/maps?q=${restaurant.latitude},${restaurant.longitude}`" target="_blank">
                            <Button size="sm">
                                Get Directions
                            </Button>
                        </a>
                    </div>
                </CardContent>
            </Card>
            <section v-if="!restaurant.user_id && can(permissions.create_restaurant_claims)">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Flag class="h-4 w-4" /> Claim Restaurant 
                        </CardTitle>
                        <CardDescription>
                            Claiming is quick and easy. We verify ownership to ensure only legitimate
                            representatives can claim. No credit card required.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter>
                        <Button as-child class="w-full">
                            <Link :href="RestaurantController.createClaim(restaurant.slug)">Get Started</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </section>

            <!-- Share -->
            <section>
                <RestaurantShare :restaurant="restaurant" />
            </section>
        </template>
    </AppLayout>
</template>
