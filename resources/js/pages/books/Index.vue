<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { show as bookShow } from '@/routes/books';
import type { Book } from '@/types';

defineProps<{
    books: Book[];
}>();
</script>

<template>
    <Head title="Books" />

    <PublicLayout>
        <section class="mx-auto max-w-6xl px-6 pt-10 pb-24 lg:px-10 lg:pt-16">
            <p class="mb-6 text-xs tracking-[0.3em] text-current/50 uppercase">
                The catalog
            </p>
            <h1
                class="font-serif-display text-4xl font-medium tracking-tight sm:text-5xl"
            >
                Books
            </h1>

            <div class="mt-16 grid gap-16 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="book in books"
                    :key="book.id"
                    :href="bookShow(book.slug)"
                    class="group"
                >
                    <div
                        class="relative aspect-[2/3] w-full border border-current/20"
                    >
                        <img
                            v-if="book.cover_url"
                            :src="book.cover_url"
                            :alt="book.title"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full flex-col items-center justify-center px-4 text-center"
                        >
                            <span
                                class="font-serif-display text-lg font-medium"
                                >{{ book.title }}</span
                            >
                        </div>
                    </div>
                    <h2
                        class="font-serif-display mt-4 text-xl font-medium transition-colors group-hover:text-current/70"
                    >
                        {{ book.title }}
                    </h2>
                    <p
                        v-if="book.subtitle"
                        class="mt-1 text-sm text-current/60 italic"
                    >
                        {{ book.subtitle }}
                    </p>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
