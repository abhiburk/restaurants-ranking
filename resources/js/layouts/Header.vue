<script setup lang="ts">
import CityController from '@/actions/App/Http/Controllers/CityController';
import DiscoverController from '@/actions/App/Http/Controllers/DiscoverController';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { Button } from '@/components/ui/button';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useAppearance } from '@/composables/useAppearance';
import { LogOut, Moon, MoonIcon } from 'lucide-vue-next';
import { logout } from '@/routes';
const showStatusBar = ref(true);
const showActivityBar = ref(false);
const showPanel = ref(false);
const position = ref('bottom');

const page = usePage();
const appName = computed(() => page.props.name);

const { appearance, updateAppearance } = useAppearance();

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <header class="fade-1 mb-6 rounded-2xl border bg-card text-card-foreground shadow-sm">
        <div class="mx-auto w-full max-w-6xl px-4 py-3 ">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <Link href="/">
                    <div class="flex items-center gap-2">
                        <!-- <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center shadow-sm">
                            <span class="text-white font-bold text-sm">FR</span>
                        </div> -->
                        <span class="text-lg font-bold text-primary dark:text-white">{{
                            appName
                            }}</span>
                    </div>
                </Link>

                <div class="flex items-center gap-2">
                    <div>
                        <Button size="sm" variant="ghost"
                            @click="updateAppearance(appearance == 'dark' ? 'light' : 'dark')">
                            <Moon />
                        </Button>
                    </div>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="link" size="sm">
                                <Avatar>
                                    <AvatarImage src="https://github.com/shadcn.png" />
                                    <AvatarFallback>CN</AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56" align="start">
                            <DropdownMenuLabel>My Account</DropdownMenuLabel>
                            <DropdownMenuGroup>
                                <DropdownMenuItem>
                                    Profile
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <Link :href="CityController.index()">Cities</Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <Link :href="CityController.comingSoonCities()">Cities (Comming Soon)</Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    Settings
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <Link :href="DiscoverController.howItWorks()">How it works</Link>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem>
                                Help
                            </DropdownMenuItem>
                            <DropdownMenuItem v-if="page.props.auth.user">
                                <Link :href="logout()" @click="handleLogout" as="button" data-test="logout-button">
                                    <!-- <LogOut class="mr-2 h-4 w-4" /> -->
                                    Log out
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>
    </header>
</template>
;
