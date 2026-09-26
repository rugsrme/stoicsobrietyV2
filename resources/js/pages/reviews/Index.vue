<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen } from '@lucide/vue';
import { computed } from 'vue';
import PublicPagination from '@/components/PublicPagination.vue';
import StarRating from '@/components/StarRating.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { create as adminCreate, index as adminIndex } from '@/routes/admin/posts';
import { show as reviewShow } from '@/routes/reviews';
import type { Paginated, PostSummary } from '@/types';

defineProps<{
    section: 'book-review';
    posts: Paginated<PostSummary>;
}>();

const page = usePage();
const isAdmin = computed(() => Boolean(page.props.auth.user?.is_admin));
</script>

<template>
    <Head title="Book Reviews" />

    <PublicLayout>
        <section class="mx-auto max-w-4xl px-7 pt-12 pb-24 lg:pt-16">
            <div class="mb-4 flex items-center justify-between gap-4">
                <p class="text-sm font-semibold text-[var(--site-ink-soft)]">
                    Book Reviews
                </p>
                <div v-if="isAdmin" class="flex items-center gap-4 text-sm">
                    <Link
                        :href="adminIndex({ query: { category: section } })"
                        class="text-[var(--site-ink-faint)] transition-colors hover:text-[var(--site-ink)]"
                    >
                        Manage
                    </Link>
                    <Link
                        :href="adminCreate({ query: { category: section } })"
                        class="font-semibold text-[var(--site-ink)] transition-colors hover:text-[var(--site-accent)]"
                    >
                        + New review
                    </Link>
                </div>
            </div>
            <h1
                class="font-serif-display text-4xl font-medium tracking-tight sm:text-5xl"
            >
                Books worth your time
            </h1>
            <p class="mt-4 max-w-[56ch] text-[var(--site-ink-soft)]">
                Honest reviews of books on recovery, Stoicism, faith, and the
                work in between.
            </p>

            <div v-if="posts.data.length" class="mt-14 space-y-10">
                <article
                    v-for="post in posts.data"
                    :key="post.id"
                    class="border-t border-[var(--site-line)] pt-10"
                >
                    <Link
                        :href="reviewShow(post.slug)"
                        class="group grid grid-cols-[96px_1fr] gap-6 sm:grid-cols-[140px_1fr] sm:gap-8"
                    >
                        <div
                            class="aspect-[2/3] overflow-hidden rounded border border-[var(--site-line)] bg-[var(--site-bg-raised)] shadow-[0_8px_24px_var(--site-shadow)]"
                        >
                            <img
                                v-if="post.cover_image_url"
                                :src="post.cover_image_url"
                                :alt="`Cover of ${post.reviewed_book_title ?? post.title}`"
                                class="size-full object-cover"
                                loading="lazy"
                            />
                            <div
                                v-else
                                class="flex size-full items-center justify-center"
                            >
                                <BookOpen
                                    class="size-7 text-[var(--site-ink-faint)]"
                                />
                            </div>
                        </div>

                        <div class="min-w-0">
                            <p
                                v-if="post.reviewed_book_title"
                                class="text-sm text-[var(--site-ink-faint)]"
                            >
                                <em>{{ post.reviewed_book_title }}</em>
                                <template v-if="post.reviewed_book_author">
                                    by {{ post.reviewed_book_author }}</template
                                >
                            </p>
                            <h2
                                class="font-serif-display mt-1 text-2xl font-medium transition-colors group-hover:text-[var(--site-accent)]"
                            >
                                {{ post.title }}
                            </h2>
                            <StarRating
                                v-if="post.rating"
                                :rating="post.rating"
                                class="mt-2"
                            />
                            <p
                                v-if="post.summary"
                                class="mt-3 line-clamp-4 max-w-2xl text-[var(--site-ink-soft)]"
                            >
                                {{ post.summary }}
                            </p>
                            <span
                                class="mt-4 inline-block text-sm font-semibold text-[var(--site-ink)]"
                                >Read the review &rarr;</span
                            >
                        </div>
                    </Link>
                </article>
            </div>

            <p v-else class="mt-16 text-[var(--site-ink-faint)] italic">
                The first reviews are on their way. Check back soon.
            </p>

            <PublicPagination :links="posts.links" />
        </section>
    </PublicLayout>
</template>
