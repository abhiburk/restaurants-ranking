<script setup lang="ts">
const props = defineProps<{
    env?: string
    version?: string
    branch?: string
    build?: string
}>()

const env     = props.env     ?? 'Testing'
const version = props.version ?? 'v0.1.0-beta'
const branch  = props.branch  ?? 'main'
const build   = props.build   ?? '1042'
</script>

<template>
    <div class="w-full bg-primary text-primary-foreground px-4 py-2 flex items-center justify-between gap-4 text-xs font-mono">

        <!-- Left — env badge -->
        <div class="flex items-center gap-2.5 shrink-0">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse" />
                <span class="tracking-widest uppercase text-yellow-400 font-medium">
                    {{ env }}
                </span>
            </span>
            <span class="text-neutral-600">·</span>
            <span class="text-neutral-400">{{ version }}</span>
        </div>

        <!-- Center — message -->
        <p class="text-neutral-400 text-center hidden sm:block">
            This is a test environment — data resets every 24 hours. Please do not use real data or credentials.
        </p>

        <!-- Right — branch + build -->
        <div class="flex items-center gap-2.5 shrink-0 text-neutral-500">
            <!-- <span class="hidden md:inline">
                branch: <span class="text-neutral-300">{{ branch }}</span>
            </span>
            <span class="text-neutral-700">·</span>
            <span>
                build: <span class="text-neutral-300">#{{ build }}</span>
            </span> -->
        </div>

    </div>
</template>