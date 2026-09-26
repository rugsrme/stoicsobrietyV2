<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, BookOpen } from '@lucide/vue';
import BookBlocks from '@/components/BookBlocks.vue';
import { chapter as chapterRoute, full as fullRoute } from '@/routes/library';
import type { LibraryChapter, LibraryChapterSummary } from '@/types';

defineProps<{
    chapter: LibraryChapter;
    prev: LibraryChapterSummary | null;
    next: LibraryChapterSummary | null;
    chapters: LibraryChapterSummary[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Library', href: fullRoute() }],
    },
});
</script>

<template>
    <Head :title="chapter.title" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:flex-row md:p-6">
        <aside class="shrink-0 md:w-56">
            <Link
                :href="fullRoute()"
                class="text-muted-foreground hover:text-foreground mb-4 inline-flex items-center gap-2 text-sm transition-colors"
            >
                <BookOpen class="size-4" />
                Read full book
            </Link>

            <nav class="flex flex-col gap-1 border-l pl-3">
                <Link
                    v-for="c in chapters"
                    :key="c.slug"
                    :href="chapterRoute(c.slug)"
                    class="rounded px-2 py-1 text-sm transition-colors"
                    :class="
                        c.slug === chapter.slug
                            ? 'bg-accent text-accent-foreground font-medium'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    {{ c.number ? `${c.number}. ` : '' }}{{ c.title }}
                </Link>
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            <p
                v-if="chapter.number"
                class="text-muted-foreground mb-2 text-sm font-semibold"
            >
                Chapter {{ chapter.number }}
            </p>
            <h1 class="text-3xl font-semibold tracking-tight">
                {{ chapter.title }}
            </h1>

            <BookBlocks :blocks="chapter.blocks" class="mt-8" />

            <div
                class="mt-12 flex items-center justify-between gap-4 border-t pt-6"
            >
                <Link
                    v-if="prev"
                    :href="chapterRoute(prev.slug)"
                    class="text-muted-foreground hover:text-foreground inline-flex items-center gap-2 text-sm transition-colors"
                >
                    <ArrowLeft class="size-4" />
                    <span>{{ prev.title }}</span>
                </Link>
                <span v-else />

                <Link
                    v-if="next"
                    :href="chapterRoute(next.slug)"
                    class="text-muted-foreground hover:text-foreground inline-flex items-center gap-2 text-right text-sm transition-colors"
                >
                    <span>{{ next.title }}</span>
                    <ArrowRight class="size-4" />
                </Link>
            </div>
        </div>
    </div>
</template>
