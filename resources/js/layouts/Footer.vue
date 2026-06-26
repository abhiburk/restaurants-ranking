<script setup lang="ts">
import DiscoverController from '@/actions/App/Http/Controllers/DiscoverController';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>
    <Card class="fade-5 sticky bottom-4 dark bg-card text-card-foreground border">
        <CardContent class="flex items-center justify-between gap-4 ">
            <div>
                <p class="text-sm font-medium">Own a restaurant?</p>
                <p class="mt-0.5 text-xs ">
                    Join {{ appName }} and get discovered today
                </p>
            </div>
            <Button as-child size="sm">
                <a href="/partner/restaurants/create" target="_blank">
                    Join now
                </a>
            </Button>
        </CardContent>
    </Card>

    <div class="fade-5 text-center text-muted-foreground text-xs">
        <Link class="hover:underline" :href="DiscoverController.about()" target="_blank">About</Link> &sdot; 
        <Link class="hover:underline" :href="DiscoverController.termsOfService()" target="_blank">Terms of Service</Link> &sdot; 
        <Link class="hover:underline" :href="DiscoverController.privacyPolicy()" target="_blank">Privacy Policy</Link> &sdot;
        <Link class="hover:underline" :href="DiscoverController.howItWorks()" target="_blank">How It Works</Link> &sdot; 
        <Link class="hover:underline" :href="DiscoverController.contactUs()" target="_blank">Contact Us</Link>
    </div>
</template>
;
