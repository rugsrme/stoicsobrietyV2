<script setup lang="ts">
import { Star } from '@lucide/vue';

defineProps<{
    rating: number;
    size?: string;
}>();
</script>

<template>
    <span
        class="inline-flex items-center gap-0.5"
        role="img"
        :aria-label="`Rated ${rating} out of 5`"
    >
        <Star
            v-for="n in 5"
            :key="n"
            :class="[
                size ?? 'size-4',
                n <= rating
                    ? 'fill-amber-400 text-amber-500'
                    : 'text-[var(--site-line)]',
            ]"
        />
    </span>
</template>
