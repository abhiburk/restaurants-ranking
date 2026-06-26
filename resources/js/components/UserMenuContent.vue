<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LayoutDashboard, LogOut, Settings } from 'lucide-vue-next';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import { usePermissions } from '@/composables/usePermissions';
import { Podium, User } from '@lucide/vue';
import ContributorController from '@/actions/App/Http/Controllers/ContributorController';
const { hasRole } = usePermissions();

type Props = {
    user: User;
};

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full cursor-pointer" :href="edit()" prefetch>
                <User class="mr-2 h-4 w-4" />
                Account
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator v-if="hasRole('contributor')" />
    <DropdownMenuGroup v-if="hasRole('contributor')">
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full cursor-pointer" :href="ContributorController.index()" prefetch>
                <Podium class="mr-2 h-4 w-4" />
                Leaderboard
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator v-if="hasRole('super_admin')" />
    <DropdownMenuGroup v-if="hasRole('super_admin')">
        <DropdownMenuItem :as-child="true">
            <a class="block w-full cursor-pointer" href="/admin" target="_blank">
                <LayoutDashboard class="mr-2 h-4 w-4" />
                Dashboard
            </a>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator v-if="hasRole('partner')" />
    <DropdownMenuGroup v-if="hasRole('partner')">
        <DropdownMenuItem :as-child="true">
            <a class="block w-full cursor-pointer" href="/partner" target="_blank">
                <LayoutDashboard class="mr-2 h-4 w-4" />
                Dashboard
            </a>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator v-if="hasRole('contributor')" />
    <DropdownMenuGroup v-if="hasRole('contributor')">
        <DropdownMenuItem :as-child="true">
            <a class="block w-full cursor-pointer" href="/contributor" target="_blank">
                <LayoutDashboard class="mr-2 h-4 w-4" />
                Dashboard
            </a>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link class="block w-full cursor-pointer" :href="logout()" @click="handleLogout" as="button"
            data-test="logout-button">
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
