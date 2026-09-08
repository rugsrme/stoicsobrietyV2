<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, Newspaper } from '@lucide/vue';
import { dashboard } from '@/routes';
import { show as blogShow } from '@/routes/blog';
import { chapter as chapterRoute, full as fullRoute } from '@/routes/library';
import type { Post } from '@/types';

defineProps<{
    latestPost: Post | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Subscribers Area',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Subscribers Area" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Subscribers Area
            </h1>
            <p class="mt-1 max-w-xl text-muted-foreground">
                Read <em>The Architecture of Surrender</em> straight from
                here — the whole book on one page, or one chapter at a time.
            </p>
        </div>

        <Link
            v-if="latestPost"
            :href="blogShow(latestPost.slug)"
            class="group flex flex-col gap-2 rounded-xl border p-6 transition-colors hover:bg-accent"
        >
            <span
                class="inline-flex items-center gap-1.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
            >
                <Newspaper class="size-3.5" />
                Latest from the journal
            </span>
            <h2 class="text-xl font-semibold">{{ latestPost.title }}</h2>
            <p
                v-if="latestPost.excerpt"
                class="max-w-2xl text-sm text-muted-foreground"
            >
                {{ latestPost.excerpt }}
            </p>
            <span
                class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-foreground"
            >
                Read the post
                <ArrowRight
                    class="size-4 transition-transform group-hover:translate-x-0.5"
                />
            </span>
        </Link>

        <div class="grid gap-4 sm:grid-cols-2">
            <Link
                :href="fullRoute()"
                class="group flex flex-col justify-between gap-4 rounded-xl border p-6 transition-colors hover:bg-accent"
            >
                <div>
                    <BookOpen class="size-6 text-muted-foreground" />
                    <h2 class="mt-4 text-lg font-semibold">Read the full book</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Every chapter, one long page — good for scrolling
                        straight through.
                    </p>
                </div>
                <span
                    class="inline-flex items-center gap-1 text-sm font-medium text-foreground"
                >
                    Start reading
                    <ArrowRight
                        class="size-4 transition-transform group-hover:translate-x-0.5"
                    />
                </span>
            </Link>

            <Link
                :href="chapterRoute('chapter-1')"
                class="group flex flex-col justify-between gap-4 rounded-xl border p-6 transition-colors hover:bg-accent"
            >
                <div>
                    <BookOpen class="size-6 text-muted-foreground" />
                    <h2 class="mt-4 text-lg font-semibold">Read by chapter</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        One chapter per page, with the full table of
                        contents in the sidebar.
                    </p>
                </div>
                <span
                    class="inline-flex items-center gap-1 text-sm font-medium text-foreground"
                >
                    Start with Chapter 1
                    <ArrowRight
                        class="size-4 transition-transform group-hover:translate-x-0.5"
                    />
                </span>
            </Link>
        </div>
    </div>
</template>
