<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, BookOpenCheck, Newspaper } from '@lucide/vue';
import StarRating from '@/components/StarRating.vue';
import { dashboard } from '@/routes';
import { chapter as chapterRoute, full as fullRoute } from '@/routes/library';
import { show as reflectionShow } from '@/routes/reflections';
import { show as reviewShow } from '@/routes/reviews';
import type { PostSummary } from '@/types';

defineProps<{
    latestReflection: PostSummary | null;
    latestReview: PostSummary | null;
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
            <p class="text-muted-foreground mt-1 max-w-xl">
                Read <em>What Was Never Yours</em> straight from here — the
                whole book on one page, or one chapter at a time.
            </p>
        </div>

        <Link
            v-if="latestReflection"
            :href="reflectionShow(latestReflection.slug)"
            class="group hover:bg-accent flex flex-col gap-2 rounded-xl border p-6 transition-colors"
        >
            <span
                class="text-muted-foreground inline-flex items-center gap-1.5 text-xs font-semibold tracking-wide uppercase"
            >
                <Newspaper class="size-3.5" />
                Latest reflection
            </span>
            <h2 class="text-xl font-semibold">{{ latestReflection.title }}</h2>
            <p
                v-if="latestReflection.summary"
                class="text-muted-foreground max-w-2xl text-sm"
            >
                {{ latestReflection.summary }}
            </p>
            <span
                class="text-foreground mt-2 inline-flex items-center gap-1 text-sm font-medium"
            >
                Read the reflection
                <ArrowRight
                    class="size-4 transition-transform group-hover:translate-x-0.5"
                />
            </span>
        </Link>

        <Link
            v-if="latestReview"
            :href="reviewShow(latestReview.slug)"
            class="group hover:bg-accent flex gap-5 rounded-xl border p-6 transition-colors"
        >
            <div
                class="bg-muted flex aspect-[2/3] w-20 shrink-0 items-center justify-center overflow-hidden rounded border"
            >
                <img
                    v-if="latestReview.cover_image_url"
                    :src="latestReview.cover_image_url"
                    :alt="`Cover of ${latestReview.reviewed_book_title ?? latestReview.title}`"
                    class="size-full object-cover"
                />
                <BookOpen v-else class="text-muted-foreground size-6" />
            </div>
            <div class="flex min-w-0 flex-col gap-2">
                <span
                    class="text-muted-foreground inline-flex items-center gap-1.5 text-xs font-semibold tracking-wide uppercase"
                >
                    <BookOpenCheck class="size-3.5" />
                    Latest book review
                </span>
                <h2 class="text-xl font-semibold">{{ latestReview.title }}</h2>
                <p
                    v-if="latestReview.reviewed_book_title"
                    class="text-muted-foreground text-sm"
                >
                    <em>{{ latestReview.reviewed_book_title }}</em>
                    <template v-if="latestReview.reviewed_book_author">
                        by {{ latestReview.reviewed_book_author }}</template
                    >
                </p>
                <StarRating
                    v-if="latestReview.rating"
                    :rating="latestReview.rating"
                />
                <p
                    v-if="latestReview.summary"
                    class="text-muted-foreground line-clamp-3 max-w-2xl text-sm"
                >
                    {{ latestReview.summary }}
                </p>
                <span
                    class="text-foreground mt-1 inline-flex items-center gap-1 text-sm font-medium"
                >
                    Read the review
                    <ArrowRight
                        class="size-4 transition-transform group-hover:translate-x-0.5"
                    />
                </span>
            </div>
        </Link>

        <div class="grid gap-4 sm:grid-cols-2">
            <Link
                :href="fullRoute()"
                class="group hover:bg-accent flex flex-col justify-between gap-4 rounded-xl border p-6 transition-colors"
            >
                <div>
                    <BookOpen class="text-muted-foreground size-6" />
                    <h2 class="mt-4 text-lg font-semibold">
                        Read the full book
                    </h2>
                    <p class="text-muted-foreground mt-1 text-sm">
                        Every chapter, one long page — good for scrolling
                        straight through.
                    </p>
                </div>
                <span
                    class="text-foreground inline-flex items-center gap-1 text-sm font-medium"
                >
                    Start reading
                    <ArrowRight
                        class="size-4 transition-transform group-hover:translate-x-0.5"
                    />
                </span>
            </Link>

            <Link
                :href="chapterRoute('chapter-1')"
                class="group hover:bg-accent flex flex-col justify-between gap-4 rounded-xl border p-6 transition-colors"
            >
                <div>
                    <BookOpen class="text-muted-foreground size-6" />
                    <h2 class="mt-4 text-lg font-semibold">Read by chapter</h2>
                    <p class="text-muted-foreground mt-1 text-sm">
                        One chapter per page, with the full table of contents in
                        the sidebar.
                    </p>
                </div>
                <span
                    class="text-foreground inline-flex items-center gap-1 text-sm font-medium"
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
