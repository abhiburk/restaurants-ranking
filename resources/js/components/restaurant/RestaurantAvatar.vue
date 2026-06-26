<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '../ui/avatar';

defineProps({
    restaurant: Object
});
</script>
<template>
    <div>
        <Avatar class="size-10">
            <AvatarImage :src="restaurant.logo_url" :alt="restaurant.name" />
            <AvatarFallback class="bg-cream-darker">{{ restaurant.name.charAt(0) }}</AvatarFallback>
        </Avatar>
    </div>
</template>
