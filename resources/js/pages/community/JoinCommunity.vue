<script setup lang="ts">
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController';
import InputError from '@/components/InputError.vue';
import RestaurantAvatar from '@/components/restaurant/RestaurantAvatar.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Form, usePage } from '@inertiajs/vue3';
import { CheckCircleIcon, UserIcon } from 'lucide-vue-next';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input'
import { computed, ref } from "vue"
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import CommunityController from '@/actions/App/Http/Controllers/CommunityController';

const page = usePage();
const user = computed(() => page.props.auth.user);

defineProps({
    activeCities: Array,
});

const modelValue = ref([]);
const formRef = ref();

function handleSuccess() {
    modelValue.value = [];
}
</script>

<template>
    <AppLayout :sidebar="false">
        <Form :ref="formRef" class="space-y-5 h-[calc(100vh-8rem)] flex items-center justify-center" method="post" #default="{ progress, processing, errors }"
            :options="{ preserveScroll: true }" 
            reset-on-success
            @success="handleSuccess"
            :action="CommunityController.store()">
            <Card class="w-full max-w-xl">
                <CardHeader class="flex items-start gap-4">
                    <UserIcon class="h-15 w-15" />
                    <div class="grid gap-3">
                        <CardTitle>
                            Join the community
                        </CardTitle>

                        <CardDescription>
                            Join the FoodRank community to access exclusive features and support the local food scene.
                        </CardDescription>
                    </div>
                </CardHeader>

                <CardContent>
                    <div class="space-y-5">
                        <!-- City -->
                        <div class="grid gap-2">
                            <Label>Select Desired City</Label>
                            <Select name="city_id" required>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a City" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Cities</SelectLabel>
                                        <SelectItem :value="city.id" v-for="city in activeCities" :key="city.id">
                                            {{ city.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.city_id" />
                        </div>

                        <!-- Notes -->
                        <div class="grid gap-2">
                            <Label>Why do you want to join?</Label>
                            <Textarea rows="4" placeholder="Anything you'd like us to know..." name="motivation" required></Textarea>
                            <InputError :message="errors.motivation" />
                        </div>
                    </div>
                </CardContent>

                <CardFooter class="flex justify-between items-center gap-4">
                    <p class="max-w-md text-xs leading-relaxed text-muted-foreground">
                        By joining the community, you agree to our terms and conditions. You can leave the community at any time. 
                    </p>
                    <Button size="lg" type="submit" :disabled="processing"
                        class="tracking-wide transition-all duration-150 hover:-translate-y-0.5 active:scale-95">
                        <Spinner class="animate-spin" v-if="processing" />
                        Submit
                    </Button>
                </CardFooter>
            </Card>
        </Form>

        <!-- <Card>
            <CardContent class="p-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                    <CheckCircleIcon class="h-8 w-8 text-green-700" />
                </div>
                <h2 class="mt-5 text-xl font-semibold">
                    Application Received
                </h2>
                <p class="mt-2 text-muted-foreground">
                    We'll review your application and let you know once contributor access is approved.
                </p>
            </CardContent>
        </Card> -->
    </AppLayout>
</template>