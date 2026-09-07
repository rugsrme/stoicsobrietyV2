<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index } from '@/routes/admin/posts';
import type { Post } from '@/types';

defineProps<{
    post: Post;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Posts', href: index() },
            { title: 'Edit post', href: index() },
        ],
    },
});
</script>

<template>
    <Head title="Edit post" />

    <div class="max-w-2xl">
        <Heading title="Edit post" :description="post.title" />

        <Form
            v-bind="PostController.update.form(post.id)"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    :default-value="post.title"
                    required
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="slug">Slug</Label>
                <Input
                    id="slug"
                    name="slug"
                    :default-value="post.slug"
                    required
                />
                <InputError :message="errors.slug" />
            </div>

            <div class="grid gap-2">
                <Label for="excerpt">Excerpt</Label>
                <Textarea
                    id="excerpt"
                    name="excerpt"
                    :default-value="post.excerpt ?? ''"
                />
                <InputError :message="errors.excerpt" />
            </div>

            <div class="grid gap-2">
                <Label for="body">Body</Label>
                <Textarea
                    id="body"
                    name="body"
                    class="min-h-64"
                    :default-value="post.body"
                    required
                />
                <InputError :message="errors.body" />
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="published"
                    type="checkbox"
                    name="published"
                    value="1"
                    class="size-4"
                    :checked="!!post.published_at"
                />
                <Label for="published">Published</Label>
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="processing"
                    >Save changes</Button
                >
            </div>
        </Form>
    </div>
</template>
