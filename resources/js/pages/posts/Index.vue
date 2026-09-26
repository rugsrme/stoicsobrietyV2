<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Lock } from '@lucide/vue';
import { computed } from 'vue';
import PublicPagination from '@/components/PublicPagination.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { postUrl } from '@/lib/posts';
import { formatDate } from '@/lib/utils';
import type { Paginated, PostCategory, PostSummary } from '@/types';

const props = defineProps<{
    section: Extract<PostCategory, 'reflection' | 'journal'>;
    posts: Paginated<PostSummary>;
}>();

const isJournal = computed(() => props.section === 'journal');
</script>

<template>
    <Head :title="isJournal ? 'Journal' : 'Reflections'" />

    <PublicLayout>
        <section class="mx-auto max-w-4xl px-7 pt-12 pb-24 lg:pt-16">
            <p
                class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--site-ink-soft)]"
            >
                <Lock v-if="isJournal" class="size-3.5" />
                {{ isJournal ? 'Journal · Private' : 'Reflections' }}
            </p>
            <h1
                class="font-serif-display text-4xl font-medium tracking-tight sm:text-5xl"
            >
                {{
                    isJournal
                        ? 'Journal'
                        : 'Reflections on Stoicism, faith & recovery'
                }}
            </h1>
            <p
                v-if="isJournal"
                class="mt-4 max-w-[56ch] text-[var(--site-ink-soft)]"
            >
                Only admins can see this section. Entries here never appear on
                the public site.
            </p>

            <div v-if="posts.data.length" class="mt-14 space-y-12">
                <article
                    v-for="post in posts.data"
                    :key="post.id"
                    class="border-t border-[var(--site-line)] pt-10"
                >
                    <Link
                        :href="postUrl(post)"
                        class="group grid gap-6"
                        :class="
                            post.cover_image_url
                                ? 'sm:grid-cols-[1fr_220px] sm:items-start'
                                : ''
                        "
                    >
                        <div>
                            <p
                                v-if="post.published_at"
                                class="mb-3 text-xs tracking-wide text-[var(--site-ink-faint)] uppercase"
                            >
                                {{ formatDate(post.published_at) }}
                            </p>
                            <h2
                                class="font-serif-display text-2xl font-medium transition-colors group-hover:text-[var(--site-accent)]"
                            >
                                {{ post.title }}
                            </h2>
                            <p
                                v-if="post.summary"
                                class="mt-3 max-w-2xl text-[var(--site-ink-soft)]"
                            >
                                {{ post.summary }}
                            </p>
                            <span
                                class="mt-4 inline-block text-sm font-semibold text-[var(--site-ink)]"
                                >Read more &rarr;</span
                            >
                        </div>
                        <img
                            v-if="post.cover_image_url"
                            :src="post.cover_image_url"
                            alt=""
                            class="order-first aspect-video w-full rounded border border-[var(--site-line)] object-cover sm:order-none"
                            loading="lazy"
                        />
                    </Link>
                </article>
            </div>

            <p v-else class="mt-16 text-[var(--site-ink-faint)] italic">
                {{
                    isJournal
                        ? 'No journal entries yet.'
                        : 'Nothing published yet. Check back soon.'
                }}
            </p>

            <PublicPagination :links="posts.links" />
        </section>
    </PublicLayout>
</template>
