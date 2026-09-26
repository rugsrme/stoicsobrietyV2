<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Download } from '@lucide/vue';
import { computed } from 'vue';
import ExcerptTicker from '@/components/ExcerptTicker.vue';
import PostCard from '@/components/PostCard.vue';
import TestimonialCarousel from '@/components/TestimonialCarousel.vue';
import type { Testimonial } from '@/components/TestimonialCarousel.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { sample as bookSample, show as bookShow } from '@/routes/books';
import { index as reflectionsIndex } from '@/routes/reflections';
import { index as reviewsIndex } from '@/routes/reviews';
import { index as sampleIndex } from '@/routes/sample';
import type { Book, PostSummary } from '@/types';

const props = defineProps<{
    book: Book | null;
    posts: PostSummary[];
    readerReviews: Testimonial[];
}>();

const retailerLabels: Record<string, string> = {
    amazon: 'Amazon',
    barnes_noble: 'Barnes & Noble',
    bookshop: 'Bookshop.org',
    apple_books: 'Apple Books',
};

const retailers = computed(() => {
    const links = props.book?.retailer_links ?? {};

    return Object.entries(links)
        .filter(([, url]) => Boolean(url))
        .map(([key, url]) => ({
            key,
            label: retailerLabels[key] ?? key,
            url: url as string,
        }));
});
</script>

<template>
    <Head title="What Was Never Yours" />

    <PublicLayout>
        <!-- Hero -->
        <section class="hero-stars relative overflow-hidden py-[76px] pb-16">
            <div
                class="relative mx-auto grid max-w-4xl grid-cols-1 items-center gap-14 px-7 lg:grid-cols-[1.15fr_0.85fr]"
            >
                <div>
                    <p
                        class="mb-[18px] text-[13px] tracking-[0.2em] text-[var(--site-ink-faint)] uppercase"
                    >
                        Steps · Stoicism · Scripture
                    </p>
                    <ExcerptTicker
                        v-if="book?.excerpts?.length"
                        :excerpts="book.excerpts"
                        class="mb-[18px] max-w-[42ch]"
                    />
                    <h1
                        class="font-serif-display max-w-[15ch] text-[32px] leading-[1.18] font-medium sm:text-[40px] lg:text-[46px]"
                    >
                        {{ book?.title ?? 'What Was Never Yours' }}
                    </h1>
                    <p
                        v-if="book?.subtitle"
                        class="mt-3.5 max-w-[42ch] text-lg text-[var(--site-ink-soft)]"
                    >
                        {{ book.subtitle }}
                    </p>
                    <div class="mt-[30px] flex flex-wrap items-center gap-5">
                        <a
                            v-if="retailers[0]"
                            :href="retailers[0].url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-block rounded bg-[var(--site-accent)] px-[26px] py-[13px] text-[15.5px] font-semibold text-[var(--site-accent-ink)] transition-transform hover:-translate-y-px"
                        >
                            Get the Book
                        </a>
                        <Link
                            v-if="book?.slug"
                            :href="bookShow(book.slug).url"
                            class="text-[15px] font-semibold text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                        >
                            About the book &rarr;
                        </Link>
                    </div>
                </div>

                <div
                    class="relative z-[1] mx-auto aspect-[5/7.4] w-full max-w-[340px] overflow-hidden rounded-md border border-[var(--site-line)] bg-[var(--site-bg-raised)] shadow-[0_18px_44px_var(--site-shadow),0_0_60px_-12px_var(--site-glow)]"
                >
                    <img
                        v-if="book?.cover_url"
                        :src="book.cover_url"
                        :alt="book?.title"
                        class="h-full w-full object-cover"
                    />
                    <div
                        v-else
                        class="flex h-full w-full flex-col items-center justify-center px-6 text-center"
                    >
                        <span
                            class="text-xs tracking-[0.3em] text-[var(--site-ink-faint)] uppercase"
                            >Sober Now We Live</span
                        >
                        <span
                            class="font-serif-display mt-4 text-2xl leading-tight font-medium"
                        >
                            {{ book?.title ?? 'What Was Never Yours' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- What the site is -->
        <section
            class="border-y border-[var(--site-line)] bg-[var(--site-bg-raised)] py-10"
        >
            <div
                class="mx-auto grid max-w-4xl grid-cols-1 items-baseline gap-x-10 gap-y-3 px-7 sm:grid-cols-[auto_1fr]"
            >
                <span
                    class="text-xs tracking-[0.2em] whitespace-nowrap text-[var(--site-ink-soft)] uppercase"
                >
                    A more honest way forward
                </span>
                <p
                    class="font-serif-display max-w-[52ch] text-[19px] leading-normal"
                >
                    Reflections on recovery, and reviews of books that help,
                    written by someone who got sober at fifty and is still
                    working it out one day at a time.
                </p>
            </div>
        </section>

        <!-- Latest writing: posts ticked "Show on home page" in admin -->
        <section
            v-if="posts.length"
            class="border-b border-[var(--site-line)] py-17"
        >
            <div class="mx-auto max-w-4xl px-7">
                <div
                    class="mb-7 flex flex-wrap items-baseline justify-between gap-x-5 gap-y-2"
                >
                    <p
                        class="text-sm font-semibold text-[var(--site-ink-soft)]"
                    >
                        Latest writing
                    </p>
                    <div
                        class="flex gap-[18px] text-sm font-semibold text-[var(--site-ink-soft)]"
                    >
                        <Link
                            :href="reflectionsIndex()"
                            class="transition-colors hover:text-[var(--site-ink)]"
                            >All reflections &rarr;</Link
                        >
                        <Link
                            :href="reviewsIndex()"
                            class="transition-colors hover:text-[var(--site-ink)]"
                            >All book reviews &rarr;</Link
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <PostCard
                        v-for="post in posts"
                        :key="post.id"
                        :post="post"
                    />
                </div>
            </div>
        </section>

        <!-- Reader reviews: published + ticked "Show in home carousel" in admin -->
        <section
            v-if="readerReviews.length"
            class="border-b border-[var(--site-line)] py-17"
        >
            <div class="mx-auto max-w-4xl px-7">
                <p
                    class="mb-7 text-sm font-semibold text-[var(--site-ink-soft)]"
                >
                    What readers are saying
                </p>

                <TestimonialCarousel :testimonials="readerReviews" />
            </div>
        </section>

        <!-- Sample -->
        <section class="border-b border-[var(--site-line)] py-17">
            <div class="mx-auto max-w-4xl px-7">
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
                    <div class="flex flex-col items-start gap-3">
                        <Link
                            :href="sampleIndex()"
                            class="inline-flex items-center gap-2 rounded bg-[var(--site-accent)] px-[22px] py-3 text-[15px] font-semibold whitespace-nowrap text-[var(--site-accent-ink)] transition-transform hover:-translate-y-px"
                        >
                            <BookOpen class="size-4" />
                            Start reading
                        </Link>
                        <a
                            v-if="book?.sample_path"
                            :href="bookSample(book.slug).url"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                        >
                            <Download class="size-4" />
                            Or download the sample PDF
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.hero-stars {
    background-image:
        radial-gradient(
            ellipse 640px 420px at 18% 8%,
            var(--site-glow),
            transparent 70%
        ),
        radial-gradient(1.6px 1.6px at 12% 22%, var(--site-star), transparent),
        radial-gradient(1.4px 1.4px at 27% 12%, var(--site-star), transparent),
        radial-gradient(1.8px 1.8px at 41% 30%, var(--site-star), transparent),
        radial-gradient(1.2px 1.2px at 58% 9%, var(--site-star), transparent),
        radial-gradient(1.6px 1.6px at 71% 24%, var(--site-star), transparent),
        radial-gradient(1.3px 1.3px at 85% 14%, var(--site-star), transparent),
        radial-gradient(1.5px 1.5px at 92% 34%, var(--site-star), transparent),
        radial-gradient(1.2px 1.2px at 6% 42%, var(--site-star), transparent),
        radial-gradient(1.4px 1.4px at 64% 40%, var(--site-star), transparent);
    background-repeat: no-repeat;
}
</style>
