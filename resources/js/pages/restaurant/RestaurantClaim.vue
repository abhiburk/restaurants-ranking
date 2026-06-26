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
import { FileTextIcon, StoreIcon, UploadIcon, XIcon } from 'lucide-vue-next';

import { computed, ref } from "vue"

const page = usePage();
const user = computed(() => page.props.auth.user);

const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)

const handleFileChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]

    if (file) {
        selectedFile.value = file
    }
}

const removeFile = () => {
    selectedFile.value = null

    if (fileInput.value) {
        fileInput.value.value = ""
    }
}

defineProps({
    restaurant: Object,
});

</script>

<template>
    <AppLayout :sidebar="false">
        <Form class="space-y-5 flex items-center justify-center" method="post"
            :action="RestaurantController.storeClaim(restaurant.slug)" #default="{ progress, processing, errors }"
            :options="{ preserveScroll: true }">
            <Card class="w-full max-w-xl">
                <CardHeader class="flex items-start gap-4">
                    <StoreIcon class="h-15 w-15" />
                    <div class="grid gap-3">
                        <CardTitle>
                            Claim your restaurant
                        </CardTitle>

                        <CardDescription>
                            Verify your ownership to manage your restaurant
                            profile, access rankings and unlock analytics on
                            FoodRank.
                        </CardDescription>
                    </div>
                </CardHeader>

                <CardContent>
                    <!-- Restaurant Preview -->
                    <div
                        class="flex flex-col gap-3 rounded-2xl border border-border bg-secondary p-4 text-secondary-foreground sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <RestaurantAvatar :restaurant="restaurant" />
                            <div>
                                <h3 class="font-semibold text-foreground">
                                    {{ restaurant.name }}
                                </h3>

                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ restaurant.city.name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="mt-8 space-y-5">
                        <!-- Owner Name -->
                        <div class="grid gap-2">
                            <Label>Your Name</Label>
                            <Input type="text" placeholder="Enter your full name" name="name"
                                :default-value="user.name" />
                            <InputError :message="errors.name" />
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col gap-5 sm:flex-row">
                            <div class="grid gap-2 w-full">
                                <Label>Email Address</Label>
                                <Input type="email" placeholder="owner@restaurant.com" name="email"
                                    :default-value="user.email" />
                                <InputError :message="errors.email" />
                            </div>
                            <div class="grid gap-2 w-full">
                                <Label>Business Phone</Label>
                                <Input type="text" placeholder="+91 98765 43210" name="phone" />
                                <InputError :message="errors.phone" />
                            </div>
                        </div>

                        <!-- Proof -->
                        <div class="grid gap-2">
                            <Label>Verification Proof</Label>
                            <div class="rounded-2xl border border-dashed border-border bg-secondary/30 p-5">
                                <input ref="fileInput" type="file" name="document" hidden accept=".pdf,.jpg,.jpeg,.png"
                                    @change="handleFileChange" />
                                <div v-if="!selectedFile" class="flex flex-col items-center justify-center text-center">
                                    <UploadIcon class="h-6 w-6 text-muted-foreground" />

                                    <p class="mt-3 text-sm font-medium text-foreground">
                                        Upload business proof
                                    </p>

                                    <p class="mt-1 text-xs text-muted-foreground">
                                        GST certificate, business license,
                                        utility bill or restaurant proof
                                    </p>

                                    <p class="mt-1 text-xs text-muted-foreground">
                                        (Accepted formats: PDF, JPG, PNG. Max size: 5MB)
                                    </p>

                                    <Button type="button" variant="outline" class="mt-4" @click="fileInput?.click()">
                                        Choose File
                                    </Button>

                                    <InputError :message="errors.document" />
                                </div>
                                <div v-else class="flex items-center justify-between gap-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-background">
                                            <FileTextIcon class="h-5 w-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="ellipsis text-sm font-medium text-foreground">
                                                {{ selectedFile.name }}
                                            </p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ (selectedFile.size / 1024 / 1024).toFixed(2) }} MB
                                            </p>
                                            <progress v-if="progress" class="mt-2 w-full" :value="progress.percentage"
                                                max="100">
                                                {{ progress.percentage }}%
                                            </progress>
                                        </div>
                                    </div>
                                    <Button type="button" variant="ghost" size="icon" @click="removeFile">
                                        <XIcon class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="grid gap-2">
                            <Label>Additional Notes</Label>
                            <Textarea rows="4" placeholder="Anything you'd like us to know..." name="notes"></Textarea>
                            <InputError :message="errors.notes" />
                        </div>
                    </div>
                </CardContent>

                <CardFooter class="flex justify-between items-center gap-4">
                    <p class="max-w-md text-xs leading-relaxed text-muted-foreground">
                        Our team manually reviews every ownership request before
                        approval. Verification usually takes less than 24 hours.
                    </p>
                    <Button size="lg" type="submit" :disabled="processing"
                        class="tracking-wide transition-all duration-150 hover:-translate-y-0.5 active:scale-95">
                        <Spinner class="animate-spin" v-if="processing" />
                        Submit
                    </Button>
                </CardFooter>
            </Card>
        </Form>
    </AppLayout>
</template>