<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
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
</script>

<template>
    <Head :title="book.title" />

    <PublicLayout>
        <section class="mx-auto max-w-6xl px-6 pt-10 pb-24 lg:px-10 lg:pt-16">
            <div class="grid gap-16 lg:grid-cols-[1.3fr_1fr] lg:items-start">
                <div>
                    <h1
                        class="font-serif-display text-4xl leading-[1.05] font-medium tracking-tight sm:text-5xl"
                    >
                        {{ book.title }}
                    </h1>
                    <p
                        v-if="book.subtitle"
                        class="font-serif-display mt-4 max-w-xl text-lg text-current/70 italic"
                    >
                        {{ book.subtitle }}
                    </p>
                    <p
                        v-if="book.description"
                        class="mt-8 max-w-xl leading-relaxed text-current/80"
                    >
                        {{ book.description }}
                    </p>

                    <div
                        class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4"
                    >
                        <a
                            v-for="retailer in retailers"
                            :key="retailer.key"
                            :href="retailer.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-serif-display border-b border-current/30 pb-1 text-lg transition-colors hover:border-current"
                        >
                            {{ retailer.label }}
                        </a>
                    </div>

                    <a
                        v-if="book.sample_path"
                        :href="bookSample(book.slug).url"
                        class="mt-8 inline-flex items-center gap-2 text-sm text-current/70 transition-colors hover:text-current"
                    >
                        <Download class="size-4" />
                        Download a sample
                    </a>
                </div>

                <div
                    class="relative mx-auto aspect-[2/3] w-full max-w-[280px] border border-current/20 lg:mx-0 lg:ml-auto"
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

            <div
                v-if="book.excerpts?.length"
                class="mt-20 border-t border-current/15 pt-16"
            >
                <p
                    class="mb-10 text-xs tracking-[0.3em] text-current/50 uppercase"
                >
                    From the book
                </p>
                <blockquote
                    v-for="(excerpt, i) in book.excerpts"
                    :key="i"
                    class="font-serif-display mb-8 max-w-3xl text-2xl leading-snug font-medium italic"
                >
                    &ldquo;{{ excerpt }}&rdquo;
                </blockquote>
            </div>

            <div
                v-if="book.author_name"
                class="mt-20 border-t border-current/15 pt-16"
            >
                <p
                    class="mb-10 text-xs tracking-[0.3em] text-current/50 uppercase"
                >
                    About the author
                </p>
                <h2 class="font-serif-display text-2xl font-medium">
                    {{ book.author_name }}
                </h2>
                <p
                    v-if="book.author_bio"
                    class="mt-4 max-w-2xl leading-relaxed text-current/80"
                >
                    {{ book.author_bio }}
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
