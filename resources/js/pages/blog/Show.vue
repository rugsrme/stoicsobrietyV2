<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Post } from '@/types';

const props = defineProps<{
    post: Post;
}>();
</script>

<template>
    <Head :title="post.title" />

    <PublicLayout>
        <article class="mx-auto max-w-3xl px-6 pt-10 pb-24 lg:px-10 lg:pt-16">
            <p
                v-if="post.published_at"
                class="mb-6 text-xs tracking-[0.3em] text-current/50 uppercase"
            >
                {{
                    new Date(post.published_at).toLocaleDateString(undefined, {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    })
                }}
            </p>
            <h1
                class="font-serif-display text-4xl font-medium tracking-tight sm:text-5xl"
            >
                {{ post.title }}
            </h1>
            <p class="mt-4 text-sm text-current/50">
                By
                {{ props.post.author.display_name || props.post.author.name }}
            </p>

            <div
                class="mt-12 leading-relaxed whitespace-pre-line text-current/80"
            >
                {{ post.body }}
            </div>
        </article>
    </PublicLayout>
</template>
