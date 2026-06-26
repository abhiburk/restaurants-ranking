<script setup lang="ts">
import { ref } from "vue"
import { StarIcon } from "lucide-vue-next"

const rating = ref(0)

const setRating = (value: number) => {
    rating.value = value
}
</script>

<template>
    <div class="flex items-center gap-2">
        <button
            v-for="star in 5"
            :key="star"
            type="button"
            @click="setRating(star)"
            class="group transition-transform active:scale-95"
        >
            <div
                class="flex h-11 w-11 items-center justify-center rounded-2xl border transition"
                :class="
                    star <= rating
                        ? 'border-yellow-300 bg-yellow-50 text-yellow-500 dark:border-yellow-900/40 dark:bg-yellow-950/30'
                        : 'border-border bg-background text-muted-foreground hover:border-yellow-200 hover:bg-yellow-50/50'
                "
            >
                <StarIcon
                    class="h-5 w-5"
                    :fill="star <= rating ? 'currentColor' : 'none'"
                />
            </div>
        </button>
        <input type="hidden" name="rating" :value="rating">
    </div>
</template>