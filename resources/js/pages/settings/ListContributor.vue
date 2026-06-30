<script setup>
import { Form, Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import moment from 'moment';
import Button from '@/components/ui/button/Button.vue';

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item'
import { Card, CardFooter, CardHeader } from '@/components/ui/card';
import CardContent from '@/components/ui/card/CardContent.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { EllipsisVerticalIcon, ExternalLinkIcon, LogOut, Trash, TrashIcon } from 'lucide-vue-next';
import { Line } from '@unovis/ts';
import { community_status } from '@/constants/status';
import ContributorController from '@/actions/App/Http/Controllers/ContributorController';

defineProps({
    joinedCommunities: Array
});

</script>

<template>
    <AppLayout :sidebar="false">

        <Head title="Joined Communities" />

        <h1 class="sr-only">Joined Communities</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Card v-if="!joinedCommunities.length" class="text-center">
                    <CardContent>
                        <div class="text-5xl">🏅</div>
                        <h2 class="mt-4 text-xl font-semibold">
                            Join Your First Community
                        </h2>
                        <p class="mx-auto mt-2 max-w-md text-muted-foreground">
                            Help discover restaurants in your city and become one of founding contributors.
                        </p>
                        <Button class="mt-6" as-child>
                            <Link :href="ContributorController.create()" target="_blank">Join Now</Link>
                        </Button>
                    </CardContent>
                </Card>
                <div v-else class="flex items-center justify-between">
                    <Heading variant="small" title="Joined Cities" description="Manage cities that you have joined" />
                    <Button size="sm" variant="link" as-child>
                        <a href="/contributor" target="_blank">Dashboard
                            <ExternalLinkIcon class="ml-2 h-4 w-4" />
                        </a>
                    </Button>
                </div>

                <section class="flex flex-col gap-3" >
                    <Link :href="ContributorController.show([community?.id])" v-for="community in joinedCommunities" :key="community?.id">
                        <Item variant="outline" class="bg-card">
                            <ItemMedia>
                                <Avatar class="size-10">
                                    <AvatarImage :src="community.city.logo_url" :alt="community.city.name" />
                                    <AvatarFallback>
                                        {{ community.city.name.charAt(0) }}
                                    </AvatarFallback>
                                </Avatar>
                            </ItemMedia>
                            <ItemContent>
                                <ItemTitle>{{ community.city.name }}</ItemTitle>
                                <ItemDescription>
                                    <span v-if="community.status == community_status.Approved">Contributor since {{ moment(community.created_at).fromNow() }}</span>
                                    <span v-else-if="community.status == community_status.Pending">Joined {{ moment(community.created_at).fromNow() }}</span>
                                    <span v-else-if="community.status == community_status.Rejected">{{ community.reason }}</span>
                                </ItemDescription>
                            </ItemContent>
                            <ItemActions> 
                                <Badge :class="{
                                    'bg-green-800': community.status == community_status.Approved,
                                    'bg-red-800': community.status == community_status.Rejected,
                                    'bg-yellow-600': community.status == community_status.Pending,
                                }">
                                    {{ community.status }}
                                </Badge>
                            </ItemActions>
                        </Item>
                    </Link>
                </section>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
