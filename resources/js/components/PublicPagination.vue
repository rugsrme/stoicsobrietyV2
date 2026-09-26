<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Paginated } from '@/types';

defineProps<{
    links: Paginated<unknown>['links'];
}>();
</script>

<template>
    <nav
        v-if="links.length > 3"
        class="mt-16 flex flex-wrap gap-4 text-sm"
        aria-label="Pagination"
    >
        <Link
            v-for="(link, i) in links"
            :key="i"
            :href="link.url ?? '#'"
            class="border-b pb-0.5 transition-colors"
            :class="[
                link.active
                    ? 'border-current'
                    : 'border-transparent text-[var(--site-ink-faint)] hover:text-[var(--site-ink)]',
                !link.url && 'pointer-events-none opacity-30',
            ]"
            preserve-scroll
            v-html="link.label"
        />
    </nav>
</template>
