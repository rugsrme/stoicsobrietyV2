<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { sample as bookSample } from '@/routes/books';
import type { Book } from '@/types';

const props = defineProps<{
    book: Book;
}>();

const retailerLabels: Record<string, string> = {
    amazon: 'Amazon',
    barnes_noble: 'Barnes & Noble',
    bookshop: 'Bookshop.org',
    apple_books: 'Apple Books',
};

const retailers = computed(() => {
    const links = props.book.retailer_links ?? {};

    return Object.entries(links)
        .filter(([, url]) => Boolean(url))
        .map(([key, url]) => ({
            key,
            label: retailerLabels[key] ?? key,
            url: url as string,
        }));
});

const authorParagraphs = computed(() =>
    (props.book.author_bio ?? '').split('\n\n').filter(Boolean),
);
</script>

<template>
    <Head :title="book.title" />

    <PublicLayout>
        <section class="mx-auto max-w-4xl px-7 pt-10 pb-16">
            <Link
                href="/"
                class="mb-8 inline-flex items-center text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
            >
                &larr; Back to home
            </Link>

            <div
                class="grid grid-cols-1 items-start gap-14 lg:grid-cols-[1.3fr_1fr]"
            >
                <div>
                    <h1
                        class="font-serif-display text-4xl leading-[1.05] font-medium tracking-tight sm:text-5xl"
                    >
                        {{ book.title }}
                    </h1>
                    <p
                        v-if="book.subtitle"
                        class="mt-4 max-w-xl text-lg text-[var(--site-ink-soft)]"
                    >
                        {{ book.subtitle }}
                    </p>
                    <p
                        v-if="book.description"
                        class="mt-8 max-w-xl leading-relaxed text-[var(--site-ink-soft)]"
                    >
                        {{ book.description }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3.5">
                        <a
                            v-for="retailer in retailers"
                            :key="retailer.key"
                            :href="retailer.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center rounded border border-[var(--site-line)] bg-[var(--site-card)] px-5 py-3 text-[15px] font-semibold transition-transform hover:-translate-y-px"
                        >
                            {{ retailer.label }}
                        </a>
                    </div>

                    <a
                        v-if="book.sample_path"
                        :href="bookSample(book.slug).url"
                        class="mt-6 inline-flex items-center gap-2 text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                    >
                        <Download class="size-4" />
                        Download a sample
                    </a>
                </div>

                <div
                    class="relative mx-auto aspect-[5/7.4] w-full max-w-[280px] overflow-hidden rounded-md border border-[var(--site-line)] bg-[var(--site-bg-raised)] shadow-[0_18px_44px_var(--site-shadow)] lg:mx-0 lg:ml-auto"
                >
                    <img
                        v-if="book.cover_url"
                        :src="book.cover_url"
                        :alt="book.title"
                        class="h-full w-full object-cover"
                    />
                    <div
                        v-else
                        class="flex h-full w-full items-center justify-center px-6 text-center"
                    >
                        <span class="font-serif-display text-2xl font-medium">{{
                            book.title
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Back cover -->
            <div
                v-if="book.back_cover_url"
                class="mt-14 flex flex-col items-center border-t border-[var(--site-line)] pt-14"
            >
                <p
                    class="mb-5 text-sm font-semibold text-[var(--site-ink-soft)]"
                >
                    Back cover
                </p>
                <div
                    class="aspect-[5/7.4] w-full max-w-[280px] overflow-hidden rounded-md border border-[var(--site-line)] bg-[var(--site-bg-raised)] shadow-[0_18px_44px_var(--site-shadow)]"
                >
                    <img
                        :src="book.back_cover_url"
                        :alt="`${book.title} — back cover`"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>

            <div
                v-if="book.excerpts?.length"
                class="mt-16 border-t border-[var(--site-line)] pt-14"
            >
                <p
                    class="mb-7 text-sm font-semibold text-[var(--site-ink-soft)]"
                >
                    From the book
                </p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="(excerpt, i) in book.excerpts"
                        :key="i"
                        class="rounded border border-[var(--site-line)] bg-[var(--site-card)] px-6 py-6"
                    >
                        <p class="font-serif-display text-lg leading-normal">
                            {{ excerpt.quote }}
                        </p>
                        <p
                            class="mt-4 text-[13px] text-[var(--site-ink-faint)]"
                        >
                            {{ excerpt.source }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                v-if="book.author_name"
                class="mt-16 border-t border-[var(--site-line)] pt-14"
            >
                <p
                    class="mb-7 text-sm font-semibold text-[var(--site-ink-soft)]"
                >
                    About the author
                </p>
                <h2 class="font-serif-display text-2xl font-medium">
                    {{ book.author_name }}
                </h2>
                <p
                    v-for="(paragraph, i) in authorParagraphs"
                    :key="i"
                    class="mt-4 max-w-2xl leading-relaxed text-[var(--site-ink-soft)]"
                >
                    {{ paragraph }}
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
