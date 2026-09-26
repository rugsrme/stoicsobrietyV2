<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, ExternalLink } from '@lucide/vue';
import { computed } from 'vue';
import StarRating from '@/components/StarRating.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatDate } from '@/lib/utils';
import { edit as adminEdit } from '@/routes/admin/posts';
import { index as reviewsIndex } from '@/routes/reviews';
import type { Post } from '@/types';

const props = defineProps<{
    section: 'book-review';
    post: Post;
}>();

const page = usePage();
const isAdmin = computed(() => Boolean(page.props.auth.user?.is_admin));
const links = computed(() => props.post.affiliate_links ?? []);
</script>

<template>
    <Head :title="post.title">
        <meta
            v-if="post.excerpt"
            head-key="description"
            name="description"
            :content="post.excerpt"
        />
    </Head>

    <PublicLayout>
        <article class="mx-auto max-w-3xl px-7 pt-10 pb-24 lg:pt-16">
            <div class="mb-8 flex items-center justify-between gap-4 text-sm">
                <Link
                    :href="reviewsIndex()"
                    class="text-[var(--site-ink-faint)] transition-colors hover:text-[var(--site-ink)]"
                >
                    &larr; All book reviews
                </Link>
                <Link
                    v-if="isAdmin"
                    :href="adminEdit(post.id)"
                    class="text-[var(--site-ink-faint)] transition-colors hover:text-[var(--site-ink)]"
                >
                    Edit review
                </Link>
            </div>

            <header
                class="grid grid-cols-1 items-start gap-8 sm:grid-cols-[200px_1fr] sm:gap-10"
            >
                <div
                    class="mx-auto aspect-[2/3] w-44 overflow-hidden rounded-md border border-[var(--site-line)] bg-[var(--site-bg-raised)] shadow-[0_18px_44px_var(--site-shadow)] sm:w-full"
                >
                    <img
                        v-if="post.cover_image_url"
                        :src="post.cover_image_url"
                        :alt="`Cover of ${post.reviewed_book_title ?? post.title}`"
                        class="size-full object-cover"
                    />
                    <div
                        v-else
                        class="flex size-full items-center justify-center"
                    >
                        <BookOpen
                            class="size-10 text-[var(--site-ink-faint)]"
                        />
                    </div>
                </div>

                <div>
                    <p
                        class="mb-3 text-xs tracking-[0.2em] text-[var(--site-ink-faint)] uppercase"
                    >
                        Book Review
                        <template v-if="post.published_at">
                            · {{ formatDate(post.published_at) }}</template
                        >
                    </p>
                    <h1
                        class="font-serif-display text-3xl leading-tight font-medium tracking-tight sm:text-4xl"
                    >
                        {{ post.title }}
                    </h1>
                    <p
                        v-if="post.reviewed_book_title"
                        class="mt-3 text-[var(--site-ink-soft)]"
                    >
                        <em>{{ post.reviewed_book_title }}</em>
                        <template v-if="post.reviewed_book_author">
                            by {{ post.reviewed_book_author }}</template
                        >
                    </p>
                    <StarRating
                        v-if="post.rating"
                        :rating="post.rating"
                        size="size-5"
                        class="mt-3"
                    />

                    <div v-if="links.length" class="mt-6 flex flex-wrap gap-3">
                        <a
                            v-for="(link, i) in links"
                            :key="i"
                            :href="link.url"
                            target="_blank"
                            rel="sponsored noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded px-5 py-2.5 text-sm font-semibold transition-transform hover:-translate-y-px"
                            :class="
                                i === 0
                                    ? 'bg-[var(--site-accent)] text-[var(--site-accent-ink)]'
                                    : 'border border-[var(--site-line)] bg-[var(--site-card)]'
                            "
                        >
                            {{ i === 0 ? `Buy on ${link.label}` : link.label }}
                            <ExternalLink class="size-3.5" />
                        </a>
                    </div>
                </div>
            </header>

            <!-- Body is sanitized server-side (App\Support\PostHtml). -->
            <div
                class="rich-content mt-12 text-[17px] text-[var(--site-ink-soft)]"
                v-html="post.body"
            />

            <footer
                class="mt-14 border-t border-[var(--site-line)] pt-6 text-sm text-[var(--site-ink-faint)]"
            >
                <p v-if="links.length">
                    Some links on this page are affiliate links: if you buy
                    through them, Sober Now We Live may earn a small commission
                    at no extra cost to you.
                    <Link
                        href="/affiliate-disclosure"
                        class="underline underline-offset-2 hover:text-[var(--site-ink)]"
                        >Affiliate disclosure</Link
                    >.
                </p>
                <p class="mt-2">
                    Reviewed by
                    {{ post.author.display_name || post.author.name }}
                </p>
            </footer>
        </article>
    </PublicLayout>
</template>
