<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" :class="className" v-bind="$attrs">
        <defs>
            <!-- Food-ranking brand gradient -->
            <linearGradient id="wootersGradientIcon" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#FF5A5F" />
                <stop offset="100%" stop-color="#FF8A00" />
            </linearGradient>
        </defs>

        <!-- Transparent Canvas -->
        <rect width="100%" height="100%" fill="none" />

        <!-- Compact Logo Icon Only -->
        <g transform="translate(3, 0)">
            <!-- Shield / Plate Rating Base -->
            <path d="M22,2 C34,2 44,12 44,24 C44,38 22,50 22,50 C22,50 0,38 0,24 C0,12 10,2 22,2 Z"
                fill="url(#wootersGradientIcon)" />

            <!-- Minimalist Spoon Cutout -->
            <path
                d="M22,10 C25,10 27,12 27,15 C27,18.5 24,22 23.5,25 L23.5,36 C23.5,37 22.8,37.5 22,37.5 C21.2,37.5 20.5,37 20.5,36 L20.5,25 C20,22 17,18.5 17,15 C17,12 19,10 22,10 Z"
                fill="#FFFFFF" />

            <!-- Embedded Rating Stars -->
            <polygon points="12,23 13,25 15,25 13,27 14,29 12,28 10,29 11,27 9,25 11,25" fill="#FFFFFF" opacity="0.9" />
            <polygon points="32,23 33,25 35,25 33,27 34,29 32,28 30,29 31,27 29,25 31,25" fill="#FFFFFF"
                opacity="0.9" />
        </g>
    </svg>

</template>
