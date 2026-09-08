<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import type { BookExcerpt } from '@/types/catalog';

const props = withDefaults(
    defineProps<{
        excerpts: BookExcerpt[];
        intervalMs?: number;
    }>(),
    {
        intervalMs: 6000,
    },
);

const active = ref(0);
let timer: ReturnType<typeof setInterval> | null = null;

function start() {
    stop();
    if (props.excerpts.length < 2) return;
    timer = setInterval(() => {
        active.value = (active.value + 1) % props.excerpts.length;
    }, props.intervalMs);
}

function stop() {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
}

onMounted(start);
onBeforeUnmount(stop);
</script>

<template>
    <div
        class="relative min-h-[3.5em]"
        @mouseenter="stop"
        @mouseleave="start"
    >
        <Transition name="ticker-fade" mode="out-in">
            <p
                :key="active"
                class="font-serif-display text-[15px] leading-relaxed text-[var(--site-ink-faint)] italic"
            >
                &ldquo;{{ excerpts[active].quote }}&rdquo;
                <span v-if="excerpts[active].source" class="not-italic">
                    &mdash; {{ excerpts[active].source }}</span
                >
            </p>
        </Transition>
    </div>
</template>

<style scoped>
.ticker-fade-enter-active,
.ticker-fade-leave-active {
    transition: opacity 0.7s ease;
}

.ticker-fade-enter-from,
.ticker-fade-leave-to {
    opacity: 0;
}
</style>
