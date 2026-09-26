<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, ChevronDown, Download } from '@lucide/vue';
import { computed, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { sample as bookSample } from '@/routes/books';
import { index as sampleIndex } from '@/routes/sample';
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

// Show about a paragraph's worth of the bio up front (the opening lines can
// be very short); the rest sits behind "Read more".
const authorExpanded = ref(false);

const authorLead = computed(() => {
    let length = 0;
    const count = authorParagraphs.value.findIndex(
        (p) => (length += p.length) >= 200,
    );

    return count === -1 ? authorParagraphs.value.length : count + 1;
});

const descriptionParagraphs = computed(() =>
    (props.book.description ?? '').split('\n\n').filter(Boolean),
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
                class="grid grid-cols-1 items-start gap-8 lg:grid-cols-[1.3fr_1fr]"
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
                    class="relative mx-auto aspect-[5/7.4] w-full max-w-[340px] overflow-hidden rounded-md border border-[var(--site-line)] bg-[var(--site-bg-raised)] shadow-[0_18px_44px_var(--site-shadow)] lg:mx-0 lg:ml-auto"
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
                class="mt-16 grid grid-cols-1 items-start gap-6 border-t border-[var(--site-line)] pt-14 lg:grid-cols-2"
            >
                <div class="lg:pl-6">
                    <p
                        v-for="(paragraph, i) in descriptionParagraphs"
                        :key="i"
                        class="mt-3 text-[15px] leading-relaxed text-[var(--site-ink-soft)] first:mt-0"
                    >
                        {{ paragraph }}
                    </p>
                </div>

                <div
                    class="relative mx-auto aspect-[5/7.4] w-full max-w-[340px] overflow-hidden rounded-md border border-[var(--site-line)] bg-[var(--site-bg-raised)] shadow-[0_18px_44px_var(--site-shadow)] lg:mx-0 lg:ml-auto"
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

            <div class="mt-16 border-t border-[var(--site-line)] pt-14">
                <p
                    class="mb-7 text-sm font-semibold text-[var(--site-ink-soft)]"
                >
                    Read Before You Buy
                </p>
                <div
                    class="flex flex-wrap items-center justify-between gap-8 rounded border border-[var(--site-line)] bg-[var(--site-card)] px-9 py-[34px]"
                >
                    <div>
                        <h3
                            class="font-serif-display mb-2 text-[22px] font-medium"
                        >
                            Read the first three chapters free
                        </h3>
                        <p class="max-w-[46ch] text-[var(--site-ink-soft)]">
                            The opening of the book and Chapters 1–3, right here
                            in your browser. No account needed.
                        </p>
                    </div>
                    <Link
                        :href="sampleIndex()"
                        class="inline-flex items-center gap-2 rounded bg-[var(--site-accent)] px-[22px] py-3 text-[15px] font-semibold whitespace-nowrap text-[var(--site-accent-ink)] transition-transform hover:-translate-y-px"
                    >
                        <BookOpen class="size-4" />
                        Start reading
                    </Link>
                </div>
            </div>

            <div
                v-if="book.author_name"
                id="about"
                class="mt-16 scroll-mt-24 border-t border-[var(--site-line)] pt-14"
            >
                <p
                    class="mb-7 text-sm font-semibold text-[var(--site-ink-soft)]"
                >
                    About the Author
                </p>

                <div
                    class="grid grid-cols-1 items-start gap-11 sm:grid-cols-[200px_1fr]"
                >
                    <div
                        class="aspect-square overflow-hidden rounded border border-[var(--site-line)] bg-[var(--site-bg-raised)] sm:w-[200px]"
                    >
                        <img
                            v-if="book.author_photo_url"
                            :src="book.author_photo_url"
                            :alt="book.author_name"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <span class="font-serif-display text-xl">{{
                                book.author_name.charAt(0)
                            }}</span>
                        </div>
                    </div>

                    <div class="max-w-[660px]">
                        <h2
                            class="font-serif-display mb-4 text-2xl font-medium"
                        >
                            {{ book.author_name }}
                        </h2>
                        <p
                            v-for="(paragraph, i) in authorParagraphs.slice(
                                0,
                                authorLead,
                            )"
                            :key="i"
                            class="mb-[18px]"
                            :class="
                                i === 0
                                    ? 'text-lg text-[var(--site-ink)]'
                                    : 'text-[var(--site-ink-soft)]'
                            "
                        >
                            {{ paragraph }}
                        </p>

                        <div
                            v-if="authorParagraphs.length > authorLead"
                            id="author-bio-more"
                            class="grid transition-[grid-template-rows] duration-300 ease-out"
                            :class="
                                authorExpanded
                                    ? 'grid-rows-[1fr]'
                                    : 'grid-rows-[0fr]'
                            "
                            :inert="!authorExpanded"
                        >
                            <div class="overflow-hidden">
                                <p
                                    v-for="(
                                        paragraph, i
                                    ) in authorParagraphs.slice(authorLead)"
                                    :key="i"
                                    class="mb-[18px] text-[var(--site-ink-soft)]"
                                >
                                    {{ paragraph }}
                                </p>
                            </div>
                        </div>

                        <button
                            v-if="authorParagraphs.length > authorLead"
                            type="button"
                            class="inline-flex items-center gap-1.5 text-[15px] font-semibold text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                            aria-controls="author-bio-more"
                            :aria-expanded="authorExpanded"
                            @click="authorExpanded = !authorExpanded"
                        >
                            {{ authorExpanded ? 'Show less' : 'Read more…' }}
                            <ChevronDown
                                class="size-4 transition-transform"
                                :class="authorExpanded ? 'rotate-180' : ''"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
