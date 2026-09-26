<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ExternalLink } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import PostForm from '@/components/PostForm.vue';
import { postNoun, postUrl } from '@/lib/posts';
import { index } from '@/routes/admin/posts';
import type { Post, PostCategoryOption } from '@/types';

defineProps<{
    post: Post;
    categories: PostCategoryOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Writing', href: index() },
            { title: 'Edit', href: index() },
        ],
    },
});
</script>

<template>
    <Head :title="`Edit: ${post.title}`" />

    <div class="flex items-start justify-between gap-4">
        <Heading
            :title="`Edit ${postNoun(post.category)}`"
            :description="post.title"
        />
        <Link
            v-if="post.published_at"
            :href="postUrl(post)"
            class="text-muted-foreground hover:text-foreground inline-flex shrink-0 items-center gap-1.5 text-sm"
        >
            {{ post.category === 'journal' ? 'View' : 'View live' }}
            <ExternalLink class="size-3.5" />
        </Link>
    </div>

    <PostForm :key="post.id" :post="post" :categories="categories" />
</template>
