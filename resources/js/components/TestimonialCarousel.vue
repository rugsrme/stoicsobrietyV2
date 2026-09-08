<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';

export type Testimonial = {
    quote: string;
    author: string;
    context?: string;
};

const props = withDefaults(
    defineProps<{
        testimonials: Testimonial[];
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

function next() {
    goTo((active.value + 1) % props.testimonials.length);
}

function prev() {
    goTo((active.value - 1 + props.testimonials.length) % props.testimonials.length);
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
        <div class="flex w-full items-center justify-center gap-3 sm:gap-5">
            <button
                type="button"
                class="shrink-0 rounded-full border border-[var(--site-line)] p-2 text-[var(--site-ink-faint)] transition-colors hover:text-[var(--site-ink)] disabled:pointer-events-none disabled:opacity-30"
                :disabled="testimonials.length < 2"
                aria-label="Previous testimonial"
                @click="prev"
            >
                <ChevronLeft class="size-4" />
            </button>

            <div class="relative w-full flex-1 overflow-hidden">
                <Transition name="quote-fade" mode="out-in">
                    <blockquote
                        :key="active"
                        class="flex min-h-[160px] flex-col items-center justify-center rounded border border-[var(--site-line)] bg-[var(--site-card)] px-7 py-8 text-center sm:px-9 sm:py-[34px]"
                    >
                        <p
                            class="font-serif-display text-lg leading-relaxed text-[var(--site-ink)]"
                        >
                            &ldquo;{{ testimonials[active].quote }}&rdquo;
                        </p>
                        <footer
                            class="mt-4 text-[13.5px] text-[var(--site-ink-faint)]"
                        >
                            {{ testimonials[active].author
                            }}<span v-if="testimonials[active].context">
                                &mdash; {{ testimonials[active].context }}</span
                            >
                        </footer>
                    </blockquote>
                </Transition>
            </div>

            <button
                type="button"
                class="shrink-0 rounded-full border border-[var(--site-line)] p-2 text-[var(--site-ink-faint)] transition-colors hover:text-[var(--site-ink)] disabled:pointer-events-none disabled:opacity-30"
                :disabled="testimonials.length < 2"
                aria-label="Next testimonial"
                @click="next"
            >
                <ChevronRight class="size-4" />
            </button>
        </div>

        <div v-if="testimonials.length > 1" class="mt-5 flex gap-2">
            <button
                v-for="(t, i) in testimonials"
                :key="i"
                type="button"
                class="h-2 w-2 rounded-full transition-colors"
                :class="i === active ? 'bg-[var(--site-accent)]' : 'bg-[var(--site-line)]'"
                :aria-label="`Go to testimonial ${i + 1}`"
                @click="goTo(i)"
            />
        </div>
    </div>
</template>

<style scoped>
.quote-fade-enter-active,
.quote-fade-leave-active {
    transition: opacity 0.5s ease;
}

.quote-fade-enter-from,
.quote-fade-leave-to {
    opacity: 0;
}
</style>
