<script setup lang="ts">
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { create, edit, index } from '@/routes/admin/posts';
import { show as blogShow } from '@/routes/blog';
import type { Post } from '@/types';

defineProps<{
    posts: Pick<Post, 'id' | 'title' | 'slug' | 'published_at'>[];
}>();

const page = usePage();
const isAdmin = computed(() => Boolean(page.props.auth.user?.is_admin));

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Posts', href: index() }],
    },
});

function isPublished(publishedAt: string | null): boolean {
    return publishedAt !== null && new Date(publishedAt) <= new Date();
}

function openPost(post: Pick<Post, 'slug' | 'published_at'>) {
    if (isPublished(post.published_at)) {
        router.visit(blogShow(post.slug));
    }
}
</script>

<template>
    <Head title="Posts" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Posts"
                description="Write and publish journal entries"
            />
            <Button v-if="isAdmin" as-child>
                <Link :href="create()">New post</Link>
            </Button>
        </div>

        <div
            v-if="posts.length"
            class="border-border divide-border divide-y rounded-lg border"
        >
            <div
                v-for="post in posts"
                :key="post.id"
                class="flex items-center justify-between gap-4 px-4 py-3 transition-colors"
                :class="
                    isPublished(post.published_at)
                        ? 'cursor-pointer hover:bg-accent'
                        : ''
                "
                @click="openPost(post)"
            >
                <div>
                    <p class="font-medium">{{ post.title }}</p>
                    <p class="text-muted-foreground text-sm">
                        {{ post.published_at ? 'Published' : 'Draft' }}
                    </p>
                </div>
                <div v-if="isAdmin" class="flex items-center gap-2" @click.stop>
                    <Button as-child variant="secondary" size="sm">
                        <Link :href="edit(post.id)">Edit</Link>
                    </Button>
                    <Form
                        v-bind="PostController.destroy.form(post.id)"
                        :options="{ preserveScroll: true }"
                    >
                        <Button type="submit" variant="destructive" size="sm"
                            >Delete</Button
                        >
                    </Form>
                </div>
            </div>
        </div>

        <p v-else class="text-muted-foreground text-sm">No posts yet.</p>
    </div>
</template>
