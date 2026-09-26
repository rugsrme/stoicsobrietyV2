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
    <!--
        Every excerpt sits in the same grid cell, so the box is always as tall
        as the longest one and the content below never jumps as they rotate.
    -->
    <div class="grid" @mouseenter="stop" @mouseleave="start">
        <p
            v-for="(excerpt, i) in excerpts"
            :key="i"
            class="ticker-item font-serif-display col-start-1 row-start-1 text-[15px] leading-relaxed text-[var(--site-ink-faint)] italic"
            :class="i === active ? 'opacity-100' : 'opacity-0'"
            :aria-hidden="i !== active"
        >
            &ldquo;{{ excerpt.quote }}&rdquo;
            <span v-if="excerpt.source" class="not-italic">
                &mdash; {{ excerpt.source }}</span
            >
        </p>
    </div>
</template>

<style scoped>
.ticker-item {
    transition: opacity 0.7s ease;
}

@media (prefers-reduced-motion: reduce) {
    .ticker-item {
        transition: none;
    }
}
</style>
