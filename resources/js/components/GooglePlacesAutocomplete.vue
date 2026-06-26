<script setup lang="ts">
import { loadGoogleMaps } from '@/composables/loadGoogleMaps';
import { onMounted, ref } from 'vue';

/* ---------------- PROPS ---------------- */
const props = defineProps<{
    placeholder?: string;
    country?: string;
    types?: string[];
    required?: boolean;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'select', payload: any): void;
}>();

/* ---------------- STATE ---------------- */
const inputRef = ref<HTMLInputElement | null>(null);

let autocomplete: google.maps.places.Autocomplete | null = null;
let sessionToken: google.maps.places.AutocompleteSessionToken | null = null;

const createSessionToken = () => {
    sessionToken = new google.maps.places.AutocompleteSessionToken();
};

/* ---------------- INIT ---------------- */
onMounted(async () => {
    await loadGoogleMaps();
    createSessionToken();

    autocomplete = new google.maps.places.Autocomplete(
        inputRef.value as HTMLInputElement,
        {
            types: props.types ?? ['restaurant'],
            componentRestrictions: props.country
                ? { country: props.country }
                : undefined,
            fields: [
                'place_id',
                'name',
                'formatted_address',
                'geometry.location',
                'rating',
                'user_ratings_total',
                'opening_hours',
                'international_phone_number',
                'website',
                'address_components',
                'url', // 👈 Google Maps link
            ],
            sessionToken,
        },
    );

    autocomplete.addListener('place_changed', () => {
        const p = autocomplete!.getPlace();
        console.log(p);
        if (!p.place_id || !p.geometry) return;

        const address = p.address_components ? extractAddressParts(p.address_components) : null;

        /* ---------------- BUILD PAYLOAD ---------------- */
        const payload = {
            place_id: p.place_id,
            name: p.name,
            address: p.formatted_address,
            lat: p.geometry.location.lat(),
            lng: p.geometry.location.lng(),
            rating: p.rating ?? null,
            reviews: p.user_ratings_total ?? null,
            phone: p.international_phone_number ?? null,
            website: p.website ?? null,
            country: address?.country ?? null,
            country_code: address?.country_code ?? null,
            state: address?.state ?? null,
            state_code: address?.state_code ?? null,
            city: address?.city ?? null,

            /* 🔗 LINKS */
            google_maps_url: p.url, // direct maps page
            google_reviews_url: `https://search.google.com/local/writereview?placeid=${p.place_id}`,

            /* EXTRA */
            is_open_now: p.opening_hours?.isOpen() ?? null,
        };

        emit('select', payload);

        // 🔁 Reset token after successful selection
        createSessionToken();
    });
});

function extractAddressParts(
    components: google.maps.GeocoderAddressComponent[],
) {
    const find = (type: string) =>
        components.find((c) => c.types.includes(type))?.long_name ?? null;

    return {
        country: find('country'),
        country_code:
            components.find((c) => c.types.includes('country'))?.short_name ??
            null,

        state: find('administrative_area_level_1'),
        state_code:
            components.find((c) =>
                c.types.includes('administrative_area_level_1'),
            )?.short_name ?? null,

        city:
            find('locality') ||
            find('administrative_area_level_3') ||
            find('sublocality') ||
            null,
    };
}
</script>

<template>
    <input
        ref="inputRef"
        type="text"
        :placeholder="props.placeholder ?? 'Search restaurant...'"
        :required="props.required"
        :disabled="props.disabled"
        autocomplete="off"
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:ring-2 focus:ring-ring focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
    />
</template>
