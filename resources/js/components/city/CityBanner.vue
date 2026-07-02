<script setup lang="ts">
import { useDialog } from '@/composables/useDialog';
import { Card } from '../ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import CardContent from '../ui/card/CardContent.vue';
import TodaysGrowthPercentage from '../custom/TodaysGrowthPercentage.vue';
import { Button } from '../ui/button';
import { Label } from '../ui/label';
import { Input } from '../ui/input';
import InputError from '../InputError.vue';
import { Spinner } from '../ui/spinner';
import { Deferred, Form } from '@inertiajs/vue3';
import CityController from '@/actions/App/Http/Controllers/CityController';
import { ArrowUpRightIcon } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '../ui/avatar';
import { Skeleton } from '../ui/skeleton';
const [isOpen, closeDialog] = useDialog();

defineProps({
    city: Object,
    allTimeVotesToday: Number,
    waitlistCount: Number
})
</script>

<template>
    <Card class="overflow-hidden pt-0">
        <div class="relative h-40 w-full overflow-hidden md:h-48">
            <img :src="city.banner_url" :alt="city.name"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                :class="city.is_live ? '' : 'opacity-50 grayscale'" />
            <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/20 to-transparent"></div>

            <div class="absolute top-4 right-4">
                <div v-if="city.is_live"
                    class="flex items-center gap-1.5 rounded-full bg-primary/50 text-primary-foreground px-3 py-1 backdrop-blur-sm">
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></div>
                    <span class="text-xs font-medium text-white">LIVE</span>
                </div>
                <div v-else
                    class="flex items-center gap-1.5 rounded-full bg-primary/50 text-primary-foreground px-3 py-1 backdrop-blur-sm">
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-primary replace"></div>
                    <span class="text-xs font-medium text-white">SOON</span>
                </div>
            </div>

            <div class="absolute bottom-4 left-6">
                <h1 class="text-2xl font-bold text-white md:text-3xl">
                    {{ city.name }}
                </h1>
                <p class="mt-0.5 text-sm text-white">
                    {{ city.state.name }}
                </p>
            </div>
        </div>

        <!-- Stats Section -->
        <CardContent>
            <div>
                <div v-if="city.is_live" class="flex items-center gap-6 justify-between">
                    <div>
                        <p class="sm:text-xs text-[10px] tracking-wide uppercase ">
                            Restaurants
                        </p>
                        <p class="font-display text-lg sm:text-2xl font-bold ">
                            {{ city.restaurants_count }}
                        </p>
                    </div>

                    <div>
                        <p class="text-right sm:text-xs text-[10px] tracking-wide uppercase truncate">
                            {{ allTimeVotesToday.toLocaleString() }} votes today
                        </p>
                        <p class="font-display text-lg sm:text-2xl font-bold">
                            <TodaysGrowthPercentage :growth_percentage="city.growth_percentage" />
                        </p>
                    </div>
                </div>
                <div v-else class="fade-3">
                    <div>
                        <div class="flex items-center gap-2 ">
                            <span class="text-primary text-xl font-semibold uppercase tracking-wide">
                                Launching Soon 🚀
                            </span>
                        </div>
                        <p class="text-sm leading-relaxed text-gray-500 ">
                            Make your voice heard! Join the waitlist to help us prioritize launching in {{ city.name
                            }}.
                        </p>

                        <!-- Footer -->
                        <div class="mt-5 space-y-4">
                            <div class="flex flex-wrap gap-3">
                                <!-- Join Waitlist -->
                                <Dialog v-model:open="isOpen">
                                    <DialogTrigger as-child>
                                        <Button>
                                            Join Waitlist
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="bg-card text-card-foreground">
                                        <Form method="post" :action="CityController.storeWishlist([city.slug])"
                                            @success="closeDialog" :reset-on-success="['email', 'name']"
                                            v-slot="{ errors, processing }" class="flex flex-col gap-6">
                                            <DialogHeader>
                                                <DialogTitle>
                                                    Join Waitlist
                                                </DialogTitle>
                                                <DialogDescription>
                                                    Be the first to know when we launch in {{ city.name }}!
                                                </DialogDescription>
                                            </DialogHeader>

                                            <div class="grid gap-6">
                                                <div class="grid gap-2">
                                                    <Label for="name">Name</Label>
                                                    <Input id="name" type="text" name="name" required autofocus
                                                        autocomplete="name" placeholder="John Doe" />
                                                    <InputError :message="errors.name" />
                                                </div>
                                                <div class="grid gap-2">
                                                    <Label for="email">Email address</Label>
                                                    <Input id="email" type="email" name="email" required autofocus
                                                        autocomplete="email" placeholder="email@example.com" />
                                                    <InputError :message="errors.email" />
                                                </div>
                                            </div>

                                            <DialogFooter>
                                                <Button type="submit" class="mt-4">
                                                    <Spinner v-if="processing" />
                                                    Submit
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>

                                </Dialog>

                                <!-- Restaurant CTA -->
                                <Button variant="outline" as-child>
                                    <a href="/partner/restaurants/create" target="_blank"
                                        class="flex items-center justify-center gap-1.5">
                                        Add Restaurant
                                        <ArrowUpRightIcon class="h-4 w-4" />
                                    </a>
                                </Button>
                            </div>

                            <!-- Waitlist Row -->
                            <div class="text-left" v-if="waitlistCount > 0">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="*:data-[slot=avatar]:ring-background flex -space-x-2 *:data-[slot=avatar]:ring-2">
                                        <Avatar>
                                            <AvatarImage src="https://i.pravatar.cc/150?img=13" alt="@shadcn" />
                                            <AvatarFallback>CN</AvatarFallback>
                                        </Avatar>
                                        <Avatar>
                                            <AvatarImage src="https://i.pravatar.cc/150?img=24" alt="@leerob" />
                                            <AvatarFallback>LR</AvatarFallback>
                                        </Avatar>
                                        <Avatar>
                                            <AvatarImage src="https://i.pravatar.cc/150?img=33" alt="@evilrabbit" />
                                            <AvatarFallback>ER</AvatarFallback>
                                        </Avatar>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        <strong>
                                            {{ waitlistCount == 1 ? waitlistCount.toLocaleString() + ' person' :
                                                waitlistCount.toLocaleString() + '+ people' }}
                                        </strong> have already joined the
                                        waitlist
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>