<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { show as blogShow } from '@/routes/blog';
import type { PostSummary } from '@/types';

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

defineProps<{
    posts: Paginated<PostSummary>;
}>();
</script>

<template>
    <Head title="Journal" />

    <PublicLayout>
        <section class="mx-auto max-w-6xl px-6 pt-10 pb-24 lg:px-10 lg:pt-16">
            <p class="mb-6 text-xs tracking-[0.3em] text-current/50 uppercase">
                Journal
            </p>
            <h1
                class="font-serif-display text-4xl font-medium tracking-tight sm:text-5xl"
            >
                Notes on Stoicism &amp; recovery
            </h1>

            <div v-if="posts.data.length" class="mt-16 space-y-14">
                <article
                    v-for="post in posts.data"
                    :key="post.id"
                    class="border-t border-current/15 pt-8"
                >
                    <Link :href="blogShow(post.slug)" class="group">
                        <h2
                            class="font-serif-display text-2xl font-medium transition-colors group-hover:text-current/70"
                        >
                            {{ post.title }}
                        </h2>
                    </Link>
                    <p
                        v-if="post.excerpt"
                        class="mt-3 max-w-2xl text-current/70"
                    >
                        {{ post.excerpt }}
                    </p>
                    <p
                        v-if="post.published_at"
                        class="mt-3 text-xs tracking-wide text-current/40 uppercase"
                    >
                        {{
                            new Date(post.published_at).toLocaleDateString(
                                undefined,
                                {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                },
                            )
                        }}
                    </p>
                </article>
            </div>

            <p v-else class="mt-16 text-current/60 italic">
                Nothing published yet. Check back soon.
            </p>

            <nav
                v-if="posts.links.length > 3"
                class="mt-16 flex flex-wrap gap-4 text-sm"
            >
                <Link
                    v-for="(link, i) in posts.links"
                    :key="i"
                    :href="link.url ?? '#'"
                    class="border-b pb-0.5 transition-colors"
                    :class="[
                        link.active
                            ? 'border-current'
                            : 'border-transparent text-current/50 hover:text-current',
                        !link.url && 'pointer-events-none opacity-30',
                    ]"
                    v-html="link.label"
                />
            </nav>
        </section>
    </PublicLayout>
</template>
