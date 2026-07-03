<template>
    <NumberFlowGroup>
        <div :class="class">
            <NumberFlow :trend="-1" :value="formattedTime.hours" :format="{ minimumIntegerDigits: 2 }" />
            <NumberFlow prefix=":" :trend="-1" :value="formattedTime.minutes" :digits="{ 1: { max: 5 } }"
                :format="{ minimumIntegerDigits: 2 }" />
            <NumberFlow prefix=":" :trend="-1" :value="formattedTime.seconds" :digits="{ 1: { max: 5 } }"
                :format="{ minimumIntegerDigits: 2 }" />
        </div>
    </NumberFlowGroup>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import moment from 'moment';
import NumberFlow, { NumberFlowGroup } from '@number-flow/vue'
defineProps({
    class: {
        type: String,
        default: 'leading-none font-semibold tabular-nums text-foreground'
    }
});

// Reactive state
const currentTime = ref(moment());
let timer = null;

// Calculate time until midnight - NOW uses currentTime.value instead of moment()
const timeUntilMidnight = computed(() => {
    const now = currentTime.value; // Use the reactive currentTime
    const midnight = moment(now).endOf('day'); // Create midnight based on current time
    const duration = moment.duration(midnight.diff(now));

    return {
        hours: Math.floor(duration.asHours()),
        minutes: duration.minutes(),
        seconds: duration.seconds(),
        totalSeconds: duration.asSeconds(),
    };
});

// Format time with leading zeros
const formattedTime = computed(() => {
    const time = timeUntilMidnight.value;
    return {
        hours: String(time.hours).padStart(2, '0'),
        minutes: String(time.minutes).padStart(2, '0'),
        seconds: String(time.seconds).padStart(2, '0'),
    };
});

// Update timer every second
const startTimer = () => {
    timer = setInterval(() => {
        currentTime.value = moment(); // Update the reactive ref
    }, 1000);
};

// Optional: Check for midnight and reset
const checkMidnight = () => {
    const secondsUntilMidnight = timeUntilMidnight.value.totalSeconds;
    if (secondsUntilMidnight <= 0) {
        console.log('Midnight reached!');
        // Add your midnight logic here
    }
};

// Watch for midnight (optional)
import { watch } from 'vue';
watch(timeUntilMidnight, (newValue) => {
    if (newValue.totalSeconds <= 0) {
        onMidnight();
    }
});

// Handle midnight event
const onMidnight = () => {
    console.log('🎉 Midnight reached! 🎉');
    // Reset any daily data, show notification, etc.
};

// Clean up timer on component unmount
onMounted(() => {
    startTimer();
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>
