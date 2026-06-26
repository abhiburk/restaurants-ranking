<script setup lang="ts">
import FileUpload from '@/components/FileUpload.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import { Input } from '@/components/ui/input';
import {
    InputGroup,
    InputGroupAddon,
    InputGroupButton,
    InputGroupInput,
} from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Form, Link } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { CheckIcon, CopyIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import Select from '../ui/select/Select.vue';
import SelectTrigger from '../ui/select/SelectTrigger.vue';
import SelectValue from '../ui/select/SelectValue.vue';
import SelectContent from '../ui/select/SelectContent.vue';
import SelectGroup from '../ui/select/SelectGroup.vue';
import SelectLabel from '../ui/select/SelectLabel.vue';
import SelectItem from '../ui/select/SelectItem.vue';
import ManageRestaurantController from '@/actions/App/Http/Controllers/Restaurant/ManageRestaurantController';

defineProps({
    restaurant: {
        type: Object,
        required: false,
    },
    cities: {
        type: Array
    },
    categories: {
        type: Array
    },
});
const source = ref('');
const { text, copy, copied, isSupported } = useClipboard({ source });
</script>
<template>
    <Form
        :action="
            restaurant?.id
                ? ManageRestaurantController.update(restaurant?.id)
                : ManageRestaurantController.store()
        "
        :transform="
            (data) => ({
                ...data,
                google_place_id: restaurant?.google_place_id,
                google_rating: restaurant?.google_rating,
                google_reviews: restaurant?.google_reviews,
                google_reviews_url: restaurant?.google_reviews_url,
                google_maps_url: restaurant?.google_maps_url,
                is_active: data.is_active ? 1 : 0,
                is_default: data.is_default ? 1 : 0,
            })
        "
        disableWhileProcessing
        className="inert:opacity-50 inert:pointer-events-none"
        v-slot="{ errors, processing, reset }"
        multipart
    >
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle> Basic Information </CardTitle>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Restaurant Name *</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    :defaultValue="restaurant?.name"
                                    placeholder="The Golden Spoon"
                                    required
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <Textarea
                                    id="description"
                                    name="description"
                                    :defaultValue="restaurant?.description"
                                    placeholder="Describe your restaurant..."
                                    rows="4"
                                />
                                <InputError :message="errors.description" />
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="city_id">Choose your restaurant city</Label>
                                <Select
                                    id="city_id"
                                    name="city_id"
                                    :defaultValue="parseInt(restaurant?.city_id)"
                                >
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select a city"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Cities</SelectLabel>
                                            <SelectItem
                                                :value="city.id"
                                                v-for="city in cities"
                                                :key="city.id"
                                            >
                                                {{ city.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.city_id" />
                            </div>
                            <div class="space-y-2">
                                <Label for="category_id">Category</Label>
                                <Select
                                    id="category_id"
                                    name="category_id"
                                    :defaultValue="restaurant?.category_id"
                                >
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select a category"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Categories</SelectLabel>
                                            <SelectItem
                                                :value="category.id"
                                                v-for="category in categories"
                                                :key="category.id"
                                            >
                                                {{ category.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.category_id" />
                            </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Contact Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Contact Information</CardTitle>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        :defaultValue="restaurant?.email"
                                        placeholder="contact@restaurant.com"
                                    />
                                    <InputError :message="errors.email" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="phone">Phone</Label>
                                    <Input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        :defaultValue="restaurant?.phone"
                                        placeholder="+1 (555) 123-4567"
                                    />
                                    <InputError :message="errors.phone" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="website_url">Website URL</Label>
                                <Input
                                    id="website_url"
                                    type="url"
                                    name="website_url"
                                    :defaultValue="restaurant?.website_url"
                                    placeholder="https://www.restaurant.com"
                                />
                                <InputError :message="errors.website_url" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Address -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Address</CardTitle>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="address">Street Address</Label>
                                <Textarea
                                    id="address"
                                    name="address"
                                    :defaultValue="restaurant?.address"
                                    placeholder="123 Main Street"
                                    rows="2"
                                />
                                <InputError :message="errors.address" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="city">City</Label>
                                    <Input
                                        id="city"
                                        name="city"
                                        :defaultValue="restaurant?.city"
                                        placeholder="New York"
                                    />
                                    <InputError :message="errors.city" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="state">State</Label>
                                    <Input
                                        id="state"
                                        name="state"
                                        :defaultValue="restaurant?.state"
                                        placeholder="NY"
                                    />
                                    <InputError :message="errors.state" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="country">Country</Label>
                                    <Input
                                        id="country"
                                        name="country"
                                        :defaultValue="restaurant?.country"
                                        placeholder="India"
                                    />
                                    <InputError :message="errors.country" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="postal_code">Postal Code</Label>
                                    <Input
                                        id="postal_code"
                                        name="postal_code"
                                        :defaultValue="restaurant?.postal_code"
                                        placeholder="10001"
                                    />
                                    <InputError :message="errors.postal_code" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="latitude">Latitude</Label>
                                    <Input
                                        id="latitude"
                                        type="text"
                                        name="latitude"
                                        :defaultValue="restaurant?.latitude"
                                        placeholder="40.7128"
                                    />
                                    <InputError :message="errors.latitude" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="longitude">Longitude</Label>
                                    <Input
                                        id="longitude"
                                        type="text"
                                        name="longitude"
                                        :defaultValue="restaurant?.longitude"
                                        placeholder="-74.0060"
                                    />
                                    <InputError :message="errors.longitude" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Configuration Sidebar -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <Card>
                        <CardContent class="space-y-4">
                            <FileUpload
                                :existing-logo="restaurant?.logo_url"
                                name="logo"
                                @remove="ManageRestaurantController.removeLogo(restaurant?.id)"
                            />
                        </CardContent>
                    </Card>
                    <!-- Google Integration -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Google Integration</CardTitle>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="google_maps_url"
                                    >Google Maps URL</Label
                                >
                                <Input
                                    id="google_maps_url"
                                    type="url"
                                    name="google_maps_url"
                                    :defaultValue="restaurant?.google_maps_url"
                                    placeholder="https://maps.google.com/..."
                                />
                                <InputError :message="errors.google_maps_url" />
                            </div>

                            <div class="space-y-2">
                                <Label for="google_reviews_url"
                                    >Google Reviews URL</Label
                                >
                                <p class="text-xs text-muted-foreground">
                                    Link to the Google Reviews page for your
                                    restaurant.
                                </p>
                                <InputGroup>
                                    <InputGroupInput
                                        id="google_reviews_url"
                                        type="url"
                                        name="google_reviews_url"
                                        disabled
                                        :defaultValue="
                                            restaurant?.google_reviews_url
                                        "
                                        placeholder="https://search.google.com/local/reviews..."
                                    />
                                    <InputGroupAddon
                                        align="inline-end"
                                        v-if="isSupported"
                                    >
                                        <InputGroupButton
                                            type="button"
                                            aria-label="Copy"
                                            title="Copy"
                                            @click="
                                                copy(
                                                    restaurant?.google_reviews_url,
                                                )
                                            "
                                        >
                                            <CheckIcon v-if="copied" />
                                            <CopyIcon v-if="!copied" />
                                        </InputGroupButton>
                                    </InputGroupAddon>
                                </InputGroup>
                                <InputError
                                    :message="errors.google_reviews_url"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Settings</CardTitle>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div class="w-full space-y-2">
                                <div class="flex justify-between gap-3">
                                    <div class="grid gap-2">
                                        <Label for="is_active">Active</Label>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Manage whether the restaurant is
                                            currently active and visible to
                                            customers.
                                        </p>
                                    </div>
                                    <Switch
                                        id="is_active"
                                        name="is_active"
                                        value="1"
                                        :default-value="restaurant?.is_active"
                                    />
                                </div>
                                <InputError :message="errors.is_active" />
                            </div>

                            <div class="w-full space-y-2">
                                <div class="flex justify-between gap-3">
                                    <div class="grid gap-2">
                                        <Label for="is_default">Default</Label>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Set this restaurant as default for
                                            the dashboard
                                        </p>
                                    </div>
                                    <Switch
                                        id="is_default"
                                        name="is_default"
                                        value="1"
                                        :default-value="restaurant?.is_default"
                                    />
                                </div>
                                <InputError :message="errors.is_default" />
                            </div>
                        </CardContent>
                    </Card>

                    <Separator />
                    <div class="flex justify-between space-y-3">
                        <Link as="button" href="/dashboard">
                            <Button variant="outline">Cancel</Button>
                        </Link>
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            {{ restaurant?.id ? 'Update' : 'Create' }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </Form>
</template>
