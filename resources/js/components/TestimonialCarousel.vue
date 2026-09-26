<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

export type Testimonial = {
    quote: string;
    author: string;
    context?: string | null;
};

const props = withDefaults(
    defineProps<{
        testimonials: Testimonial[];
        intervalMs?: number;
    }>(),
    {
        intervalMs: 6500,
    },
);

const active = ref(0);
let timer: ReturnType<typeof setInterval> | null = null;

function start() {
    stop();
    if (props.testimonials.length < 2) return;
    timer = setInterval(() => {
        active.value = (active.value + 1) % props.testimonials.length;
    }, props.intervalMs);
}

function stop() {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
}

function goTo(index: number) {
    active.value = index;
    start(); // restart the clock so a manual click isn't immediately overridden
}

onMounted(start);
onBeforeUnmount(stop);
</script>

<template>
    <div
        class="mx-auto flex w-full max-w-2xl flex-col items-center"
        @mouseenter="stop"
        @mouseleave="start"
    >
        <span
            class="font-serif-display mb-[18px] block text-[56px] leading-[0.6] text-[var(--site-accent)] opacity-50"
            aria-hidden="true"
            >&ldquo;</span
        >

        <!-- All quotes share one grid cell so the height never jumps. -->
        <div class="grid w-full">
            <blockquote
                v-for="(t, i) in testimonials"
                :key="i"
                class="quote-item col-start-1 row-start-1 px-3 py-2 text-center"
                :class="
                    i === active
                        ? 'opacity-100'
                        : 'pointer-events-none opacity-0'
                "
                :aria-hidden="i !== active"
            >
                <p
                    class="font-serif-display text-xl leading-normal text-balance text-[var(--site-ink)] sm:text-2xl"
                >
                    {{ t.quote }}
                </p>
                <footer
                    class="mt-[18px] text-[13.5px] text-[var(--site-ink-faint)]"
                >
                    {{ t.author
                    }}<span v-if="t.context"> &mdash; {{ t.context }}</span>
                </footer>
            </blockquote>
        </div>

        <div v-if="testimonials.length > 1" class="mt-6 flex gap-2">
            <button
                v-for="(t, i) in testimonials"
                :key="i"
                type="button"
                class="h-2 w-2 rounded-full transition-colors"
                :class="
                    i === active
                        ? 'bg-[var(--site-accent)]'
                        : 'bg-[var(--site-line)]'
                "
                :aria-label="`Show review ${i + 1}`"
                :aria-current="i === active"
                @click="goTo(i)"
            />
        </div>
    </div>
</template>

<style scoped>
.quote-item {
    transition: opacity 0.6s ease;
}

@media (prefers-reduced-motion: reduce) {
    .quote-item {
        transition: none;
    }
}
</style>
