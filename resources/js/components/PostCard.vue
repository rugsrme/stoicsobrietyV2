<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import StarRating from '@/components/StarRating.vue';
import { postUrl } from '@/lib/posts';
import { formatDate } from '@/lib/utils';
import type { PostSummary } from '@/types';

defineProps<{
    post: PostSummary;
}>();
</script>

<template>
    <Link
        :href="postUrl(post)"
        class="group flex flex-col overflow-hidden rounded border border-[var(--site-line)] bg-[var(--site-card)] transition-[transform,box-shadow] hover:-translate-y-0.5 hover:shadow-[0_10px_28px_var(--site-shadow)]"
    >
        <div
            class="relative aspect-video overflow-hidden bg-[var(--site-bg-raised)]"
        >
            <img
                v-if="post.cover_image_url"
                :src="post.cover_image_url"
                alt=""
                class="h-full w-full object-cover"
                loading="lazy"
            />
            <!-- No cover: a quiet horizon in the site's accent colour -->
            <svg
                v-else
                class="absolute inset-0 h-full w-full text-[var(--site-accent)]"
                viewBox="0 0 320 180"
                preserveAspectRatio="xMidYMid slice"
                aria-hidden="true"
            >
                <rect
                    width="320"
                    height="180"
                    fill="currentColor"
                    opacity="0.08"
                />
                <circle
                    :cx="post.category === 'book-review' ? 90 : 236"
                    cy="70"
                    r="30"
                    fill="currentColor"
                    opacity="0.3"
                />
                <path
                    d="M0 130 Q80 100 160 122 T320 112 V180 H0Z"
                    fill="currentColor"
                    opacity="0.16"
                />
                <path
                    d="M0 150 Q100 128 200 146 T320 140 V180 H0Z"
                    fill="currentColor"
                    opacity="0.22"
                />
            </svg>
        </div>

        <div class="flex flex-1 flex-col gap-2.5 px-[22px] pt-5 pb-[22px]">
            <span
                class="self-start rounded-[3px] border px-2 py-[3px] text-[11px] font-semibold tracking-[0.14em] uppercase"
                :class="
                    post.category === 'book-review'
                        ? 'border-[var(--site-accent)]/35 text-[var(--site-accent)]'
                        : 'border-[var(--site-line)] text-[var(--site-ink-soft)]'
                "
            >
                {{
                    post.category === 'book-review'
                        ? 'Book Review'
                        : 'Reflection'
                }}
            </span>

            <h3
                class="font-serif-display text-xl leading-snug font-medium text-balance"
            >
                {{ post.title }}
            </h3>

            <div
                v-if="
                    post.category === 'book-review' && post.reviewed_book_title
                "
                class="-mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[13.5px] text-[var(--site-ink-soft)]"
            >
                <span
                    >{{ post.reviewed_book_title
                    }}<template v-if="post.reviewed_book_author">
                        &middot; {{ post.reviewed_book_author }}</template
                    ></span
                >
                <StarRating
                    v-if="post.rating"
                    :rating="post.rating"
                    size="size-3.5"
                />
            </div>

            <p
                v-if="post.summary"
                class="line-clamp-4 text-[14.5px] leading-normal text-[var(--site-ink-soft)]"
            >
                {{ post.summary }}
            </p>

            <div
                class="mt-auto flex justify-between pt-1.5 text-[13px] text-[var(--site-ink-faint)]"
            >
                <span v-if="post.published_at">{{
                    formatDate(post.published_at)
                }}</span>
                <span
                    class="ml-auto transition-colors group-hover:text-[var(--site-ink)]"
                    >Read &rarr;</span
                >
            </div>
        </div>
    </Link>
</template>
