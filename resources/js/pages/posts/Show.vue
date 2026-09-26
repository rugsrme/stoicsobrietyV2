<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Lock } from '@lucide/vue';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { postIndexUrl } from '@/lib/posts';
import { formatDate } from '@/lib/utils';
import { edit as adminEdit } from '@/routes/admin/posts';
import type { Post, PostCategory } from '@/types';

const props = defineProps<{
    section: Extract<PostCategory, 'reflection' | 'journal'>;
    post: Post;
}>();

const page = usePage();
const isAdmin = computed(() => Boolean(page.props.auth.user?.is_admin));
const isJournal = computed(() => props.section === 'journal');
</script>

<template>
    <Head :title="post.title">
        <meta
            v-if="post.excerpt && !isJournal"
            head-key="description"
            name="description"
            :content="post.excerpt"
        />
    </Head>

    <PublicLayout>
        <article class="mx-auto max-w-3xl px-7 pt-10 pb-24 lg:pt-16">
            <div class="mb-8 flex items-center justify-between gap-4 text-sm">
                <Link
                    :href="postIndexUrl(section)"
                    class="text-[var(--site-ink-faint)] transition-colors hover:text-[var(--site-ink)]"
                >
                    &larr;
                    {{ isJournal ? 'Back to the journal' : 'All reflections' }}
                </Link>
                <Link
                    v-if="isAdmin"
                    :href="adminEdit(post.id)"
                    class="text-[var(--site-ink-faint)] transition-colors hover:text-[var(--site-ink)]"
                >
                    Edit
                </Link>
            </div>

            <p
                class="mb-5 inline-flex items-center gap-1.5 text-xs tracking-[0.2em] text-[var(--site-ink-faint)] uppercase"
            >
                <template v-if="isJournal">
                    <Lock class="size-3" /> Private journal ·
                </template>
                <template v-if="post.published_at">{{
                    formatDate(post.published_at)
                }}</template>
            </p>
            <h1
                class="font-serif-display text-4xl leading-tight font-medium tracking-tight sm:text-5xl"
            >
                {{ post.title }}
            </h1>
            <p class="mt-4 text-sm text-[var(--site-ink-faint)]">
                By {{ post.author.display_name || post.author.name }}
            </p>

            <img
                v-if="post.cover_image_url"
                :src="post.cover_image_url"
                alt=""
                class="mt-10 w-full rounded border border-[var(--site-line)]"
            />

            <!-- Body is sanitized server-side (App\Support\PostHtml). -->
            <div
                class="rich-content mt-10 text-[17px] text-[var(--site-ink-soft)]"
                v-html="post.body"
            />
        </article>
    </PublicLayout>
</template>
