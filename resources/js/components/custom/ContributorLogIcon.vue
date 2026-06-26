<!-- components/LogIcon.vue -->
<script setup>
defineProps({ icon: String })
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

        <template v-if="icon === 'check-circle'">
            <circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>
        </template>
        <template v-else-if="icon === 'x-circle'">
            <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
        </template>
        <template v-else-if="icon === 'plus-circle'">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </template>
        <template v-else-if="icon === 'minus-circle'">
            <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
        </template>
        <template v-else-if="icon === 'home'">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </template>
        <template v-else-if="icon === 'file-plus'">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
        </template>
        <template v-else-if="icon === 'star'">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </template>
        <template v-else-if="icon === 'arrow-up-circle'">
            <circle cx="12" cy="12" r="10"/><polyline points="16 12 12 8 8 12"/><line x1="12" y1="16" x2="12" y2="8"/>
        </template>

    </svg>
</template>