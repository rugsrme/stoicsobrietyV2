<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Download } from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { show as blogShow } from '@/routes/blog';
import { sample as bookSample } from '@/routes/books';
import type { Book, PostSummary } from '@/types';

const props = defineProps<{
    book: Book | null;
    recentPosts: PostSummary[];
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

const excerpts = computed(() => props.book?.excerpts ?? []);
const excerptIndex = ref(0);
let excerptTimer: ReturnType<typeof setInterval> | null = null;

function nextExcerpt() {
    if (excerpts.value.length === 0) return;
    excerptIndex.value = (excerptIndex.value + 1) % excerpts.value.length;
}

function prevExcerpt() {
    if (excerpts.value.length === 0) return;
    excerptIndex.value =
        (excerptIndex.value - 1 + excerpts.value.length) %
        excerpts.value.length;
}

onMounted(() => {
    if (excerpts.value.length > 1) {
        excerptTimer = setInterval(nextExcerpt, 6000);
    }
});

onBeforeUnmount(() => {
    if (excerptTimer) clearInterval(excerptTimer);
});
</script>

<template>
    <Head title="Stoic Recovery" />

    <PublicLayout>
        <!-- Hero -->
        <section class="mx-auto max-w-6xl px-6 pt-10 pb-24 lg:px-10 lg:pt-16">
            <div class="grid gap-16 lg:grid-cols-[1.3fr_1fr] lg:items-end">
                <div>
                    <p
                        class="mb-6 text-xs tracking-[0.3em] text-current/50 uppercase"
                    >
                        A new book from Stoic Recovery
                    </p>
                    <h1
                        class="font-serif-display text-5xl leading-[1.05] font-medium tracking-tight sm:text-6xl lg:text-7xl"
                    >
                        {{ book?.title ?? 'Architecture of Surrender' }}
                    </h1>
                    <p
                        v-if="book?.subtitle"
                        class="font-serif-display mt-5 max-w-xl text-xl text-current/70 italic"
                    >
                        {{ book.subtitle }}
                    </p>
                    <p
                        v-if="book?.description"
                        class="mt-8 max-w-xl text-base leading-relaxed text-current/80"
                    >
                        {{ book.description }}
                    </p>

                    <div
                        class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4"
                    >
                        <a
                            v-if="retailers[0]"
                            :href="retailers[0].url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center border border-current bg-current px-6 py-3 text-sm tracking-wide text-[#f6f1e9] uppercase transition-opacity hover:opacity-80 dark:text-[#15130f]"
                        >
                            Buy the book
                        </a>
                        <a
                            v-if="book?.sample_path"
                            :href="bookSample(book.slug).url"
                            class="inline-flex items-center gap-2 text-sm text-current/70 transition-colors hover:text-current"
                        >
                            <Download class="size-4" />
                            Download a sample
                        </a>
                    </div>
                </div>

                <!-- Book jacket -->
                <div
                    class="relative mx-auto aspect-[2/3] w-full max-w-[280px] border border-current/20 lg:mx-0 lg:ml-auto"
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
                            class="text-xs tracking-[0.3em] text-current/40 uppercase"
                            >Stoic Recovery</span
                        >
                        <span
                            class="font-serif-display mt-4 text-2xl leading-tight font-medium"
                        >
                            {{ book?.title ?? 'Architecture of Surrender' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Excerpts -->
        <section
            v-if="excerpts.length"
            class="mx-auto max-w-6xl border-t border-current/15 px-6 py-20 lg:px-10"
        >
            <p class="mb-10 text-xs tracking-[0.3em] text-current/50 uppercase">
                01 / From the book
            </p>

            <div
                class="grid gap-10 lg:grid-cols-[auto_1fr_auto] lg:items-center"
            >
                <button
                    type="button"
                    class="hidden text-current/40 transition-colors hover:text-current lg:block"
                    aria-label="Previous excerpt"
                    @click="prevExcerpt"
                >
                    <ArrowLeft class="size-6" />
                </button>

                <blockquote
                    class="font-serif-display max-w-3xl text-2xl leading-snug font-medium italic sm:text-3xl"
                >
                    &ldquo;{{ excerpts[excerptIndex] }}&rdquo;
                </blockquote>

                <button
                    type="button"
                    class="hidden text-current/40 transition-colors hover:text-current lg:block"
                    aria-label="Next excerpt"
                    @click="nextExcerpt"
                >
                    <ArrowRight class="size-6" />
                </button>
            </div>

            <div class="mt-8 flex gap-2 lg:hidden">
                <button
                    v-for="(_, i) in excerpts"
                    :key="i"
                    type="button"
                    class="h-px w-8 transition-colors"
                    :class="i === excerptIndex ? 'bg-current' : 'bg-current/20'"
                    :aria-label="`Go to excerpt ${i + 1}`"
                    @click="excerptIndex = i"
                />
            </div>
        </section>

        <!-- About the author -->
        <section
            v-if="book?.author_name"
            class="mx-auto max-w-6xl border-t border-current/15 px-6 py-20 lg:px-10"
        >
            <p class="mb-10 text-xs tracking-[0.3em] text-current/50 uppercase">
                02 / About the author
            </p>

            <div class="grid gap-10 lg:grid-cols-[auto_1fr] lg:items-start">
                <div
                    class="size-24 shrink-0 overflow-hidden rounded-full border border-current/20"
                >
                    <img
                        v-if="book.author_photo_url"
                        :src="book.author_photo_url"
                        :alt="book.author_name"
                        class="h-full w-full object-cover"
                    />
                    <div
                        v-else
                        class="flex h-full w-full items-center justify-center bg-current/5"
                    >
                        <span class="font-serif-display text-xl">{{
                            book.author_name.charAt(0)
                        }}</span>
                    </div>
                </div>

                <div class="max-w-2xl">
                    <h2 class="font-serif-display text-2xl font-medium">
                        {{ book.author_name }}
                    </h2>
                    <p
                        v-if="book.author_bio"
                        class="mt-4 leading-relaxed text-current/80"
                    >
                        {{ book.author_bio }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Where to buy -->
        <section
            v-if="retailers.length"
            class="mx-auto max-w-6xl border-t border-current/15 px-6 py-20 lg:px-10"
        >
            <p class="mb-10 text-xs tracking-[0.3em] text-current/50 uppercase">
                03 / Where to buy
            </p>

            <div class="flex flex-wrap items-baseline gap-x-10 gap-y-4">
                <a
                    v-for="retailer in retailers"
                    :key="retailer.key"
                    :href="retailer.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-serif-display border-b border-current/30 pb-1 text-xl transition-colors hover:border-current"
                >
                    {{ retailer.label }}
                </a>
            </div>
            <p
                v-if="book?.price_formatted"
                class="mt-6 text-sm text-current/50"
            >
                From {{ book.currency }} ${{ book.price_formatted }}, depending
                on retailer and format.
            </p>
        </section>

        <!-- Reviews -->
        <section
            class="mx-auto max-w-6xl border-t border-current/15 px-6 py-20 lg:px-10"
        >
            <p class="mb-10 text-xs tracking-[0.3em] text-current/50 uppercase">
                04 / Reviews
            </p>
            <p
                class="font-serif-display max-w-2xl text-xl text-current/60 italic"
            >
                Early reviews are on their way. Check back soon.
            </p>
        </section>

        <!-- Journal teaser -->
        <section
            v-if="recentPosts.length"
            class="mx-auto max-w-6xl border-t border-current/15 px-6 py-20 lg:px-10"
        >
            <p class="mb-10 text-xs tracking-[0.3em] text-current/50 uppercase">
                05 / From the journal
            </p>

            <div class="grid gap-10 sm:grid-cols-3">
                <Link
                    v-for="post in recentPosts"
                    :key="post.id"
                    :href="blogShow(post.slug)"
                    class="group"
                >
                    <h3
                        class="font-serif-display text-lg font-medium transition-colors group-hover:text-current/70"
                    >
                        {{ post.title }}
                    </h3>
                    <p v-if="post.excerpt" class="mt-2 text-sm text-current/60">
                        {{ post.excerpt }}
                    </p>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
