<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import BookBlocks from '@/components/BookBlocks.vue';
import { chapter as chapterRoute, full as fullRoute } from '@/routes/library';
import type { LibraryChapter } from '@/types';

const props = defineProps<{
    chapters: LibraryChapter[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Library', href: fullRoute() }],
    },
});
</script>

<template>
    <Head title="The Architecture of Surrender" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:flex-row md:p-6">
        <aside class="shrink-0 md:w-56">
            <p class="mb-4 text-sm font-semibold text-muted-foreground">
                Contents
            </p>
            <nav class="flex flex-col gap-1 border-l pl-3">
                <a
                    v-for="c in chapters"
                    :key="c.slug"
                    :href="`#${c.slug}`"
                    class="rounded px-2 py-1 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    {{ c.number ? `${c.number}. ` : '' }}{{ c.title }}
                </a>
            </nav>
            <p class="mt-4 text-xs text-muted-foreground">
                Prefer reading one chapter at a time?
                <Link
                    :href="chapterRoute(props.chapters[0].slug)"
                    class="underline underline-offset-2 hover:text-foreground"
                >
                    Switch to chapter view
                </Link>
            </p>
        </aside>

        <div class="min-w-0 flex-1">
            <h1 class="text-3xl font-semibold tracking-tight">
                The Architecture of Surrender
            </h1>
            <p class="mt-2 text-muted-foreground">
                How a Rabbi, an Emperor, and a Roomful of Drunks Found the
                Same Way Out
            </p>

            <article
                v-for="c in chapters"
                :id="c.slug"
                :key="c.slug"
                class="mt-14 scroll-mt-20 border-t pt-10 first:mt-10 first:border-t-0 first:pt-0"
            >
                <p
                    v-if="c.number"
                    class="mb-2 text-sm font-semibold text-muted-foreground"
                >
                    Chapter {{ c.number }}
                </p>
                <h2 class="text-2xl font-semibold tracking-tight">
                    {{ c.title }}
                </h2>

                <BookBlocks :blocks="c.blocks" class="mt-6" />
            </article>
        </div>
    </div>
</template>
