<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Lock } from '@lucide/vue';
import { computed } from 'vue';
import BookBlocks from '@/components/BookBlocks.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { register } from '@/routes';
import { chapter as libraryChapter } from '@/routes/library';
import { chapter as sampleChapter } from '@/routes/sample';
import type { Book, LibraryChapter, SampleChapterSummary } from '@/types';

const props = defineProps<{
    chapter: LibraryChapter;
    prev: SampleChapterSummary | null;
    next: SampleChapterSummary | null;
    chapters: SampleChapterSummary[];
    book: Pick<Book, 'id' | 'title' | 'slug' | 'retailer_links'> | null;
}>();

function chapterHref(c: SampleChapterSummary) {
    return c.public ? sampleChapter(c.slug) : libraryChapter(c.slug);
}

function label(c: SampleChapterSummary) {
    return c.number ? `${c.number}. ${c.title}` : c.title;
}

const buyUrl = computed(
    () => Object.values(props.book?.retailer_links ?? {}).find(Boolean) ?? null,
);

/** The last free chapter — end it with an invitation to keep reading. */
const sampleEndsHere = computed(
    () => props.next !== null && !props.next.public,
);
</script>

<template>
    <Head :title="`${chapter.title} — Free Sample`" />

    <PublicLayout>
        <div
            class="mx-auto flex max-w-5xl flex-col gap-10 px-7 pt-10 pb-24 md:flex-row lg:pt-14"
        >
            <aside class="shrink-0 md:w-60">
                <p
                    class="mb-1 text-sm font-semibold text-[var(--site-ink-soft)]"
                >
                    Free sample
                </p>
                <p class="mb-5 text-xs text-[var(--site-ink-faint)]">
                    The opening and first three chapters of
                    <em>What Was Never Yours</em>.
                </p>
                <nav
                    class="flex flex-col gap-0.5 border-l border-[var(--site-line)] pl-3"
                    aria-label="Chapters"
                >
                    <Link
                        v-for="c in chapters"
                        :key="c.slug"
                        :href="chapterHref(c)"
                        class="flex items-center justify-between gap-2 rounded px-2 py-1 text-sm transition-colors"
                        :class="[
                            c.slug === chapter.slug
                                ? 'bg-[var(--site-bg-raised)] font-medium text-[var(--site-ink)]'
                                : 'text-[var(--site-ink-soft)] hover:text-[var(--site-ink)]',
                            !c.public && 'text-[var(--site-ink-faint)]',
                        ]"
                        :title="c.public ? undefined : 'Subscribers only'"
                    >
                        <span>{{ label(c) }}</span>
                        <Lock v-if="!c.public" class="size-3 shrink-0" />
                    </Link>
                </nav>
            </aside>

            <article class="min-w-0 flex-1">
                <p
                    v-if="chapter.number"
                    class="mb-2 text-sm font-semibold text-[var(--site-ink-faint)]"
                >
                    Chapter {{ chapter.number }}
                </p>
                <h1
                    class="font-serif-display text-3xl leading-tight font-medium tracking-tight sm:text-4xl"
                >
                    {{ chapter.title }}
                </h1>

                <BookBlocks
                    :blocks="chapter.blocks"
                    class="sample-text mt-8 text-[17px]"
                />

                <section
                    v-if="sampleEndsHere"
                    class="mt-14 rounded border border-[var(--site-line)] bg-[var(--site-card)] px-8 py-8"
                >
                    <h2 class="font-serif-display text-2xl font-medium">
                        That's the end of the free sample
                    </h2>
                    <p class="mt-2 max-w-[52ch] text-[var(--site-ink-soft)]">
                        Keep reading with a free subscriber account, or get your
                        own copy of the book.
                    </p>
                    <div class="mt-6 flex flex-wrap items-center gap-4">
                        <Link
                            :href="register()"
                            class="inline-block rounded bg-[var(--site-accent)] px-6 py-3 text-[15px] font-semibold text-[var(--site-accent-ink)] transition-transform hover:-translate-y-px"
                        >
                            Create a free account
                        </Link>
                        <a
                            v-if="buyUrl"
                            :href="buyUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-block rounded border border-[var(--site-ink)] px-6 py-3 text-[15px] font-semibold transition-colors hover:bg-[var(--site-ink)] hover:text-[var(--site-bg)]"
                        >
                            Get the book
                        </a>
                    </div>
                </section>

                <div
                    class="mt-12 flex items-center justify-between gap-4 border-t border-[var(--site-line)] pt-6 text-sm"
                >
                    <Link
                        v-if="prev"
                        :href="chapterHref(prev)"
                        class="inline-flex items-center gap-2 text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                    >
                        <ArrowLeft class="size-4" />
                        <span>{{ prev.title }}</span>
                    </Link>
                    <span v-else />

                    <Link
                        v-if="next"
                        :href="chapterHref(next)"
                        class="inline-flex items-center gap-2 text-right text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                    >
                        <span>{{ next.title }}</span>
                        <Lock v-if="!next.public" class="size-3.5" />
                        <ArrowRight v-else class="size-4" />
                    </Link>
                </div>
            </article>
        </div>
    </PublicLayout>
</template>

<style scoped>
/* BookBlocks uses the app's theme tokens; map them to the public palette. */
.sample-text {
    --muted-foreground: var(--site-ink-soft);
    --foreground: var(--site-ink);
    color: var(--site-ink);
}
</style>
